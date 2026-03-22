<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use ReflectionMethod;

/**
 * Utilitats compartides per a parseig i validació de camps XML.
 */
class ImportXmlHelperService
{
    public function extractField(mixed $attributes, mixed $key, int $func, object $context): mixed
    {
        $parts = explode(',', (string) $key, 99);
        if (count($parts) === 1) {
            if (isset($attributes[$key])) {
                return mb_convert_encoding((string) $attributes[$key], 'utf8');
            }

            return $key;
        }

        $params = [];
        for ($i = $func; $i < count($parts); $i++) {
            $params[$i - $func] = mb_convert_encoding((string) $attributes[$parts[$i]], 'utf8');
        }

        if ($func) {
            return $this->invokeContextMethod($context, $parts[0], $params);
        }

        return $params;
    }

    /**
     * @param array<int, string> $filter
     */
    public function passesFilter(array $filter, mixed $fields): bool
    {
        $element = $fields[$filter[0]];
        $op = $filter[1];
        $value = $filter[2];
        $condition = "return('$element' $op '$value');";

        return eval($condition) ? true : false;
    }

    /**
     * @param array<int, string> $required
     */
    public function findMissingRequired(array $required, mixed $fields, bool $strictSpaceCheck = false): ?string
    {
        $missing = null;
        foreach ($required as $key) {
            $isMissing = $strictSpaceCheck ? ($fields[$key] === ' ') : ($fields[$key] == ' ');
            if ($isMissing) {
                $missing = $key;
            }
        }

        return $missing;
    }

    /**
     * @param array<int, mixed> $params
     */
    private function invokeContextMethod(object $context, string $method, array $params): mixed
    {
        $reflection = new ReflectionMethod($context, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($context, $params);
    }
}
