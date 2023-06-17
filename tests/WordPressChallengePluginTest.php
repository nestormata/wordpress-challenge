<?php declare(strict_types=1);

namespace Tests;

use Brain\Monkey\Functions;
use Challenge\WordPressChallengePlugin;

class WordPressChallengePluginTest extends BaseTestCase
{

    public function testQueryVarsFilterRegistered()
    {
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $this->assertNotFalse(has_filter('query_vars', 'Challenge\WordPressChallengePlugin->registerQueryVars()'));
    }

    public function testInitActionRegistered()
    {
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $this->assertNotFalse(has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()'));
    }

    public function testReWriteWithDefaultValue()
    {
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $this->assertNotFalse(has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()'));

        Functions\when('get_option')->returnArg(2);
        Functions\expect('add_rewrite_rule')->once()->with('^users/?$', 'index.php?custom_page=1', 'top');
        $plugin->registerCustomRoute();
        $this->assertNotFalse(has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()'));
    }

    public function testReWriteWithCustomValue()
    {
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        Functions\when('get_option')->justReturn('custom-value');
        Functions\expect('add_rewrite_rule')->once()->with('^custom-value/?$', 'index.php?custom_page=1', 'top');
        $plugin->registerCustomRoute();
        $this->assertNotFalse(has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()'));
    }
}
