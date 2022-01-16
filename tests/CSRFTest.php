<?php

use PHPUnit\Framework\TestCase;

class CSRFTest extends TestCase
{
    public function testGetKey()
    {
        $this->temp_key = CSRF::getKey();
        $this->assertIsString($this->temp_key);
        $this->assertEquals(32, strlen($this->temp_key));
    }

    public function testGenerate()
    {
        $token = CSRF::generate("some-action");
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));
        
        $this->expectException(InvalidArgumentException::class);
        CSRF::generate();
        CSRF::generate(12);
        CSRF::generate(null);
    }

    public function testVerify()
    {
        $token = CSRF::generate("some-action");
        $this->assertEquals(CSRF::verify($token, "some-action"), true);
        $this->assertEquals(CSRF::verify($token, "other-action"), false);
        $this->assertEquals(CSRF::verify("anything-else", "some-action"), false);
        $this->assertEquals(CSRF::verify(1, "string"), false);
        $this->assertEquals(CSRF::verify("string", 2), false);
        $this->assertEquals(CSRF::verify(null, null), false);
    }
}
