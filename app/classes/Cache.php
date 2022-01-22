<?php
/**
* This library is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* © Copyright 2005 Richard Heyes
*/

/**
* Caching Libraries for PHP5
*
* Handles data and output caching. Defaults to /dev/shm
* (shared memory). All methods are static.
*
* Eg: (output caching)
*
* if (!OutputCache::Start('group', 'unique id', 600)) {
*
*   // ... Output
*
*   OutputCache::End();
* }
*
* Eg: (data caching)
*
* if (!$data = DataCache::Get('group', 'unique id')) {
*
*   $data = time();
*
*   DataCache::Put('group', 'unique id', 10, $data);
* }
*
* echo $data;
*/
class Cache
{
    /**
    * Whether caching is enabled
    */
    public static bool $enabled = true;

    /**
    * Place to store the cache files
    */
    protected static string $store = '/dev/shm/';
        
    /**
    * Prefix to use on cache files
    */
    protected static string $prefix = 'cache_';

    /**
    * Stores data
    *
    * @param string $group Group to store data under
    * @param string $id    Unique ID of this data
    * @param int    $ttl   How long to cache for (in seconds)
    */
    protected static function write(string $group, string $id, int $ttl, string $data) : void
    {
        $filename = self::getFilename($group, $id);
            
        if (self::$enabled && $fp = fopen($filename, 'xb')) {
            if (flock($fp, LOCK_EX)) {
                fwrite($fp, $data);
            }
            fclose($fp);

            // Set filemtime
            touch($filename, time() + $ttl);
        }
    }
        
    /**
    * Reads data
    *
    * @param string $group Group to store data under
    * @param string $id    Unique ID of this data
    */
    protected static function read($group, $id) : string
    {
        $filename = self::getFilename($group, $id);
            
        return file_get_contents($filename);
    }
        
    /**
    * Determines if an entry is cached
    *
    * @param string $group Group to store data under
    * @param string $id    Unique ID of this data
    */
    protected static function isCached($group, $id) : bool
    {
        $filename = self::getFilename($group, $id);

        if (self::$enabled && file_exists($filename) && filemtime($filename) > time()) {
            return true;
        }

        if (is_file($filename)) {
            unlink($filename);
        }

        return false;
    }

    public static function prune(string $group, ?string $id = null) : void
    {
        if (is_null($id)) {
            $filenames = glob(self::$store . self::$prefix . "{$group}_*");
        } else {
            $filenames = [self::getFilename($group, $id)];
        }

        foreach ($filenames as $filename) {
            if (is_file($filename)) {
                unlink($filename);
            }
        }
    }
        
    /**
    * Builds a filename/path from group, id and
    * store.
    *
    * @param string $group Group to store data under
    * @param string $id    Unique ID of this data
    */
    protected static function getFilename(string $group, string $id) : string
    {
        $id = md5($id);

        return self::$store . self::$prefix . "{$group}_{$id}";
    }
        
    /**
    * Sets the filename prefix to use
    *
    * @param string $prefix Filename Prefix to use
    */
    public static function setPrefix($prefix) : void
    {
        self::$prefix = $prefix;
    }

    /**
    * Sets the store for cache files. Defaults to
    * /dev/shm. Must have trailing slash.
    *
    * @param string $store The dir to store the cache data in
    */
    public static function setStore($store) : void
    {
        self::$store = $store;
    }
}
    
    /**
    * Output Cache extension of base caching class
    */
class OutputCache extends Cache
{
    /**
    * Group of currently being recorded data
    */
    private static string $group;
        
    /**
    * ID of currently being recorded data
    */
    private static string $id;
        
    /**
    * Ttl of currently being recorded data
    */
    private static int $ttl;

    /**
    * Starts caching off. Returns true if cached, and dumps
    * the output. False if not cached and start output buffering.
    *
    * @param  string $group Group to store data under
    * @param  string $id    Unique ID of this data
    * @param  int    $ttl   How long to cache for (in seconds)
    * @return bool          True if cached, false if not
    */
    public static function Start($group, $id, $ttl) : bool
    {
        if (self::isCached($group, $id)) {
            echo self::read($group, $id);
            return true;
        } else {
            ob_start();
                
            self::$group = $group;
            self::$id    = $id;
            self::$ttl   = $ttl;
                
            return false;
        }
    }
        
    /**
    * Ends caching. Writes data to disk.
    */
    public static function End() : void
    {
        $data = ob_get_contents();
        ob_end_flush();
            
        self::write(self::$group, self::$id, self::$ttl, $data);
    }
}
    
    /**
    * Data cache extension of base caching class
    */
class DataCache extends Cache
{
    
    /**
    * Retrieves data from the cache
    *
    * @param  string $group Group this data belongs to
    * @param  string $id    Unique ID of the data
    * @return mixed         Either the resulting data, or null
    */
    public static function Get($group, $id) : mixed
    {
        if (self::isCached($group, $id)) {
            return unserialize(self::read($group, $id));
        }

        return null;
    }
        
    /**
    * Stores data in the cache
    *
    * @param string $group Group this data belongs to
    * @param string $id    Unique ID of the data
    * @param int    $ttl   How long to cache for (in seconds)
    * @param mixed  $data  The data to store
    */
    public static function Put($group, $id, $ttl, $data) : void
    {
        self::write($group, $id, $ttl, serialize($data));
    }
}
