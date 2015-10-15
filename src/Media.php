<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:51
 */

namespace Vinnia\SocialTools;


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
    private $source;

    /**
     * @var int
     */
    private $type;

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
     * @param int $source
     * @param int $type
     */
    function __construct($source, $type) {
        if ( !self::isSourceValid($source) || !self::isTypeValid($type) ) {
            throw new \InvalidArgumentException('Invalid source or type');
        }

        $this->source = $source;
        $this->type = $type;
    }

    public function getSource() {
        return $this->source;
    }

    public function getType() {
        return $this->type;
    }

    /**
     * @return int[]
     */
    public static function getValidTypes() {
        return [self::TYPE_TEXT, self::TYPE_IMAGE, self::TYPE_VIDEO];
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
     * @param int $type
     * @return bool
     */
    public static function isTypeValid($type) {
        return in_array($type, self::getValidTypes(), $strict = true);
    }

    /**
     * @param int $source
     * @return bool
     */
    public static function isSourceValid($source) {
        return in_array($source, self::getValidSources(), $strict = true);
    }

}
