<?php declare(strict_types=1);

namespace Challenge\Managers;

use Challenge\Helpers\TemplateEngine;
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

    private function registerFiltersAndActions()
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
        $data = [
            'base_path' => $this->plugin->pluginUrl(),
        ];
        // Include the assets (CSS/JS)
        wp_enqueue_script('challenge-users', $this->plugin->pluginUrl() . 'assets/js/challenge.js');
        wp_localize_script('challenge-users', 'challenge', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('challenge-users')
        ]);
        // Attempts to find a theme local template
        $site_theme_template = locate_template(['challenge-users.php']);
        if (strlen($site_theme_template)) {
            // We load the template using buffers to protect the class
            $render = function($data) use ($site_theme_template) {
                ob_start();
                load_template($site_theme_template, true, $data);
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            };
            echo $render($data);
        } else {
            // If no custom template found use our own page template and styles
            wp_enqueue_style('challenge-users', $this->plugin->pluginUrl() . 'assets/css/challenge.css');
            get_header();
            // Render the template and pass the data
            $engine = new TemplateEngine($this->plugin->pluginDir());
            echo $engine->render('UsersPage', $data);
            get_footer();
        }
    }
}
