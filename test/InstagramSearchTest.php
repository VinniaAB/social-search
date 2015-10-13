<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:55
 */

namespace Vinnia\SocialSearch\Test;

use GuzzleHttp\Client;
use Vinnia\SocialSearch\Media;
use Vinnia\SocialSearch\InstagramSearch;

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

        //$insta = new Instagram($_ENV['INSTAGRAM_CLIENT_ID']);
        $guzzle = new Client();
        $this->search = new InstagramSearch($guzzle, $_ENV['INSTAGRAM_CLIENT_ID']);
    }

    public function testFindByTag() {
        $result = $this->search->findByTag('swag');

        $this->assertInstanceOf(Media::class, $result[0]);
    }

    public function testGetIdByUsername() {
        $id = $this->search->getIdByUsername($this->seolhyun);

        $this->assertEquals(2009373206, $id);
    }

    public function testFindByUsername() {
        $media = $this->search->findByUsername($this->seolhyun);

        $this->assertInstanceOf(Media::class, $media[0]);
    }

}