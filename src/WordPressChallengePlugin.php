<?php

declare(strict_types=1);

namespace Challenge;

use Challenge\Managers\AdminSettingsManager;
use Challenge\Managers\UsersPageManager;

/**
 * The plugin's main class that registers the actions and other requirements for
 * the plugin to work.
 */
class WordPressChallengePlugin
{
    private string $pluginDir;
    private AdminSettingsManager $adminSettingsManager;
    private UsersPageManager $usersPageManager;

    // Constructor.
    public function __construct(string $pluginDir)
    {
        // FIXME: Wiring the directory for the symlink in dev, back to the variable when done
        $this->pluginDir = '/var/www/html/wp-content/plugins/wordpress-challenge';//$pluginDir;
        $this->adminSettingsManager = new AdminSettingsManager($this);
        $this->usersPageManager = new UsersPageManager($this);
        $this->registerFiltersAndActions();
    }

    public function pluginDir(): string
    {
        return $this->pluginDir;
    }

    /**
     * Register the required filters and actions.
     */
    public function registerFiltersAndActions(): void
    {
        add_filter('query_vars', [$this, 'registerQueryVars']);
        add_action('init', [$this, 'registerCustomRoute']);
    }

    /**
     * Register this plugin required query vars.
     */
    public function registerQueryVars(array $queryVars): array
    {
        $queryVars[] = 'custom_page';
        return $queryVars;
    }

    /**
     * Register the rewrite rules for the custom route.
     */
    public function registerCustomRoute(): void
    {
        $slug = get_option('users_slug', 'users');
        add_rewrite_rule('^' . $slug . '/?$', 'index.php?custom_page=1', 'top');
    }
}
