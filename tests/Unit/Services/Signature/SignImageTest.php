<?php

namespace Tests\Unit\Services\Signature;

use Intranet\Services\Signature\SignImage;
use ReflectionClass;
use Tests\TestCase;

class SignImageTest extends TestCase
{
    public function testBreakTextSplitsLongStrings()
    {
        $sign = new SignImage();
        $ref = new ReflectionClass($sign);
        $method = $ref->getMethod('breakText');
        $method->setAccessible(true);

        $text = str_repeat('A', 80);
        $result = $method->invoke($sign, $text, SignImage::FONT_SIZE_LARGE);

        $this->assertStringContainsString(PHP_EOL, $result);
    }

    public function testBreakTextKeepsShortStrings()
    {
        $sign = new SignImage();
        $ref = new ReflectionClass($sign);
        $method = $ref->getMethod('breakText');
        $method->setAccessible(true);

        $text = 'Text curt';
        $result = $method->invoke($sign, $text, SignImage::FONT_SIZE_LARGE);

        $this->assertSame($text, $result);
    }
}
