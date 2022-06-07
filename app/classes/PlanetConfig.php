<?php

/**
 * Planet configuration class
 */
class PlanetConfig
{

    /** @var array<string, mixed> */
    protected $conf = [];

    private string $sitePrefix = '';

    private ?string $authFile     = null;
    private ?string $cacheDir     = null;
    private ?string $cacheRootDir = null;
    private ?string $configDir    = null;
    private ?string $configFile   = null;
    private ?string $opmlFile     = null;

    /** @var array<string, mixed> */
    public static $defaultConfig = [
        'url'        => 'http://www.example.com/',
        'name'       => '',
        'locale'     => 'en',
        'items'      => 10,
        'refresh'    => 3600,
        'cache'      => 1800,
        'print_full_post'   => true,
        'categories' => '',
        'cachedir'   => './cache',
        'debug'      => false,
        'checkcerts' => true,
    ];

    /**
     * PlanetConfig constructor.
     * @param array<string, mixed> $userConfig
     * @param bool  $useDefaultConfig
     */
    public function __construct($userConfig = [], $useDefaultConfig = true, string $sitePrefix = '')
    {
        $default = $useDefaultConfig ? self::$defaultConfig : [];
        $this->conf = $this->merge($default, $userConfig);

        $this->sitePrefix = $sitePrefix;

        $this->configDir = __DIR__ . '/../../custom/config';
        if (!empty($this->sitePrefix)) {
            $this->configDir .= '/' . $this->sitePrefix;
        }

        $this->cacheRootDir = realpath(__DIR__ . '/../../' . ($this->conf['cachedir'] ?? 'cache'));
    }

    /**
     */
    public static function load(string $sitePrefix = '') : PlanetConfig
    {
        $config = new PlanetConfig([], true, $sitePrefix);

        if (!$config->isInstalled()) {
            return $config;
        }

        $configFile = realpath($config->getConfigFile());
        $conf = Spyc::YAMLLoad($configFile);

        // this is a check to upgrade older config file without l10n
        if (!isset($conf['locale'])) {
            $resetPlanetConfig = new PlanetConfig($conf, true, $sitePrefix);
            file_put_contents($configFile, $resetPlanetConfig->toYaml());
            $conf = Spyc::YAMLLoad($configFile);
            return $resetPlanetConfig;
        }

        $config = new PlanetConfig($conf, true, $sitePrefix);

        return $config;
    }

    public static function config_path(string $file = '', string $site = '') : string
    {
        $path = __DIR__ . '/../../custom/config';
        if (!empty($site)) {
            $path .= '/' . $site;
        }
        if (!empty($file)) {
            $path .= '/' . $file;
        }
        return $path;
    }

    /**
     */
    public function saveConfig() : int
    {
        return file_put_contents(
            $this->getConfigFile(),
            $this->toYaml()
        );
    }

    /**
     * Is moonmoon installed?
     *
     * @return bool
     */
    public function isInstalled() : bool
    {
        if (file_exists($this->getConfigFile()) &&
            file_exists($this->getOpmlFile())) {
            return true;
        }

        // if old install <=9.x, migrate it
        if (file_exists(__DIR__ . '/../../custom/config.yml')) {
            if (!is_dir($this->configDir)) {
                if (!mkdir($this->configDir, 0777, true)) {
                    error_log('Could not create config directory.');
                    return false;
                }
            }
            rename(__DIR__ . '/../../custom/config.yml', $this->getConfigFile());
            rename(__DIR__ . '/../../custom/people.opml', $this->getOpmlFile());
            rename(__DIR__ . '/../../admin/pwd.inc.php', $this->configDir . '/pwd.inc.php');

            return true;
        }

        return false;
    }

    /**
     * @param array<string,mixed> $userConfig
     */
    public function setConfig($userConfig = []) : void
    {
        $this->conf = $this->merge(self::$defaultConfig, $userConfig);
    }

    /**
     * Merge the configuration of the user in the default one.
     *
     * @param array<string, mixed> $default
     * @param array<string, mixed> $user
     * @return array<string, mixed>
     */
    protected function merge($default = [], $user = [])
    {
        return array_merge($default, $this->normalizeArrayKeys($user));
    }

    public function getUrl() : string
    {
        return $this->conf['url'];
    }

    public function getName() : string
    {
        return $this->conf['name'];
    }

    public function setName(string $name) : void
    {
        $this->conf['name'] = $name;
    }

    public function getCacheTimeout() : int
    {
        return $this->conf['refresh'];
    }

    public function getCacheRootDir() : string
    {
        return $this->cacheRootDir;
    }

    public function getCacheDir() : string
    {
        if (!empty($this->sitePrefix)) {
            return $this->cacheRootDir . '/' . $this->sitePrefix;
        }
        return $this->cacheRootDir;
    }

    public function getConfigDir() : string
    {
        return $this->configDir;
    }

    public function getOpmlFile() : string
    {
        return $this->configDir . '/people.opml';
    }

    public function getConfigFile() : string
    {
        return $this->configDir . '/config.yml';
    }

    public function getAuthFile() : string
    {
        return $this->configDir . '/pwd.inc.php';
    }

    public function getOutputTimeout() : int
    {
        return $this->conf['cache'];
    }

    public function getMaxDisplay() : int
    {
        return $this->conf['items'];
    }

    public function getCategories() : string
    {
        return $this->conf['categories'];
    }

    public function toYaml() : string
    {
        return Spyc::YAMLDump($this->conf, 4);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return $this->conf;
    }

    public function getDebug() : bool
    {
        return $this->conf['debug'];
    }

    public function checkCertificates() : bool
    {
        return $this->conf['checkcerts'];
    }

    public function getLocale() : string
    {
        return $this->conf['locale'];
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultConfig()
    {
        return self::$defaultConfig;
    }

    public function getSitePrefix() : string
    {
        return $this->sitePrefix;
    }

    public function showFullPost(): bool
    {
        return $this->conf['print_full_post'] ?? true;
    }

    /**
     * Normalize the name of a configuration key.
     *
     * @param  string $key
     * @return string
     */
    protected function normalizeKeyName(string $key) : string
    {
        return strtolower($key);
    }

    /**
     * Normalize all the keys of the array.
     *
     * @param  array<string, mixed> $array
     * @return array<string, mixed>
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
    public function __get(string $key)
    {
        $key = $this->normalizeKeyName($key);

        return array_key_exists($key, $this->conf) ?
            $this->conf[$key] :
            null;
    }

    /**
     * Generic configuration setter.
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value) : void
    {
        $key = $this->normalizeKeyName($key);

        $this->conf[$key] = $value;
    }
}
