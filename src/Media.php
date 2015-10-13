<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:51
 */

namespace Vinnia\SocialSearch;


class Media {

    const TYPE_TEXT = 0;
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    const SOURCE_INSTAGRAM = 0;
    const SOURCE_TWITTER = 1;
    const SOURCE_FACEBOOK = 2;
    const SOURCE_PERISCOPE = 3;

    /**
     * @var int
     */
    public $source;

    /**
     * @var int
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

    /**
     * @return string[]
     */
    public static function getValidTypes() {
        return [self::TYPE_TEXT, self::TYPE_IMAGE, self::TYPE_VIDEO];
    }

    /**
     * @param string $type
     * @return string|null
     */
    public static function isTypeValid($type) {
        return in_array($type, self::getValidTypes(), $strict = true);
    }

}
