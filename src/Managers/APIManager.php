<?php

declare(strict_types=1);

namespace Challenge\Managers;

use Challenge\WordPressChallengePlugin;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Handle the communication witht he external API and serves the
 * internal requests to provide the data to the front end.
 */
class APIManager
{
    private WordPressChallengePlugin $plugin;

    /**
     * Construct and setup.
     */
    public function __construct(WordPressChallengePlugin $plugin)
    {
        $this->plugin = $plugin;
        $this->registerFiltersAndActions();
    }

    /**
     * Register the filters and actions required.
     */
    private function registerFiltersAndActions()
    {
        add_action('wp_ajax_challenge_users', [$this, 'fetchUsers']);
        add_action('wp_ajax_nopriv_challenge_users', [$this, 'fetchUsers']);
        add_action('wp_ajax_challenge_user', [$this, 'fetchUserData']);
        add_action('wp_ajax_nopriv_challenge_user', [$this, 'fetchUserData']);
    }

    /**
     * Fetch the list of users for the ajax request.
     */
    public function fetchUsers(): void
    {
        if (
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_REQUEST['nonce'] ?? '')),
                'challenge-users'
            )
        ) {
            die('Invalid nonce!');
        }
        $transientKey = 'challenge_users';
        // Check if cached first.
        $stored = get_transient($transientKey);
        if ($stored) {
            wp_send_json(unserialize($stored));
        }
        // Fetch and cache.
        $json = $this->fetchJson('users/');
        set_transient($transientKey, serialize($json), 60 * 60); // Cache for 1 hour
        wp_send_json($json);
    }

    /**
     * Fetch the information of a specific user for the ajax request.
     */
    public function fetchUserData(): void
    {
        if (
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_REQUEST['nonce'] ?? '')),
                'challenge-users'
            )
        ) {
            die('Invalid nonce!');
        }
        $userId = intval($_REQUEST['id'] ?? 0);
        // Check if cached first.
        $transientKey = 'challenge_user_' . $userId;
        $stored = get_transient($transientKey);
        if ($stored) {
            wp_send_json(unserialize($stored));
        }
        // Fetch and cache.
        $json = $this->fetchJson('users/' . $userId);
        set_transient($transientKey, serialize($json), 60 * 60); // Cache for 1 hour
        wp_send_json($json);
    }

    /**
     * Function to fetch of an endpoint from the source.
     */
    protected function fetch(string $endpoint, string $method = 'GET', array $data = []): Response
    {
        $client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com/',
        ]);
        $request = new Request($method, $endpoint, [], json_encode($data));
        $response = $client->send($request);
        return $response;
    }

    /**
     * Function to fetch JSON data from an endpoint of the source.
     */
    protected function fetchJson(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $response = $this->fetch($endpoint, $method, $data);
        $body = $response->getBody()->__toString();
        $result = json_decode($body, true);
        return $result;
    }
}
