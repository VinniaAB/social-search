<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 15:36
 */

namespace Vinnia\SocialTools;


interface MediaSyncInterface {

    /**
     * @param string $tag tag to sync
     * @param int $since unix timestamp to start from
     * @param MediaStorageInterface $store storage to sync to
     * @return int number of synced items
     */
    public function run($tag, $since, MediaStorageInterface $store);

}
