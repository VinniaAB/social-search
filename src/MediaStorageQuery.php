<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 16:59
 */

namespace Vinnia\SocialTools;


class MediaStorageQuery {

    /**
     * @var string[]
     */
    public $tags = [];

    /**
     * @var int unix timestamp
     */
    public $since;

    function __construct(array $params = []) {
        foreach ( $params as $key => $value ) {
            $this->{$key} = $value;
        }
    }

}
