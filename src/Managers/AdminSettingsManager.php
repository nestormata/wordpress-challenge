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
        // Create nounce hidden field
        $nounceField = wp_nonce_field(
            'challenge_tools_nonce',
            'challenge_tools_nonce',
            true,
            false
        );
        // Gather data for the template
        $slug = get_option('users_slug', 'users');
        $submitButton = get_submit_button('Save');
        $adminUrl = admin_url('admin-post.php');
        $baseUrl = get_home_url();
        $message = sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''));
        $error = sanitize_text_field(wp_unslash($_REQUEST['error'] ?? ''));
        // Render the template and pass the data
        $engine = new TemplateEngine($this->plugin->pluginDir());
        // I disable the phpcs rule because the engine is in charge of handling the output.
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
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
        if ($this->isSlugProvidedAndNounceValid()) {
            // Sanitize and make sure only letters, numbers or - or _ are left.
            // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $usersSlug = preg_replace(
                '/[^a-z0-9_-]+/',
                '',
                strtolower($_POST['users_slug'] ?? '')
            );
            // If no slug given return error and don't save
            $message = __('Please enter a slug', 'challenge');
            $url = admin_url('tools.php?page=challenge&error=' . $message);
            if (0 !== strlen($usersSlug)) {
                // Save the new slug
                update_option('users_slug', $usersSlug);
                flush_rewrite_rules();
                $message = __('Data saved', 'challenge');
                $url = admin_url('tools.php?page=challenge&message=' . $message);
            }
            // I disable the rule because I'm indeed calling die, but on testing.
            // phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
            wp_safe_redirect($url);
            // We don't die in testing, but we do in normal execution.
            if (!defined('UNIT_TESTING')) {
                die;
            }
        }
    }

    private function isSlugProvidedAndNounceValid(): bool
    {
        if (!isset($_POST['challenge_tools_nonce'])) {
            return false;
        }
        $nounce = sanitize_text_field(wp_unslash($_POST['challenge_tools_nonce']));
        return wp_verify_nonce($nounce, 'challenge_tools_nonce')
            && isset($_POST['users_slug']);
    }
}
