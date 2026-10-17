<?php
use PHPUnit\Framework\TestCase;

class CsrfTest extends TestCase
{
    public function testTokenAndValidate()
    {
        $token = \GOF\Helpers\Csrf::token();
        $this->assertIsString($token);
        $this->assertTrue(\GOF\Helpers\Csrf::validate($token));
        $this->assertFalse(\GOF\Helpers\Csrf::validate('invalid-token'));
    }
}
