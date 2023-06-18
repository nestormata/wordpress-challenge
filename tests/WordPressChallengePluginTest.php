<?php

declare(strict_types=1);

namespace Challenge\Tests;

use Brain\Monkey\Functions;
use Challenge\WordPressChallengePlugin;

class WordPressChallengePluginTest extends BaseTestCase
{
    private function initializePlugin(): WordPressChallengePlugin
    {
        return new WordPressChallengePlugin(
            __DIR__ . '/..',
            'https://localhost/wp-content/plugins/wordpress-challenge'
        );
    }

    public function testQueryVarsFilterRegistered(): void
    {
        $this->initializePlugin();
        $this->assertNotFalse(
            has_filter('query_vars', 'Challenge\WordPressChallengePlugin->registerQueryVars()')
        );
    }

    public function testInitActionRegistered(): void
    {
        $this->initializePlugin();
        $this->assertNotFalse(
            has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()')
        );
    }

    public function testReWriteWithDefaultValue(): void
    {
        $plugin = $this->initializePlugin();
        $this->assertNotFalse(
            has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()')
        );

        Functions\when('get_option')
            ->returnArg(2);
        Functions\expect('add_rewrite_rule')
            ->once()
            ->with('^users/?$', 'index.php?custom_page=1', 'top');
        $plugin->registerCustomRoute();
        $this->assertNotFalse(
            has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()')
        );
    }

    public function testReWriteWithCustomValue(): void
    {
        $plugin = $this->initializePlugin();
        Functions\when('get_option')
            ->justReturn('custom-value');
        Functions\expect('add_rewrite_rule')
            ->once()
            ->with('^custom-value/?$', 'index.php?custom_page=1', 'top');
        $plugin->registerCustomRoute();
        $this->assertNotFalse(
            has_action('init', 'Challenge\WordPressChallengePlugin->registerCustomRoute()')
        );
    }
}
