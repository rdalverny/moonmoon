<?php

class Opml
{
    private $_xml = null;
    private string $_currentTag = '';

    public string $ownerName = '';
    public string $ownerEmail = '';
    public string $ownerId = '';

    public string $title = '';

    /** @var array<int, string> */
    public $entries = array();
    private $map  =
        array(
            'URL'         => 'website',
            'HTMLURL'     => 'website',
            'TEXT'        => 'name',
            'TITLE'       => 'name',
            'XMLURL'      => 'feed',
            'DESCRIPTION' => 'description',
            'ISDOWN'      => 'isDown'
        );


    /**
     * @param string $data
     * @return array<int, string>
    */
    public function parse(string $data) : array
    {
        $this->_xml = xml_parser_create('UTF-8');
        //xml_parser_set_option($this->_xml, XML_OPTION_CASE_FOLDING, false);
        //xml_parser_set_option($this->_xml, XML_OPTION_SKIP_WHITE, true);
        xml_set_object($this->_xml, $this);
        xml_set_element_handler($this->_xml, [$this, '_openTag'], [$this, '_closeTag']);
        xml_set_character_data_handler($this->_xml, [$this, '_cData']);

        xml_parse($this->_xml, $data);
        xml_parser_free($this->_xml);
        return $this->entries;
    }


    /**
     * @param array<string, string> $attrs
    */
    private function _openTag($p, string $tag, array $attrs) : void
    {
        $this->_currentTag = $tag;

        if ($tag == 'OUTLINE') {
            $i = count($this->entries);
            foreach (array_keys($this->map) as $key) {
                if (isset($attrs[$key])) {
                    $this->entries[$i][$this->map[$key]] = $attrs[$key];
                }
            }
        }
    }

    private function _closeTag($p, string $tag) : void
    {
        $this->_currentTag = '';
    }

    private function _cData($p, string $cdata) : void
    {
        switch ($this->_currentTag) {
            case 'TITLE':
                $this->title = $cdata;
                break;
            case 'OWNERNAME':
                $this->ownerName = $cdata;
                break;
            case 'OWNEREMAIL':
                $this->ownerEmail = $cdata;
                break;
            case 'OWNERID':
                $this->ownerId = $cdata;
                break;
        }
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return array<int, string>
    */
    public function getPeople() : array
    {
        return $this->entries;
    }
}
