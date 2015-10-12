<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 16:04
 */

namespace Vinnia\SocialSearch;

use MetzWeb\Instagram\Instagram;

class InstagramSearch implements SearchInterface {

    /**
     * @var Instagram
     */
    private $client;

    function __construct(Instagram $client) {
        $this->client = $client;
    }

    /**
     * @param string $tag
     * @return Media[]
     */
    public function findByTag($tag) {
        $result = $this->client->getTagMedia($tag, 20);

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
        $id = $this->getIdByUsername($username);

        // no user with the specified username was found
        if ( $id === null ) {
            return [];
        }

        $result = $this->client->getUserMedia($id, 100);

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
        $result = $this->client->searchUser($username, 1);

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

        $media = new Media();
        $media->source = Media::SOURCE_INSTAGRAM;
        $media->username = $item->user->username;
        $media->createdAt = (int) $item->created_time;

        switch ($item->type) {
            case 'image':
                $media->type = Media::TYPE_IMAGE;
                $media->data = $item->images->standard_resolution->url;
                break;
            case 'video':
                $media->type = Media::TYPE_VIDEO;
                $media->data = $item->videos->standard_resolution->url;
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown media type %s', $item->type));
        }

        return $media;
    }

}
