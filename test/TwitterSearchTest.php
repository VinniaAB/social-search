<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:22
 */

namespace Vinnia\SocialTools\Test;

use GuzzleHttp\Client;
use Vinnia\SocialTools\Media;
use Vinnia\SocialTools\TwitterClient;
use Vinnia\SocialTools\TwitterSearch;

class TwitterSearchTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var TwitterSearch
     */
    public $search;

    public function setUp() {
        parent::setUp();

        $key = $_ENV['TWITTER_KEY'];
        $secret = $_ENV['TWITTER_SECRET'];
        $guzzle = new Client();
        $twitterClient = new TwitterClient($guzzle, $key, $secret);
        $this->search = new TwitterSearch($twitterClient);
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
