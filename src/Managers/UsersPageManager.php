<?php

declare(strict_types=1);

namespace Challenge\Managers;

use Challenge\Helpers\TemplateEngine;
use Challenge\WordPressChallengePlugin;

/**
 * Handles the Users page that we show to the users.
 */
class UsersPageManager
{
    private WordPressChallengePlugin $plugin;

    /**
     * Construct the instance and makes the setup.
     */
    public function __construct(WordPressChallengePlugin $plugin)
    {
        $this->plugin = $plugin;
        $this->registerFiltersAndActions();
    }

    /**
     * Register the necesary filters and actions.
     */
    private function registerFiltersAndActions(): void
    {
        add_action('template_redirect', [$this, 'listenCustomRoute']);
        add_filter('script_loader_tag', [$this, 'convertScriptToModuleType'], 10, 3);
    }

    /**
     * Alters the JavaScript tag to set the type as "module" so the reactive code can work.
     */
    public function convertScriptToModuleType(string $tag, string $handle, string $src): string
    {
        // We convert the script tag to module type so we can load the App component.
        if ('challenge-users' === $handle) {
            // I disable the rule because it was enqueued, I'm adding the type module.
            // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
            $tag = '<script type="module" src="'
                . esc_url($src)
                . '" id="' . $handle . '-js"></script>'
                . PHP_EOL;
        }
        return $tag;
    }

    /**
     * Checks if we are in the custom URL assigned for the users page in order to render it.
     */
    public function listenCustomRoute(): void
    {
        global $wp;
        if (get_query_var('custom_page')) {
            $this->handleUsersPage();
            die;
        }
    }

    /**
     * The actual handler for the users page on the custom route.
     */
    private function handleUsersPage(): void
    {
        $data = [];
        // Include the assets (CSS/JS)
        wp_enqueue_script(
            'challenge-users',
            $this->plugin->pluginUrl() . 'assets/js/ChallengeModule.js',
            [],
            $this->plugin->version(),
            true
        );
        wp_localize_script('challenge-users', 'challenge', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('challenge-users'),
        ]);
        // Attempts to find a theme local template
        $siteThemeTemplate = locate_template(['challenge-users.php']);
        if (strlen($siteThemeTemplate)) {
            // We load the template using buffers to protect the class
            $render = static function (array $data) use ($siteThemeTemplate): string {
                ob_start();
                load_template($siteThemeTemplate, true, $data);
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            };
            // I disable the rule because the I'm outputing a template.
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $render($data);
            return;
        }
        // If no custom template found use our own page template and styles
        wp_enqueue_style(
            'challenge-users',
            $this->plugin->pluginUrl() . 'assets/css/challenge.css',
            [],
            $this->plugin->version()
        );
        get_header();
        // Render the template and pass the data
        $engine = new TemplateEngine($this->plugin->pluginDir());
        // I disable the rule because the template engine is escaping output.
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $engine->render('UsersPage', $data);
        get_footer();
    }
}
