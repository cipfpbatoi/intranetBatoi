<?php

namespace Intranet\Http\Controllers\Docs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intranet\Http\Controllers\Controller;

/**
 * Renderitza la documentacio de doc-blocks de l'aplicacio.
 */
class DocblockDocsController extends Controller
{
    /**
     * Mostra la pagina de documentacio amb index lateral de seccions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->renderMarkdownDocument(
            'docs/app-docblocks-index.md',
            'No s\'ha trobat el fitxer de documentacio de doc-blocks.'
        );
    }

    /**
     * Mostra l'esquema de la BBDD en format HTML.
     *
     * @return \Illuminate\Http\Response
     */
    public function schema()
    {
        return $this->renderMarkdownDocument(
            'docs/bbdd-esquema.md',
            'No s\'ha trobat el fitxer d\'esquema de la BBDD.'
        );
    }

    /**
     * Renderitza un fitxer markdown en la vista comuna de documentacio.
     *
     * @param string $relativePath
     * @param string $notFoundMessage
     * @return \Illuminate\Http\Response
     */
    private function renderMarkdownDocument(string $relativePath, string $notFoundMessage)
    {
        $path = base_path($relativePath);

        if (!File::exists($path)) {
            abort(404, $notFoundMessage);
        }

        $markdown = File::get($path);
        $content = Str::markdown($markdown);
        $sections = [];
        $sectionIndex = 0;

        $content = preg_replace_callback(
            '/<h2>(.*?)<\/h2>/i',
            function (array $matches) use (&$sections, &$sectionIndex): string {
                $title = trim(strip_tags($matches[1]));
                $id = 'section-'.(++$sectionIndex).'-'.Str::slug($title);
                $sections[] = [
                    'id' => $id,
                    'title' => $title,
                ];

                return '<h2 id="'.e($id).'">'.$matches[1].'</h2>';
            },
            $content
        ) ?? $content;

        return response()
            ->view('docs.app-docblocks', [
                'content' => $content,
                'sections' => $sections,
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
