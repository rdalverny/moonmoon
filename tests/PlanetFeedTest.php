<?php

use PHPUnit\Framework\TestCase;

class PlanetFeedTest extends TestCase
{
    protected $feed;
    protected $items;

    public function setUp() : void
    {
        $this->feed = new PlanetFeed('Test Feed', 'http://localhost:8081/tests/feeds/feed-rss2.rss', 'http://localhost:8081/tests/', '');
    }

    protected function _after()
    {
        unset($this->feed);
    }

    public function testNo()
    {
        //print_r($this->feed);
        $this->assertEquals(true, true);
    }

    public function testItemsContent()
    {
        if (!$this->feed->init()) {
            $this->fail(sprintf('Failed to init feed: %s', $this->feed->error));
        }
        $items = $this->feed->get_items();
        $this->assertEquals(5, count($items));

        $expectedContents = [
            '<div>Blabla.</div>',
            "<div>Blabla.  Ho. Ho. Ho.</div>",
            '<div>Image relative: <img src="https://blog.example.com/data/images/jjg.jpg"></div>',
            '<div>Audio relatif: <audio src="https://blog.example.com/data/documents/jjg.mp3" type="audio/mp3" controls="controls" preload="none"></audio></div>',
            //'<div>Video relative: <video preload="none"><source src="https://blog.example.com/data/documents/jjg.mpeg"></source></video></div>'
        ];
        foreach ($expectedContents as $ix => $value) {
            $this->assertEquals($value, $items[$ix]->get_content());
        }
    }
}
