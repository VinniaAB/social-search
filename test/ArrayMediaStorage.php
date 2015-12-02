<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 16:43
 */

namespace Vinnia\SocialTools\Test;

use Vinnia\SocialTools\Media;
use Vinnia\SocialTools\MediaStorageInterface;
use Vinnia\SocialTools\MediaStorageQuery;

class ArrayMediaStorage implements MediaStorageInterface {

    /**
     * @var Media[]
     */
    public $cache = [];

    /**
     * @param Media[] $media
     * @return int number of saved medias
     */
    public function insert(array $media) {
        $this->cache = array_merge($this->cache, $media);

        return count($media);
    }

    /**
     * @param MediaStorageQuery $query
     * @return Media[]
     */
    public function query(MediaStorageQuery $query) {

        // filter tags
        $cache = array_filter($this->cache, function(Media $item) use ($query) {
            return count($query->tags) === 0 || count(array_intersect($item->tags, $query->tags)) !== 0;
        });

        // filter since
        $cache = array_filter($cache, function(Media $item) use ($query) {
            return !$query->since || $item->createdAt >= $query->since;
        });

        // filter until
        $cache = array_filter($cache, function(Media $item) use ($query) {
            return !$query->until || $item->createdAt <= $query->until;
        });

        return $cache;
    }

}
