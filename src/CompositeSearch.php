<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:33
 */

namespace Vinnia\SocialSearch;


class CompositeSearch implements SearchInterface {

    /**
     * @var SearchInterface[]
     */
    private $delegates;

    /**
     * @param SearchInterface[] $delegates
     */
    function __construct(array $delegates) {
        $this->delegates = $delegates;
    }

    /**
     * Sort the objects with the latest first
     * @param Media[] $media
     */
    protected function sortMedia(array &$media) {
        usort($media, function($a, $b) {
            return $a->createdAt < $b->createdAt;
        });
    }

    /**
     * @param string $tag
     * @return Media[]
     */
    public function findByTag($tag) {
        $data = [];

        foreach ( $this->delegates as $delegate ) {
            $data = array_merge($data, $delegate->findByTag($tag));
        }

        $this->sortMedia($data);

        return $data;
    }

    /**
     * @param string $username
     * @return Media[]
     */
    public function findByUsername($username) {
        $data = [];

        foreach ( $this->delegates as $delegate ) {
            $data = array_merge($data, $delegate->findByUsername($username));
        }

        $this->sortMedia($data);

        return $data;
    }
}
