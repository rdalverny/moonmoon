<?php

use PHPUnit\Framework\TestCase;

class OpmlTest extends TestCase
{
    public function setUp() : void
    {
        $this->fixtures = [
            [file_get_contents('./tests/opml/test-empty.opml'), [], '', '', '', ''],
            [
                file_get_contents('./tests/opml/test-valid.opml'),
                [
                    [
                        'website' => 'https://blog.example.com/',
                        'name' => 'text 1',
                        'feed' => 'https://some.other.example.com/feed/path',
                        'isDown' => '',
                    ],
                    [
                        'website' => 'https://blog2.example.com',
                        'name' => 'text 2',
                        'feed' => 'https://blog2.example.com/rss.xml',
                        'isDown' => '',
                    ]
                ],
                'Test OPML',
                'user name',
                'user@example.com',
                'http://user.example.com/'
            ]
        ];
    }

    public function testLoadValidFile()
    {
        $mngr = OpmlManager::load('tests/opml/test-valid.opml');
        $this->assertInstanceOf('Opml', $mngr);
    }

    public function testLoadAbsentFile()
    {
        $this->expectException('Exception');
        OpmlManager::load('/some/where');
    }

    public function testFormat()
    {
        $file = 'tests/opml/test-valid.opml';
        $opml = OpmlManager::load($file);
        $this->assertXmlStringEqualsXmlFile($file, OpmlManager::format($opml, true));
    }
}
