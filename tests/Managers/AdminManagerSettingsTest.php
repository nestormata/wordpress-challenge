<?php

declare(strict_types=1);

namespace Challenge\Tests\Managers;

use Brain\Monkey\Functions;
use Challenge\WordPressChallengePlugin;
use Challenge\Managers\AdminSettingsManager;
use Challenge\Tests\BaseTestCase;

class AdminManagerSettingsTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Prepare some general WP functions
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        Functions\when('wp_verify_nonce')->justReturn(true);
        Functions\when('__')->returnArg();
        Functions\when('wp_die')->justReturn();
    }

    private function initializeManager(): AdminSettingsManager
    {
        $plugin = new WordPressChallengePlugin(
            __DIR__ . '/..',
            'https://localhost/wp-content/plugins/wordpress-challenge'
        );
        return new AdminSettingsManager($plugin);
    }

    public function testAdminMenuActionRegistered()
    {
        $this->initializeManager();
        $this->assertNotFalse(
            has_action('admin_menu', 'Challenge\Managers\AdminSettingsManager->registerMenu()')
        );
    }

    public function testAdminPostActionRegistered()
    {
        $this->initializeManager();
        $this->assertNotFalse(
            has_action(
                'admin_post_challenge_tools',
                'Challenge\Managers\AdminSettingsManager->saveSettingsAdmin()'
            )
        );
    }

    public function testSaveSettingAllValuesValid()
    {
        // Prepare HTTP parameters
        $_POST['users_slug'] = 'test';
        $_POST['challenge_tools_nonce'] = 'test';
        // Set up expectations
        Functions\expect('update_option')->once()->with('users_slug', 'test');
        Functions\expect('flush_rewrite_rules')->once();
        // Set up functions
        Functions\when('admin_url')->returnArg();
        Functions\when('wp_safe_redirect')->alias(static function (string $url) {
            BaseTestCase::assertStringContainsString('Data saved', $url);
            BaseTestCase::assertStringContainsString('message', $url);
        });
        // Execute saving
        $manager = $this->initializeManager();
        $manager->saveSettingsAdmin();
    }

    public function testSaveSettingSlugWithInvalidChars()
    {
        // Prepare HTTP parameters
        $_POST['users_slug'] = '@TeSt#)%340';
        $_POST['challenge_tools_nonce'] = 'test';
        // Set up expectations
        Functions\expect('update_option')->once()->with('users_slug', 'test340');
        Functions\expect('flush_rewrite_rules')->once();
        // Set up functions
        Functions\when('admin_url')->returnArg();
        Functions\when('wp_safe_redirect')->alias(static function (string $url) {
            BaseTestCase::assertStringContainsString('Data saved', $url);
            BaseTestCase::assertStringContainsString('message', $url);
        });
        // Execute saving
        $manager = $this->initializeManager();
        $manager->saveSettingsAdmin();
    }

    public function testSaveSettingSlugWithEmptySlugShouldError()
    {
        // Prepare HTTP parameters
        $_POST['users_slug'] = '';
        $_POST['challenge_tools_nonce'] = 'test';
        // Set up expectations
        Functions\expect('update_option')->never();
        Functions\expect('flush_rewrite_rules')->never();
        // Set up functions
        Functions\when('admin_url')->returnArg();
        Functions\when('wp_safe_redirect')->alias(static function (string $url) {
            BaseTestCase::assertStringContainsString('Please enter a slug', $url);
            BaseTestCase::assertStringContainsString('error', $url);
        });
        // Execute saving
        $manager = $this->initializeManager();
        $manager->saveSettingsAdmin();
    }
}
