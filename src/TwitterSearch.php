<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:22
 */

namespace Vinnia\SocialSearch;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class TwitterSearch implements SearchInterface {

    const API_URL = 'https://api.twitter.com/1.1';
    const REQUEST_TIMEOUT = 5;

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
            'body' => 'grant_type=client_credentials',
            'timeout' => self::REQUEST_TIMEOUT
        ]);

        return json_decode((string) $res->getBody());
    }

    /**
     * @param string $tag
     * @return Media[]
     */
    public function findByTag($tag) {
        return $this->searchTweets('#' . $tag . ' exclude:retweets');
    }

    /**
     * @param string $username
     * @return Media[]
     */
    public function findByUsername($username) {
        return $this->searchTweets('from:' . $username . ' exclude:retweets');
    }

    /**
     * @param string $query
     * @return Media[]
     */
    protected function searchTweets($query) {
        $res = $this->sendRequest('GET', '/search/tweets.json', [
            'query' => [
                'q' => $query,
                'result_type' => 'recent',
                'count' => 25
            ],
            'timeout' => self::REQUEST_TIMEOUT
        ]);

        $statuses = $res->statuses;
        $statusCollection = new Collection($statuses);

        return $statusCollection->map(function($item) {
            return $this->tweetToMedia($item);
        })->all();
    }

    /**
     * Convert a tweet to a media object
     * @param \stdClass $tweet
     * @return Media
     */
    protected function tweetToMedia($tweet) {

        if ( isset($tweet->entities->media) && $tweet->entities->media[0]->type === 'photo' ) {
            $media = new Media(Media::SOURCE_TWITTER, Media::TYPE_IMAGE);
            $media->data = $tweet->entities->media[0]->media_url_https;
            $media->caption = $tweet->text;
        }
        else {
            $media = new Media(Media::SOURCE_TWITTER, Media::TYPE_TEXT);
            $media->data = $tweet->text;
        }

        $media->username = $tweet->user->screen_name;
        $media->createdAt = strtotime($tweet->created_at);

        return $media;
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
