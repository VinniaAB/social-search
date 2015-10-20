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
    private $cache = [];

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
        return $this->cache;
    }

}
