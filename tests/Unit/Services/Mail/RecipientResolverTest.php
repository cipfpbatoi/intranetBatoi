<?php

namespace Tests\Unit\Services\Mail;

use Intranet\Services\Mail\RecipientResolver;
use Tests\TestCase;

class RecipientResolverTest extends TestCase
{
    public function testFormatReceiversAddsTrailingComma()
    {
        $resolver = new RecipientResolver();

        $a = (object) ['id' => 1, 'mail' => 'a@b.com', 'contact' => 'A'];
        $b = (object) ['id' => 2, 'email' => 'c@d.com', 'contacto' => 'B'];

        $result = $resolver->formatReceivers([$a, $b]);

        $this->assertSame('1(a@b.com;A),2(c@d.com;B),', $result);
    }

    public function testResolveElementsReturnsCollectionForNull()
    {
        $resolver = new RecipientResolver();

        $result = $resolver->resolveElements(null, null);

        $this->assertTrue($result->isEmpty());
    }
}
