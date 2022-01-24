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

    public function verifyProvider()
    {
        $token = CSRF::generate("some-action");
        return [
            'valid pair'        => [$token, 'some-action', true],
            'different action'  => [$token, 'other-action', false],
            'wrong token value' => ['anything-else', 'some-action', false],
            'wrong token type'  => [1, 'string', false],
            'wrong action type' => ['string', 2, false],
            'null token/action' => [null, null, false]
        ];
    }

    /**
     * @dataProvider verifyProvider
     */
    public function testVerify($token, $action, $expected)
    {
        $this->assertEquals(CSRF::verify($token, $action), $expected);
    }
}
