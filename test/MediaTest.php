<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:14
 */

namespace Vinnia\SocialTools\Test;

use Vinnia\SocialTools\Media;

class MediaTest extends \PHPUnit_Framework_TestCase {

    public function validDataTypeProvider() {
        return [
            [Media::TYPE_TEXT, true],
            [Media::TYPE_IMAGE, true],
            [Media::TYPE_VIDEO, true],
            ['document', false]
        ];
    }

    /**
     * @dataProvider validDataTypeProvider
     * @param string $type
     * @param bool $valid
     */
    public function testIsValidType($type, $valid) {
        $this->assertEquals($valid, Media::isTypeValid($type));
    }

}
