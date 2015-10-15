<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:22
 */

namespace Vinnia\SocialTools;

use GuzzleHttp\ClientInterface;

class TwitterClient {

    const API_URL = 'https://api.twitter.com/1.1';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param ClientInterface $httpClient
     * @param string $key
     * @param string $secret
     */
    function __construct(ClientInterface $httpClient, $key, $secret) {
        $this->httpClient = $httpClient;
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Make sure we have an access token before executing requests
     */
    protected function assertHasAccessToken() {
        if ( !$this->accessToken ) {
            $res = $this->getAccessToken();
            $this->accessToken = $res->access_token;
        }
    }

    /**
     * Get an access token from the Twitter OAuth service
     * Method designed from specification at https://dev.twitter.com/oauth/application-only
     * @return \stdClass
     */
    protected function getAccessToken() {
        $creds = rawurlencode($this->key) . ':' . rawurlencode($this->secret);

        $res = $this->httpClient->request('POST', 'https://api.twitter.com/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($creds),
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ],
            'body' => 'grant_type=client_credentials'
        ]);

        return json_decode((string) $res->getBody());
    }

    /**
     * @param string[] $params
     * @return \stdClass
     */
    public function searchTweets(array $params) {
        return $this->sendRequest('GET', '/search/tweets.json', [
            'query' => $params
        ]);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return \stdClass
     */
    protected function sendRequest($method, $endpoint, array $options = []) {
        $this->assertHasAccessToken();

        $opts = array_merge_recursive([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken
            ]
        ], $options);

        $res = $this->httpClient->request($method, self::API_URL . $endpoint, $opts);

        return json_decode((string) $res->getBody());
    }

}
