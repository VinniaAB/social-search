<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 14:55
 */

namespace Vinnia\SocialTools\Test;

use GuzzleHttp\Client;
use Vinnia\SocialTools\InstagramClient;
use Vinnia\SocialTools\Media;
use Vinnia\SocialTools\InstagramSearch;

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

        $guzzle = new Client();
        $instagramClient = new InstagramClient($guzzle, $_ENV['INSTAGRAM_CLIENT_ID']);
        $this->search = new InstagramSearch($instagramClient);
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