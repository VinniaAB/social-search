<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:51
 */

namespace Vinnia\SocialSearch;


class Media {

    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';

    const SOURCE_INSTAGRAM = 'instagram';
    const SOURCE_TWITTER = 'twitter';
    const SOURCE_FACEBOOK = 'facebook';
    const SOURCE_PERISCOPE = 'periscope';

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $data;

    /**
     * @var string
     */
    public $caption;

    /**
     * @var string
     */
    public $username;

    /**
     * @var int
     */
    public $createdAt;

}
