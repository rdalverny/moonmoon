<?php

use PHPUnit\Framework\TestCase;

class OpmlManagerTest extends TestCase
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

    public function testParse()
    {
        foreach ($this->fixtures as $data) {
            $given = $data[0];
            $entries = $data[1];

            $opml = new Opml();
            $entries = $opml->parse($given);

            $this->assertEquals($data[1], $entries);
            $this->assertEquals($data[1], $opml->entries);
            $this->assertEquals($data[1], $opml->getPeople());
            $this->assertEquals($data[2], $opml->getTitle());
            $this->assertEquals($data[3], $opml->ownerName);
            $this->assertEquals($data[4], $opml->ownerEmail);
            $this->assertEquals($data[5], $opml->ownerId);
        }
    }
}
