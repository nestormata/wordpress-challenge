<?php

declare(strict_types=1);

namespace Challenge\Managers;

use Challenge\WordPressChallengePlugin;

/**
 * Handles the Users page that we show to the users.
 */
class UsersPageManager
{
    private WordPressChallengePlugin $plugin;

    public function __construct(WordPressChallengePlugin $plugin)
    {
        $this->plugin = $plugin;
        $this->registerFiltersAndActions();
    }

    public function registerFiltersAndActions()
    {
        add_action('template_redirect', [$this, 'listenCustomRoute']);
    }

    public function listenCustomRoute(): void
    {
        global $wp;
        if (get_query_var('custom_page')) {
            $this->customRoute();
            exit;
        }
    }

    private function customRoute()
    {
        // TODO: attempts to find a theme local template
        $site_theme_template = locate_template(['challenge-users.php']);
        // TODO: if no template found use our own page template
        // Include the assets (CSS/JS)
        echo 'TEST';
    }
}
