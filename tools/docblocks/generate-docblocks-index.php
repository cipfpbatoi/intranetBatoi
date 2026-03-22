<?php

declare(strict_types=1);

/**
 * Genera un index Markdown de doc-blocks de classes i metodes en el codi de l'aplicacio.
 */
final class DocblockIndexGenerator
{
    private array $roots;
    private string $outputPath;
    private string $basePath;

    /**
     * @param array<int, string> $roots
     */
    public function __construct(array $roots, string $outputPath, string $basePath)
    {
        $this->roots = array_values(array_filter(array_map('realpath', $roots)));
        $this->outputPath = $outputPath;
        $this->basePath = rtrim($basePath, '/');
    }

    public function generate(): void
    {
        $rows = [];

        foreach ($this->roots as $root) {
            if (!is_dir($root)) {
                continue;
            }

            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
            foreach ($it as $file) {
                /** @var SplFileInfo $file */
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $parsed = $this->parsePhpFile((string) $file->getPathname());
                if ($parsed !== []) {
                    $parsed['category'] = $this->categoryForFile($parsed['file']);
                    $rows[] = $parsed;
                }
            }
        }

        usort(
            $rows,
            function (array $a, array $b): int {
                $categoryOrder = array_flip($this->orderedCategories());
                $categoryCompare = ($categoryOrder[$a['category']] ?? PHP_INT_MAX)
                    <=> ($categoryOrder[$b['category']] ?? PHP_INT_MAX);

                if ($categoryCompare !== 0) {
                    return $categoryCompare;
                }

                return strcmp($a['file'], $b['file']);
            }
        );

        $markdown = $this->renderMarkdown($rows);
        file_put_contents($this->outputPath, $markdown);
    }

    /**
     * @return array<string, mixed>
     */
    private function parsePhpFile(string $path): array
    {
        $source = file_get_contents($path);
        if ($source === false) {
            return [];
        }

        $tokens = token_get_all($source);

        $namespace = '';
        $lastDocblock = null;
        $classes = [];
        $currentClass = null;

        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (!is_array($token)) {
                continue;
            }

            $type = $token[0];
            $text = $token[1];

            if ($type === T_DOC_COMMENT) {
                $lastDocblock = $text;
                continue;
            }

            if ($type === T_NAMESPACE) {
                $namespace = $this->readNamespace($tokens, $i + 1);
                continue;
            }

            if (in_array($type, [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], true)) {
                $className = $this->readNextStringToken($tokens, $i + 1);
                if ($className === null) {
                    continue;
                }

                $fqcn = $namespace !== '' ? $namespace.'\\'.$className : $className;
                $summary = $this->docblockSummary($lastDocblock);
                $classes[$fqcn] = [
                    'summary' => $summary,
                    'methods' => [],
                ];
                $currentClass = $fqcn;
                $lastDocblock = null;
                continue;
            }

            if ($type === T_FUNCTION && $currentClass !== null) {
                $methodName = $this->readNextStringToken($tokens, $i + 1);
                if ($methodName === null) {
                    continue;
                }

                $classes[$currentClass]['methods'][] = [
                    'name' => $methodName,
                    'parameters' => $this->readFunctionParameters($tokens, $i + 1),
                    'return' => $this->readFunctionReturnType($tokens, $i + 1)
                        ?: $this->docblockReturnType($lastDocblock),
                    'summary' => $this->docblockSummary($lastDocblock),
                ];
                $lastDocblock = null;
                continue;
            }

            if ($type !== T_WHITESPACE && $type !== T_COMMENT) {
                if ($type !== T_PUBLIC && $type !== T_PROTECTED && $type !== T_PRIVATE && $type !== T_STATIC) {
                    $lastDocblock = null;
                }
            }
        }

        if ($classes === []) {
            return [];
        }

        return [
            'file' => $this->relativePath($path),
            'classes' => $classes,
        ];
    }

    private function relativePath(string $path): string
    {
        $full = realpath($path) ?: $path;
        if (str_starts_with($full, $this->basePath.'/')) {
            return substr($full, strlen($this->basePath) + 1);
        }

        return $full;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function readNamespace(array $tokens, int $index): string
    {
        $parts = [];
        $count = count($tokens);

        for ($i = $index; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token) && ($token === ';' || $token === '{')) {
                break;
            }
            if (is_array($token) && in_array($token[0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                $parts[] = $token[1];
            }
        }

        return implode('', $parts);
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function readNextStringToken(array $tokens, int $index): ?string
    {
        $count = count($tokens);
        for ($i = $index; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token) && $token === '(') {
                return null;
            }
            if (is_array($token) && $token[0] === T_STRING) {
                return $token[1];
            }
        }

        return null;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function readFunctionParameters(array $tokens, int $index): string
    {
        $count = count($tokens);
        $start = null;

        for ($i = $index; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token) && $token === '(') {
                $start = $i;
                break;
            }
        }

        if ($start === null) {
            return '';
        }

        $depth = 0;
        $raw = '';

        for ($i = $start; $i < $count; $i++) {
            $token = $tokens[$i];
            $text = is_array($token) ? $token[1] : $token;

            if ($text === '(') {
                $depth++;
                if ($depth === 1) {
                    continue;
                }
            } elseif ($text === ')') {
                $depth--;
                if ($depth === 0) {
                    break;
                }
            }

            if ($depth >= 1) {
                $raw .= $text;
            }
        }

        $normalized = trim((string) preg_replace('/\s+/', ' ', $raw));
        $normalized = (string) preg_replace('/\s*,\s*/', ', ', $normalized);
        return $normalized;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function readFunctionReturnType(array $tokens, int $index): string
    {
        $count = count($tokens);
        $start = null;

        for ($i = $index; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token) && $token === '(') {
                $start = $i;
                break;
            }
        }

        if ($start === null) {
            return '';
        }

        $depth = 0;
        $closeIndex = null;
        for ($i = $start; $i < $count; $i++) {
            $token = $tokens[$i];
            $text = is_array($token) ? $token[1] : $token;

            if ($text === '(') {
                $depth++;
            } elseif ($text === ')') {
                $depth--;
                if ($depth === 0) {
                    $closeIndex = $i;
                    break;
                }
            }
        }

        if ($closeIndex === null) {
            return '';
        }

        $colonIndex = null;
        for ($i = $closeIndex + 1; $i < $count; $i++) {
            $token = $tokens[$i];
            $text = is_array($token) ? $token[1] : $token;

            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            if ($text === ':') {
                $colonIndex = $i;
            }
            break;
        }

        if ($colonIndex === null) {
            return '';
        }

        $raw = '';
        for ($i = $colonIndex + 1; $i < $count; $i++) {
            $token = $tokens[$i];
            $text = is_array($token) ? $token[1] : $token;

            if ($text === '{' || $text === ';' || $text === '=') {
                break;
            }

            $raw .= $text;
        }

        $normalized = trim((string) preg_replace('/\s+/', '', $raw));
        return $normalized;
    }

    private function docblockReturnType(?string $docblock): string
    {
        if ($docblock === null) {
            return '';
        }

        if (preg_match('/@return\s+([^\s\*]+)/', $docblock, $matches) === 1) {
            return trim($matches[1]);
        }

        return '';
    }

    private function docblockSummary(?string $docblock): string
    {
        if ($docblock === null) {
            return 'Sense doc-block';
        }

        $lines = preg_split('/\R/', $docblock) ?: [];
        foreach ($lines as $line) {
            $clean = trim((string) preg_replace('/^\s*\/\*\*?|\*\/\s*$|^\s*\*/', '', $line));
            if ($clean === '' || str_starts_with($clean, '@')) {
                continue;
            }
            return $clean;
        }

        return 'Sense resum en doc-block';
    }

    /**
     * Classifica fitxers de l'aplicacio per seccions funcionals de documentacio.
     */
    private function categoryForFile(string $relativeFile): string
    {
        if (str_starts_with($relativeFile, 'app/Http/Controllers/')) {
            return 'Controladors';
        }

        if (
            str_starts_with($relativeFile, 'app/Entities/')
            || str_starts_with($relativeFile, 'app/Models/')
        ) {
            return 'Models';
        }

        if (
            str_starts_with($relativeFile, 'app/Application/')
            || str_starts_with($relativeFile, 'app/Services/')
            || str_starts_with($relativeFile, 'app/Domain/')
        ) {
            return 'Serveis';
        }

        if (str_starts_with($relativeFile, 'app/Http/Requests/')) {
            return 'Requests';
        }

        if (str_starts_with($relativeFile, 'app/Policies/')) {
            return 'Policies';
        }

        if (str_starts_with($relativeFile, 'app/Events/')) {
            return 'Events';
        }

        if (str_starts_with($relativeFile, 'app/Listeners/')) {
            return 'Listeners';
        }

        if (str_starts_with($relativeFile, 'app/Jobs/')) {
            return 'Jobs';
        }

        if (str_starts_with($relativeFile, 'app/Console/Commands/')) {
            return 'Comandes';
        }

        return 'Altres';
    }

    /**
     * @return array<int, string>
     */
    private function orderedCategories(): array
    {
        return [
            'Controladors',
            'Models',
            'Serveis',
            'Requests',
            'Policies',
            'Events',
            'Listeners',
            'Jobs',
            'Comandes',
            'Altres',
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function renderMarkdown(array $rows): string
    {
        $out = [];
        $out[] = '# Index Doc-Blocks de l aplicacio';
        $out[] = '';
        $out[] = 'Fitxer generat automaticament des dels doc-blocks de `app/`.';
        $out[] = '';

        $rowsByCategory = [];
        foreach ($rows as $row) {
            $rowsByCategory[$row['category']][] = $row;
        }

        foreach ($this->orderedCategories() as $category) {
            if (!isset($rowsByCategory[$category])) {
                continue;
            }

            $out[] = '## '.$category;
            $out[] = '';

            foreach ($rowsByCategory[$category] as $row) {
                $out[] = '### `'.$row['file'].'`';
                $out[] = '';

                foreach ($row['classes'] as $fqcn => $classData) {
                    $out[] = '#### `'.$fqcn.'`';
                    if ($classData['summary'] !== 'Sense doc-block') {
                        $out[] = $classData['summary'];
                        $out[] = '';
                    }

                    if ($classData['methods'] === []) {
                        $out[] = '- Metodes: cap';
                        $out[] = '';
                        continue;
                    }

                    $out[] = '- Metodes:';
                    foreach ($classData['methods'] as $method) {
                        $signature = '**`'.$method['name'].'`**(' . ($method['parameters'] ?? '') . ')';
                        if (($method['return'] ?? '') !== '') {
                            $signature .= ': '.$method['return'];
                        }
                        $out[] = '  - '.$signature;
                        if ($method['summary'] !== 'Sense doc-block') {
                            $out[] = '';
                            $out[] = '    '.$method['summary'];
                        }
                    }
                    $out[] = '';
                }
                $out[] = '';
            }
            $out[] = '';
        }

        return implode(PHP_EOL, $out).PHP_EOL;
    }
}

$generator = new DocblockIndexGenerator(
    [
        __DIR__.'/../../app',
    ],
    __DIR__.'/../../docs/app-docblocks-index.md',
    dirname(__DIR__, 2)
);

$generator->generate();

echo "Index doc-block generat en docs/app-docblocks-index.md".PHP_EOL;
