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
     * @param Opml   $opml
     * @param string $file
     * @return int|false
     */
    public static function save(Opml $opml, string $file)
    {
        return file_put_contents($file, $opml->formatString());
    }

    public static function backup(string $file) : void
    {
        copy($file, $file.'.bak');
    }
}
