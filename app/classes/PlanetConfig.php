<?php

/**
 * Planet configuration class
 */
class PlanetConfig
{

    protected $conf = [];

    private ?string $opmlFile = null;
    private ?string $cacheDir = null;

    public static $defaultConfig = [
        'url'           => 'http://www.example.com/',
        'name'          => '',
        'locale'        => 'en',
        'items'         => 10,
        'shuffle'       => 0,
        'refresh'       => 240,
        'cache'         => 10,
        'nohtml'        => 0,
        'postmaxlength' => 0,
        'categories'    => '',
        'cachedir'      => './cache',
        'debug'         => false,
        'checkcerts'    => true,
    ];

    /**
     * PlanetConfig constructor.
     * @param array $userConfig
     * @param bool  $useDefaultConfig
     */
    public function __construct($userConfig = [], $useDefaultConfig = true)
    {
        $default = $useDefaultConfig ? self::$defaultConfig : array();
        $this->conf = $this->merge($default, $userConfig);
    }

    /**
     * @return static
     */
    public static function load(string $dir)
    {
        $config = new PlanetConfig;
        $configFile = realpath($dir . '/../custom/config.yml');

        if (self::isInstalled()) {
            $conf = Spyc::YAMLLoad($configFile);

            // this is a check to upgrade older config file without l10n
            if (!isset($conf['locale'])) {
                $resetPlanetConfig = new PlanetConfig($conf);
                file_put_contents($configFile, $resetPlanetConfig->toYaml());
                $conf = Spyc::YAMLLoad($configFile);

                return $resetPlanetConfig;
            }

            $config = new PlanetConfig($conf);
        }

        return $config;
    }

    /**
     * Is moonmoon installed?
     *
     * @return bool
     */
    public static function isInstalled() : bool
    {
        return file_exists(custom_path('config.yml')) &&
            file_exists(custom_path('people.opml'));
    }

    /**
     * Merge the configuration of the user in the default one.
     *
     * @param array $default
     * @param array $user
     * @return array
     */
    protected function merge($default = [], $user = [])
    {
        return array_merge($default, $this->normalizeArrayKeys($user));
    }

    public function getUrl()
    {
        return $this->conf['url'];
    }

    public function getName()
    {
        return $this->conf['name'];
    }

    public function setName($name)
    {
        $this->conf['name'] = $name;
    }

    public function getCacheTimeout()
    {
        return $this->conf['refresh'];
    }

    public function getCacheDir()
    {
        if (is_null($this->cacheDir)) {
            $this->cacheDir = realpath(__DIR__ . '/../../'.$this->conf['cachedir']);
        }
        return $this->cacheDir;
    }

    public function getOpmlFile()
    {
        if (is_null($this->opmlFile)) {
            $this->opmlFile = realpath(__DIR__ . '/../../custom/people.opml');
        }
        return $this->opmlFile;
    }

    public function getOutputTimeout()
    {
        return $this->conf['cache'];
    }

    public function getMaxDisplay()
    {
        return $this->conf['items'];
    }

    public function getCategories()
    {
        return $this->conf['categories'];
    }

    public function toYaml()
    {
        return Spyc::YAMLDump($this->conf, 4);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->conf;
    }

    public function getDebug()
    {
        return $this->conf['debug'];
    }

    public function checkCertificates()
    {
        return $this->conf['checkcerts'];
    }

    public function getLocale()
    {
        return $this->conf['locale'];
    }

    /**
     * @return array
     */
    public function getDefaultConfig()
    {
        return self::$defaultConfig;
    }

    /**
     * Normalize the name of a configuration key.
     *
     * @param  string $key
     * @return string
     */
    protected function normalizeKeyName($key = null)
    {
        return strtolower($key);
    }

    /**
     * Normalize all the keys of the array.
     *
     * @param  array $array
     * @return array
     */
    protected function normalizeArrayKeys($array = [])
    {
        foreach ($array as $key => $value) {
            $normalized = $this->normalizeKeyName($key);
            if ($normalized !== $key) {
                $array[$this->normalizeKeyName($key)] = $value;
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Generic configuration getter.
     *
     * @return mixed|null
     */
    public function __get($key)
    {
        $key = $this->normalizeKeyName($key);

        return array_key_exists($key, $this->conf) ?
            $this->conf[$key] :
            null;
    }

    /**
     * Generic configuration setter.
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $key = $this->normalizeKeyName($key);

        $this->conf[$key] = $value;
    }
}
