<?php


class OpmlManager
{
    public static function load(string $file) : Opml
    {
        if (!file_exists($file)) {
            throw new Exception('OPML file not found!');
        }

        $opml = new Opml();

        //Remove BOM if needed
        $BOM = '/^﻿/';
        $fileContent = file_get_contents($file);
        $fileContent = preg_replace($BOM, '', $fileContent, 1);

        //Parse
        $opml->parse($fileContent);

        return $opml;
    }

    /**
     *
     * @param  Opml $opml object
     * @param  boolean $freezeDateModified update (or not) the dateModified entry - used for tests only
     * @return string  OPML valid string
     */
    public static function format(Opml $opml, bool $freezeDateModified = false) : string
    {
        $owner = '';
        if ($opml->ownerName != '') {
            $owner .= '<ownerName>'.htmlspecialchars($opml->ownerName).'</ownerName>'."\n";
        }
        if ($opml->ownerEmail != '') {
            $owner .= '<ownerEmail>'.htmlspecialchars($opml->ownerEmail).'</ownerEmail>'."\n";
        }
        if ($opml->ownerId != '') {
            $owner .= '<ownerId>'.htmlspecialchars($opml->ownerId).'</ownerId>'."\n";
        }

        $entries = '';
        foreach ($opml->entries as $person) {
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
            htmlspecialchars($opml->getTitle()),
            $opml->dateCreated,
            $freezeDateModified ? $opml->dateModified : date_format(date_create('now', new DateTimeZone('UTC')), DateTimeInterface::ATOM),
            $owner,
            $entries
        );
    }

    /**
     * @param Opml   $opml
     * @param string $file
     */
    public static function save(Opml $opml, string $file) : int|bool
    {
        return file_put_contents($file, self::format($opml));
    }

    public static function backup(string $file) : void
    {
        copy($file, $file.'.bak');
    }
}
