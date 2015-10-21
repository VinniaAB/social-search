<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:51
 */

namespace Vinnia\SocialTools;


class Media {

    const SOURCE_INSTAGRAM = 0;
    const SOURCE_TWITTER = 1;
    const SOURCE_FACEBOOK = 2;
    const SOURCE_PERISCOPE = 3;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $source;

    /**
     * The original id of this item from the source
     * @var string
     */
    public $originalId;

    /**
     * @var string
     */
    public $text;

    /**
     * Array of image urls
     * @var string[]
     */
    public $images = [];

    /**
     * Array of video urls
     * @var string[]
     */
    public $videos = [];

    /**
     * @var string
     */
    public $username;

    /**
     * @var float
     */
    public $lat;

    /**
     * @var float
     */
    public $long;

    /**
     * @var string[]
     */
    public $tags = [];

    /**
     * @var int
     */
    public $createdAt;

    /**
     * Url to the original post
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $active = true;

    /**
     * @param int $source
     */
    function __construct($source) {
        if ( !self::isSourceValid($source) ) {
            throw new \InvalidArgumentException('Invalid source or type');
        }

        $this->source = $source;
    }

    public function getSource() {
        return $this->source;
    }

    /**
     * @return int[]
     */
    public static function getValidSources() {
        return [
            self::SOURCE_INSTAGRAM,
            self::SOURCE_TWITTER,
            self::SOURCE_FACEBOOK,
            self::SOURCE_PERISCOPE
        ];
    }

    /**
     * @param int $source
     * @return bool
     */
    public static function isSourceValid($source) {
        return in_array($source, self::getValidSources(), $strict = true);
    }

}
