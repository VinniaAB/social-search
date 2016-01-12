<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 16:04
 */

namespace Vinnia\SocialTools;

use GuzzleHttp\ClientInterface;

class InstagramClient {

    const API_URL = 'https://api.instagram.com/v1';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param ClientInterface $httpClient
     * @param string $accessToken instagram access token
     */
    function __construct(ClientInterface $httpClient, $accessToken) {
        $this->httpClient = $httpClient;
        $this->accessToken = $accessToken;
    }

    /**
     * @param string $tag
     * @param string[] $params
     * @return \stdClass
     */
    public function tagsMediaRecent($tag, array $params = []) {
        return $this->sendRequest('GET', "/tags/{$tag}/media/recent", [
            'query' => $params
        ]);
    }

    /**
     * @param string $id user id. note that this is not the username.
     * @param string[] $params
     * @return \stdClass
     */
    public function usersMediaRecent($id, array $params = []) {
        return $this->sendRequest('GET', "/users/{$id}/media/recent/", [
            'query' => $params
        ]);
    }

    /**
     * @param string[] $params
     * @return \stdClass
     */
    public function usersSearch(array $params = []) {
        return $this->sendRequest('GET', '/users/search', [
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
        $opts = array_merge_recursive($options, [
            'query' => [
                'access_token' => $this->accessToken,
            ],
        ]);
        $res = $this->httpClient->request($method, self::API_URL . $endpoint, $opts);

        return json_decode((string) $res->getBody());
    }

}
