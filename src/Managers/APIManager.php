<?php declare(strict_types=1);

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

    public function __construct(WordPressChallengePlugin $plugin)
    {
        $this->plugin = $plugin;
        $this->registerFiltersAndActions();
    }

    private function registerFiltersAndActions()
    {
        add_action('wp_ajax_challenge_users', [$this, 'fetchUsers']);
        add_action('wp_ajax_nopriv_challenge_users', [$this, 'fetchUsers']);
        add_action('wp_ajax_challenge_user', [$this, 'fetchUserData']);
        add_action('wp_ajax_nopriv_challenge_user', [$this, 'fetchUserData']);
    }

    public function fetchUsers(): void
    {
        wp_send_json($this->fetchJson('users/'));
    }

    public function fetchUserData(): void
    {
        $userId = intval($_REQUEST['id'] ?? 0);
        wp_send_json($this->fetchJson('users/' . $userId));
    }

    protected function fetch($endpoint, $method = 'GET', $data = []): Response
    {
        $client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com/',
        ]);
        $request = new Request($method, $endpoint, [], json_encode($data));
        $response = $client->send($request);
        return $response;
    }

    protected function fetchJson($endpoint, $method = 'GET', $data = []): array
    {
        $response = $this->fetch($endpoint, $method, $data);
        $body = $response->getBody()->__toString();
        $result = json_decode($body, true);
        return $result;
    }
}
