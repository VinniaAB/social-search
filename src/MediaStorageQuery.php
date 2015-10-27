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
     * @var int unix timestamp. Results will have a created_at value larger than this.
     */
    public $since;

    /**
     * @var int unix timestamp. Results will have a created_at value less than this.
     */
    public $until;

    /**
     * @var int maximum count
     */
    public $count;

    function __construct(array $params = []) {
        foreach ( $params as $key => $value ) {
            $this->{$key} = $value;
        }
    }

}
