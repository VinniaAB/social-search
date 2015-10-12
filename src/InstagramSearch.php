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
    public function searchByTag($tag) {
        $items = $this->client->getTagMedia($tag);
        $mediaCollection = new Collection($items);

        // TODO: map to Media object
        return $mediaCollection->all();
    }

    /**
     * @param string $username
     * @return Media[]
     */
    public function searchByUsername($username) {
        $users = $this->client->searchUser($username);

        // no user with the specified username was found
        if ( count($users) === 0 ) {
            return [];
        }

        /* @var int $id */
        $id = $users[0]->id;
        $media = $this->client->getUserMedia($id);

        $mediaCollection = new Collection($media);

        // TODO: map to Media object
        return $mediaCollection->all();
    }

}
