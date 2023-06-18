<?php declare(strict_types=1);

namespace Challenge;

use Challenge\Managers\AdminSettingsManager;
use Challenge\Managers\APIManager;
use Challenge\Managers\UsersPageManager;

/**
 * The plugin's main class that registers the actions and other requirements for
 * the plugin to work.
 */
class WordPressChallengePlugin
{
    private string $pluginDir;
    private string $pluginUrl;
    private AdminSettingsManager $adminSettingsManager;
    private UsersPageManager $usersPageManager;
    private APIManager $apiManager;

    // Constructor.
    public function __construct(string $pluginDir, string $pluginUrl)
    {
        $this->pluginDir = $pluginDir;
        $this->pluginUrl = $pluginUrl;
        $this->adminSettingsManager = new AdminSettingsManager($this);
        $this->usersPageManager = new UsersPageManager($this);
        $this->apiManager = new APIManager($this);
        $this->registerFiltersAndActions();
    }

    public function pluginDir(): string
    {
        return $this->pluginDir;
    }

    public function pluginUrl(): string
    {
        return $this->pluginUrl;
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
