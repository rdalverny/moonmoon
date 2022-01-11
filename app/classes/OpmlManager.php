<?php


class OpmlManager
{
    public static function load($file)
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
     * @param Opml   $opml
     * @param string $file
     */
    public static function save($opml, $file)
    {
        $out = '<?xml version="1.0"?>'."\n";
        $out.= '<opml version="2.0">'."\n";
        $out.= '<head>'."\n";
        $out.= '<title>'.htmlspecialchars($opml->getTitle()).'</title>'."\n";
        $out.= '<dateCreated>'.gmdate('c').'</dateCreated>'."\n";
        $out.= '<dateModified>'.gmdate('c').'</dateModified>'."\n";
        if ($opml->ownerName != '') {
            $out.= '<ownerName>'.htmlspecialchars($opml->ownerName).'</ownerName>'."\n";
        }
        if ($opml->ownerEmail != '') {
            $out.= '<ownerEmail>'.htmlspecialchars($opml->ownerEmail).'</ownerEmail>'."\n";
        }
        if ($opml->ownerId != '') {
            $out.= '<ownerId>'.htmlspecialchars($opml->ownerId).'</ownerId>'."\n";
        }
        $out.= '<docs>http://opml.org/spec2.opml</docs>'."\n";

        $out.= '</head>'."\n";
        $out.= '<body>'."\n";
        foreach ($opml->entries as $person) {
            $out .= sprintf(
                '<outline text="%s" htmlUrl="%s" xmlUrl="%s" isDown="%s" />',
                htmlspecialchars($person['name'], ENT_QUOTES),
                htmlspecialchars($person['website'], ENT_QUOTES),
                htmlspecialchars($person['feed'], ENT_QUOTES),
                htmlspecialchars($person['isDown'] ?? '', ENT_QUOTES)
            ) . "\n";
        }
        $out.= '</body>'."\n";
        $out.= '</opml>';

        file_put_contents($file, $out);
    }

    public static function backup($file)
    {
        copy($file, $file.'.bak');
    }
}
