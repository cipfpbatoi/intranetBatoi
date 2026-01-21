<?php

namespace Tests\Unit\Services;

use FilesystemIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionProperty;
use Tests\TestCase;

class ServicesDocumentationTest extends TestCase
{
    #[DataProvider('serviceClassProvider')]
    public function testServicesHaveDocblocksAndTypedProperties(string $className): void
    {
        $this->assertTrue(class_exists($className), "No s'ha pogut carregar la classe $className");

        $reflection = new ReflectionClass($className);

        $this->assertNotFalse(
            $reflection->getDocComment(),
            "Falta el DocBlock de classe a $className"
        );

        foreach ($reflection->getProperties(
            ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE
        ) as $property) {
            if ($property->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            $this->assertTrue(
                $property->getType() !== null || $property->getDocComment(),
                sprintf('Cal documentar o tipar la propietat %s::$%s', $className, $property->getName())
            );
        }
    }

    public static function serviceClassProvider(): array
    {
        $basePath = dirname(__DIR__, 3) . '/app/Services';
        $directoryIterator = new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator);
        $classes = [];

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $relativeClass = str_replace(DIRECTORY_SEPARATOR, '\\', substr($relativePath, 0, -4));
            $classes[] = ['Intranet\\Services\\' . $relativeClass];
        }

        sort($classes);

        return $classes;
    }
}
