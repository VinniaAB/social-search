<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:22
 */

namespace Vinnia\SocialSearch;


class TwitterSearch implements SearchInterface {

    const API_URL = 'https://api.twitter.com/1.1';

    /**
     * @var TwitterClient
     */
    private $client;

    /**
     * @var int
     */
    private $resultCount;


    function __construct(TwitterClient $client, $resultCount = 25) {
        $this->client = $client;
        $this->resultCount = $resultCount;
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
        $params = [
            'q' => $query,
            'result_type' => 'recent',
            'count' => $this->resultCount
        ];

        $res = $this->client->searchTweets($params);
        $statuses = $res->statuses;

        return array_map([$this, 'tweetToMedia'], $statuses);
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

}
