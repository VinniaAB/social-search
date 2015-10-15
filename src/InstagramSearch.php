<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 16:04
 */

namespace Vinnia\SocialSearch;

use GuzzleHttp\ClientInterface;

class InstagramSearch implements SearchInterface {

    const API_URL = 'https://api.instagram.com/v1';
    const REQUEST_TIMEOUT = 5;

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
     * @return Media[]
     */
    public function findByTag($tag) {
        $result = $this->sendRequest('GET', "/tags/{$tag}/media/recent", [
            'query' => ['count' => 25],
            'timeout' => self::REQUEST_TIMEOUT
        ]);

        $mediaCollection = new Collection($result->data);

        return $mediaCollection->map(function($item) {
            return $this->responseItemToMedia($item);
        })->all();
    }

    /**
     * @param string $username
     * @return Media[]
     */
    public function findByUsername($username) {
        $id = $this->getIdByUsername('GET', $username);

        // no user with the specified username was found
        if ( $id === null ) {
            return [];
        }

        $result = $this->sendRequest('GET', "/users/{$id}/media/recent", [
            'query' => ['count' => 25],
            'timeout' => self::REQUEST_TIMEOUT
        ]);

        $mediaCollection = new Collection($result->data);

        return $mediaCollection->map(function($item) {
            return $this->responseItemToMedia($item);
        })->all();
    }

    /**
     * @param string $username
     * @return string|null
     */
    public function getIdByUsername($username) {
        $result = $this->sendRequest('GET', '/users/search', [
            'query' => [
                'q' => $username,
                'count' => 1,
            ],
            'timeout' => self::REQUEST_TIMEOUT
        ]);

        // no user with the specified username was found
        if ( count($result->data) === 0 ) {
            return null;
        }

        return $result->data[0]->id;
    }

    /**
     * @param \stdClass $item
     * @return Media
     */
    protected function responseItemToMedia($item) {

        $media = null;

        switch ($item->type) {
            case 'image':
                $media = new Media(Media::SOURCE_INSTAGRAM, Media::TYPE_IMAGE);
                $media->data = $item->images->standard_resolution->url;
                break;
            case 'video':
                $media = new Media(Media::SOURCE_INSTAGRAM, Media::TYPE_VIDEO);
                $media->data = $item->videos->standard_resolution->url;
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown media type %s', $item->type));
        }

        $media->username = $item->user->username;
        $media->createdAt = (int) $item->created_time;

        return $media;
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
