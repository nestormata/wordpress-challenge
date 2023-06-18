<?php declare(strict_types=1);

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
            $usersSlug = preg_replace(
                '/[^a-z0-9_-]+/', '', strtolower($_POST['users_slug'] ?? '')
            );
            // If no slug given return error and don't save
            if (0 === strlen($usersSlug)) {
                $message = __('Please enter a slug');
                wp_safe_redirect(admin_url('tools.php?page=challenge&error=' . $message));
                wp_die();
                return;
            }
            // Save the new slug
            update_option('users_slug', $usersSlug);
            flush_rewrite_rules();
            $message = __('Data saved');
            wp_safe_redirect(admin_url('tools.php?page=challenge&message=' . $message));
            wp_die();
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
