<?php

namespace Tests;



use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use ReflectionMethod;
use ReflectionProperty;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    protected function callProtectedMethod($object, $methodName, array $args = [])
    {
        $reflection = new ReflectionMethod($object, $methodName);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $args);
    }

    // Afegir funció per accedir a propietats protegides o privades
    protected function getProtectedProperty($object, $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }

}
