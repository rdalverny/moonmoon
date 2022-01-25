<?php

/**
 * Planet configuration class
 */
class PlanetConfig
{

    /** @var array<string, mixed> */
    protected $conf = [];

    private ?string $opmlFile = null;
    private ?string $cacheDir = null;
    private ?string $authInc = null;

    /** @var array<string, mixed> */
    public static $defaultConfig = [
        'url'           => 'http://www.example.com/',
        'name'          => '',
        'locale'        => 'en',
        'items'         => 10,
        'refresh'       => 3600,
        'cache'         => 1800,
        'categories'    => '',
        'cachedir'      => './cache',
        'debug'         => false,
        'checkcerts'    => true,
    ];

    private string $sitePrefix = '';

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
    }

    /**
     */
    public static function load(string $sitePrefix = '') : PlanetConfig
    {
        $config = new PlanetConfig([], true, $sitePrefix);

        if (!$config->isInstalled()) {
            // pre10 versions won't require $sitePrefix
            if (!$config->isInstalledPre10Version()) {
                return $config;
            }

            if (!$config->migratePre10Version()) {
                error_log('Failed to migrate configuration.');
                return $config;
            }
        }

        $configFile = realpath(config_path('config.yml', $sitePrefix));
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

    /**
     */
    public function saveConfig() : int
    {
        return file_put_contents(
            config_path('config.yml', $this->sitePrefix),
            $this->toYaml()
        );
    }

    public function saveAdminPassword(string $hash) : int
    {
        return file_put_contents(
            config_path('pwd.inc.php', $this->sitePrefix),
            sprintf('<?php $login="admin"; $password="%s"; ?>', $hash)
        );
    }

    public function saveOpml(Opml $opml) : int
    {
        return file_put_contents(
            config_path('people.opml', $this->sitePrefix),
            OpmlManager::format($opml)
        );
    }

    /**
     * Is moonmoon installed?
     *
     * @return bool
     */
    public function isInstalled() : bool
    {
        return file_exists(config_path('config.yml', $this->sitePrefix)) &&
            file_exists(config_path('people.opml', $this->sitePrefix));
    }

    /**
     * Or is it a pre-10 version installed?
     * (that is, config is stored in custom/config.yml instead of custom/config/config.yml)
     */
    public function isInstalledPre10Version(): bool
    {
        return file_exists(custom_path('config.yml')) &&
            file_exists(custom_path('people.opml'));
    }

    /**
     * Migrate config files from old to new location.
     * Purge cache.
     *
     * @return bool true if succeeded, false otherwise
     */
    public function migratePre10Version() : bool
    {
        if (!is_dir(config_path())) {
            if (!mkdir(config_path(), 0777, true)) {
                return false;
            }
        }

        return self::migrate_file('config.yml') &&
            self::migrate_file('people.opml');
    }

    public static function migrate_file(string $file) : bool
    {
        $source = custom_path($file);
        $dest = config_path($file);
        if (!copy($source, $source . '.bak')) {
            error_log("Failed to make a backup of ${source}");
            return false;
        }
        if (!rename($source, $dest)) {
            error_log("Failed to move ${source} to ${dest}");
            return false;
        }

        return true;
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

    public function getCacheDir() : string
    {
        if (is_null($this->cacheDir)) {
            $this->cacheDir = realpath(__DIR__ . '/../../'.$this->conf['cachedir']);
            if (!empty($this->sitePrefix)) {
                $this->cacheDir .= '/' . $this->sitePrefix;
            }
        }
        return $this->cacheDir;
    }

    public function getOpmlFile() : string
    {
        if (is_null($this->opmlFile)) {
            $this->opmlFile = realpath(config_path('people.opml', $this->sitePrefix));
        }
        return $this->opmlFile;
    }

    public function getAuthInc() : string
    {
        if (is_null($this->authInc)) {
            $this->authInc = realpath(config_path('pwd.inc.php', $this->sitePrefix));
        }
        return $this->authInc;
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
