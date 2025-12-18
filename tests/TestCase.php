<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Executa un mètode protegit o privat mitjançant reflexió.
     */
    protected function callProtectedMethod(object $object, string $method, array $parameters = [])
    {
        $reflection = new ReflectionClass($object);
        $methodReflection = $reflection->getMethod($method);
        $methodReflection->setAccessible(true);

        return $methodReflection->invokeArgs($object, $parameters);
    }

    /**
     * Obté el valor d'una propietat protegida o privada.
     */
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionClass($object);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }
}
