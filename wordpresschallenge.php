<?php

/**
 * Plugin Name: WordPress Challenge
 * Description: Challenge WP plugin for Inpsyde. It renders an external users table.
 * Version: 1.0
 * Author: Nestor Mata <nestor.mata@gmail.com>
 */

declare(strict_types=1);

use Challenge\WordPressChallengePlugin;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Initialize the plugin.
add_action('plugins_loaded', fn() => new WordPressChallengePlugin(
    __DIR__,
    plugin_dir_url(__FILE__)
));

register_activation_hook(__FILE__, 'flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
