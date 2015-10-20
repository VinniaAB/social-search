<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 16:19
 */

namespace Vinnia\SocialTools;


interface MediaStorageInterface {

    /**
     * @param Media[] $media
     * @return int number of saved medias
     */
    public function insert(array $media);

    /**
     * @param MediaStorageQuery $query
     * @return Media[]
     */
    public function query(MediaStorageQuery $query);

}
