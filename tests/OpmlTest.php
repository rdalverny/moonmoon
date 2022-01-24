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

    public function parseProvider()
    {
        return [
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

    /**
     * @dataProvider parseProvider
     */
    public function testParse($feedContents, $feeds, $title, $name, $email, $id)
    {
        $opml = new Opml();
        $entries = $opml->parse($feedContents);

        $this->assertEquals($feeds, $entries);
        $this->assertEquals($feeds, $opml->entries);
        $this->assertEquals($feeds, $opml->getPeople());
        $this->assertEquals($title, $opml->getTitle());
        $this->assertEquals($name, $opml->ownerName);
        $this->assertEquals($email, $opml->ownerEmail);
        $this->assertEquals($id, $opml->ownerId);
    }
}
