<?php declare(strict_types=1);

namespace Tests\Managers;

use Brain\Monkey\Functions;
use Challenge\WordPressChallengePlugin;
use Challenge\Managers\AdminSettingsManager;
use Tests\BaseTestCase;

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

    public function testAdminMenuActionRegistered()
    {
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $manager = new AdminSettingsManager($plugin);
        $this->assertNotFalse(has_action('admin_menu', 'Challenge\Managers\AdminSettingsManager->registerMenu()'));
    }

    public function testAdminPostActionRegistered()
    {
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $manager = new AdminSettingsManager($plugin);
        $this->assertNotFalse(has_action('admin_post_challenge_tools', 'Challenge\Managers\AdminSettingsManager->saveSettingsAdmin()'));
    }

    public function testSaveSettingAllValuesValid()
    {
        // Prepare HTTP parameters
        $_POST['users_slug'] = 'test';
        $_POST['challenge_tools_nonce'] = 'test';
        // Set up expectations
        Functions\expect('update_option')->once()->with('users_slug', 'test');
        Functions\expect('flush_rewrite_rules')->once();
        Functions\expect('wp_safe_redirect')->once();
        $test_class = $this;
        // Set up functions
        Functions\when('admin_url')->alias(function($url) use ($test_class) {
            $test_class->assertStringContainsString('Data saved', $url);
            return $url;
        });
        // Execute saving
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $manager = new AdminSettingsManager($plugin);
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
        Functions\expect('wp_safe_redirect')->once();
        $test_class = $this;
        // Set up functions
        Functions\when('admin_url')->alias(function($url) use ($test_class) {
            $test_class->assertStringContainsString('Data saved', $url);
            $test_class->assertStringContainsString('message', $url);
            return $url;
        });
        // Execute saving
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $manager = new AdminSettingsManager($plugin);
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
        Functions\expect('wp_safe_redirect')->once();
        $test_class = $this;
        // Set up functions
        Functions\when('admin_url')->alias(function($url) use ($test_class) {
            $test_class->assertStringContainsString('Please enter a slug', $url);
            $test_class->assertStringContainsString('error', $url);
            return $url;
        });
        // Execute saving
        $plugin = new WordPressChallengePlugin(__DIR__ . '/..');
        $manager = new AdminSettingsManager($plugin);
        $manager->saveSettingsAdmin();
    }
}
