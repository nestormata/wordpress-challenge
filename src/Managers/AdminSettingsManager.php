<?php

declare(strict_types=1);

namespace Challenge\Managers;

use Challenge\WordPressChallengePlugin;
use Challenge\Helpers\TemplateEngine;

/**
 * Handles the administration pages for the settings options.
 */
class AdminSettingsManager
{
    private WordPressChallengePlugin $plugin;

    /**
     * Construct the Admin Settigns Manager.
     */
    public function __construct(WordPressChallengePlugin $plugin)
    {
        $this->plugin = $plugin;
        $this->registerFiltersAndActions();
    }

    /**
     * Register Filters and Actions for this manager.
     */
    private function registerFiltersAndActions(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_post_challenge_tools', [$this, 'saveSettingsAdmin']);
    }

    /**
     * Register the admin menu option.
     */
    public function registerMenu(): void
    {
        add_submenu_page(
            'tools.php',
            'WordPress Challenge',
            'Challenge',
            'manage_options',
            'challenge',
            [$this, 'showSettingsAdminPage']
        );
    }

    /**
     * Renders the setting admin page.
     */
    public function showSettingsAdminPage(): void
    {
        $nounceField = wp_nonce_field(
            'challenge_tools_nonce',
            'challenge_tools_nonce',
            true,
            false
        );
        $slug = get_option('users_slug', 'users');
        $submitButton = get_submit_button('Save');
        $adminUrl = admin_url('admin-post.php');
        $baseUrl = get_home_url();
        $message = sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''));
        $error = sanitize_text_field(wp_unslash($_REQUEST['error'] ?? ''));
        $engine = new TemplateEngine($this->plugin->pluginDir());
        echo $engine->render('AdminToolsPage', compact(
            'nounceField',
            'slug',
            'submitButton',
            'baseUrl',
            'adminUrl',
            'message',
            'error'
        ));
    }

    /**
     * Save the changes to the settings admin page.
     */
    public function saveSettingsAdmin(): void
    {
        // Verify the nonce for security.
        if (
            isset($_POST['challenge_tools_nonce'])
            && wp_verify_nonce($_POST['challenge_tools_nonce'], 'challenge_tools_nonce')
            && isset($_POST['users_slug'])
        ) {
            // Sanitize and save the entered value.
            $usersSlug = sanitize_text_field($_POST['users_slug']);
            if (0 === strlen($usersSlug)) {
                wp_safe_redirect(admin_url('tools.php?page=challenge&error=Please enter a slug'));
                exit;
            }
            update_option('users_slug', $usersSlug);
            flush_rewrite_rules();
            wp_safe_redirect(admin_url('tools.php?page=challenge&message=Data saved'));
            exit;
        }
    }
}
