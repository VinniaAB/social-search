<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:22
 */

namespace Vinnia\SocialSearch\Test;

use GuzzleHttp\Client;
use Vinnia\SocialSearch\Media;
use Vinnia\SocialSearch\TwitterSearch;

class TwitterSearchTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var TwitterSearch
     */
    public $search;

    public function setUp() {
        parent::setUp();

        $guzzle = new Client();
        $key = $_ENV['TWITTER_KEY'];
        $secret = $_ENV['TWITTER_SECRET'];
        $this->search = new TwitterSearch($guzzle, $key, $secret);
    }

    public function testFindByUsername() {
        $media = $this->search->findByUsername('JohnMayer');

        $this->assertInstanceOf(Media::class, $media[0]);
    }

    public function testFindByTag() {
        $media = $this->search->findByTag('swag');

        $this->assertInstanceOf(Media::class, $media[0]);
    }

}
