<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:55
 */

namespace Vinnia\SocialSearch\Test;

use Vinnia\SocialSearch\Media;
use Vinnia\SocialSearch\InstagramSearch;
use MetzWeb\Instagram\Instagram;

class InstagramSearchTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var InstagramSearch
     */
    public $search;

    /**
     * @var string
     */
    public $seolhyun = 'sh_9513';

    public function setUp() {
        parent::setUp();

        $insta = new Instagram($_ENV['INSTAGRAM_CLIENT_ID']);
        $this->search = new InstagramSearch($insta);
    }

    public function testSearchByTag() {
        $result = $this->search->searchByTag('swag');

        $this->assertInstanceOf(Media::class, $result[0]);
    }

    public function testGetIdByUsername() {
        $id = $this->search->getIdByUsername($this->seolhyun);

        $this->assertEquals(2009373206, $id);
    }

    public function testSearchByUsername() {
        $media = $this->search->searchByUsername($this->seolhyun);

        $this->assertInstanceOf(Media::class, $media[0]);
    }

}