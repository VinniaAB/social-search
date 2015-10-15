<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 16:04
 */

namespace Vinnia\SocialSearch;

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
    private $clientId;

    /**
     * @param ClientInterface $httpClient
     * @param string $clientId Instagram app client id
     */
    function __construct(ClientInterface $httpClient, $clientId) {
        $this->httpClient = $httpClient;
        $this->clientId = $clientId;
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
     * @param string $id
     * @param string[] $params
     * @return \stdClass
     */
    public function usersMediaRecent($id, array $params = []) {
        return $this->sendRequest('GET', "/users/{$id}/media/recent", [
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
        $opts = array_merge_recursive(['query' => ['client_id' => $this->clientId]], $options);
        $res = $this->httpClient->request($method, self::API_URL . $endpoint, $opts);
        return json_decode((string) $res->getBody());
    }

}
