<?php

require_once 'GuzzleHarness.php';

class InstallTest extends GuzzleHarness
{

    public function setUp() : void
    {
        parent::setUp();
        removeCustomFiles();
    }

    public function tearDown() : void
    {
        parent::tearDown();
        removeCustomFiles();
    }

    public function test_index_page_tells_moonmoon_is_not_installed()
    {
        $res = $this->client->get('/index.php');
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringContainsString('install moonmoon', (string) $res->getBody());
    }

    public function test_install_page_loads_without_error()
    {
        $res = $this->client->get('/install.php');
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringContainsString('Administrator password', (string) $res->getBody());
    }

    /**
     * Regression test, `people.opml` was created by requesting `/install.php`
     * even if the site was not installed: `touch()` was called to see if
     * the path was writable but the file was not removed.
     */
    public function test_get_install_page_should_not_create_custom_files()
    {
        $this->client->get('/install.php');
        $this->assertFalse(file_exists(config_path('people.opml')));
        $this->assertFalse(file_exists(config_path('config.yml')));
        $this->assertFalse(file_exists(config_path('pwc.inc.php')));
    }

    public function test_install_button()
    {
        $data = [
            'url' => 'http://127.0.0.1:8081/',
            'title' => 'My website',
            'password' => 'admin',
            'locale' => 'en',
        ];

        $res = $this->client->request('POST', '/install.php', [
            'form_params' => $data
        ]);
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringContainsString('Your moonmoon is ready.', (string) $res->getBody());
    }


    public function testMigratePre10SetupAuto()
    {
        $this->assertEquals(false, PlanetConfig::isInstalledPre10Version(), "Planet is not installed /old config");
        $this->assertEquals(false, PlanetConfig::isInstalled(), "Planet is not installed /new config");

        $config = new PlanetConfig();
        file_put_contents(custom_path('config.yml'), $config->toYaml());
        OpmlManager::save(new Opml(), custom_path('people.opml'));

        $this->assertEquals(true, PlanetConfig::isInstalledPre10Version(), "Planet is installed /old config");
        $this->assertEquals(false, PlanetConfig::isInstalled(), "Planet is not installed /new config");

        // explicitly migrate
        $this->assertEquals(true, PlanetConfig::migratePre10Version(), "Migration succeeded");

        $this->assertEquals(true, PlanetConfig::isInstalled(), "Planet is installed /new config");
        $this->assertFileExists(custom_path('config.yml.bak'), "Backup config is kept");
        $this->assertFileExists(custom_path('people.opml.bak'), "Backup OPML is kept");
    }

    public function testMigratePre10SetupIndex()
    {
        $this->assertEquals(false, PlanetConfig::isInstalledPre10Version(), "Planet is not installed /old config");
        $this->assertEquals(false, PlanetConfig::isInstalled(), "Planet is not installed /new config");

        $config = new PlanetConfig();
        file_put_contents(custom_path('config.yml'), $config->toYaml());
        OpmlManager::save(new Opml(), custom_path('people.opml'));

        $this->assertEquals(true, PlanetConfig::isInstalledPre10Version(), "Planet is installed /old config");
        $this->assertEquals(false, PlanetConfig::isInstalled(), "Planet is not installed /new config");

        // call through web interface
        $this->client->get('/');

        $this->assertEquals(true, PlanetConfig::isInstalled(), "Planet is installed /new config");
        $this->assertFileExists(custom_path('config.yml.bak'), "Backup config is kept");
        $this->assertFileExists(custom_path('people.opml.bak'), "Backup OPML is kept");
    }
}
