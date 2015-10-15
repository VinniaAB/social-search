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

    /**
     * @var InstagramClient
     */
    private $client;

    /**
     * @var int
     */
    private $resultCount;

    /**
     * @param InstagramClient $client
     * @param int $resultCount
     */
    function __construct(InstagramClient $client, $resultCount = 25) {
        $this->client = $client;
        $this->resultCount = $resultCount;
    }

    /**
     * @param string $tag
     * @return Media[]
     */
    public function findByTag($tag) {
        $result = $this->client->tagsMediaRecent($tag, [
            'count' => $this->resultCount
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

        $result = $this->client->usersMediaRecent($id, [
            'count' => $this->resultCount
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
        $result = $this->client->usersSearch([
            'q' => $username,
            'count' => 1
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

}
