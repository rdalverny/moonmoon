<?php

class Opml
{
    private $_xml = null;
    private string $_currentTag = '';

    public string $ownerName = '';
    public string $ownerEmail = '';
    public string $ownerId = '';

    public string $dateCreated = '';
    public string $dateModified = '';

    public string $title = '';

    /** @var array<int, array<mixed>> */
    public $entries = array();

    /** @var array<string, string> */
    private $map  =
        array(
            'URL'         => 'website',
            'HTMLURL'     => 'website',
            'TEXT'        => 'name',
            'TITLE'       => 'name',
            'XMLURL'      => 'feed',
            'DESCRIPTION' => 'description',
            'ISDOWN'      => 'isDown',
        );


    /**
     * @param string $data
     * @return array<int, array<mixed>>
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
            case 'DATECREATED':
                $this->dateCreated = $cdata;
                break;
            case 'DATEMODIFIED':
                $this->dateModified = $cdata;
                break;
        }
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return array<int, array<mixed>>
    */
    public function getPeople() : array
    {
        return $this->entries;
    }

    /**
     *
     * @param  boolean $freezeDateModified update (or not) the dateModified entry - used for tests only
     * @return string  OPML valid string
     */
    public function formatString(bool $freezeDateModified = false) : string
    {
        $owner = '';
        if ($this->ownerName != '') {
            $owner .= '<ownerName>'.htmlspecialchars($this->ownerName).'</ownerName>'."\n";
        }
        if ($this->ownerEmail != '') {
            $owner .= '<ownerEmail>'.htmlspecialchars($this->ownerEmail).'</ownerEmail>'."\n";
        }
        if ($this->ownerId != '') {
            $owner .= '<ownerId>'.htmlspecialchars($this->ownerId).'</ownerId>'."\n";
        }

        $entries = '';
        foreach ($this->entries as $person) {
            $entries .= sprintf(
                "\t" . '<outline text="%s" htmlUrl="%s" xmlUrl="%s" isDown="%s" />',
                htmlspecialchars($person['name'], ENT_QUOTES),
                htmlspecialchars($person['website'], ENT_QUOTES),
                htmlspecialchars($person['feed'], ENT_QUOTES),
                htmlspecialchars($person['isDown'] ?? '', ENT_QUOTES)
            ) . "\n";
        }

        $template = <<<XML
<?xml version="1.0"?>
<opml version="2.0">
<head>
    <title>%s</title>
    <dateCreated>%s</dateCreated>
    <dateModified>%s</dateModified>
    %s
    <docs>http://opml.org/spec2.opml</docs>
</head>
<body>
%s
</body>
</opml>
XML;

        return sprintf(
            $template,
            htmlspecialchars($this->getTitle()),
            $this->dateCreated,
            $freezeDateModified ? $this->dateModified : date_format(date_create('now', new DateTimeZone('UTC')), DateTimeInterface::ATOM),
            $owner,
            $entries
        );
    }
}
