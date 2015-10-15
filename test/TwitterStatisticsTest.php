<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-15
 * Time: 17:34
 */

namespace Vinnia\SocialTools\Test;


use GuzzleHttp\Client;
use Vinnia\SocialTools\TwitterClient;
use Vinnia\SocialTools\TwitterStatistics;

class TwitterStatisticsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var TwitterStatistics
     */
    public $stats;

    public function setUp() {
        parent::setUp();

        $key = $_ENV['TWITTER_KEY'];
        $secret = $_ENV['TWITTER_SECRET'];

        $client = new TwitterClient(new Client(), $key, $secret);
        $this->stats = new TwitterStatistics($client);
    }

    public function testGetTweetCountForTagBetween() {
        var_dump($this->stats->getTweetCountForTagBetween('schwarzenegger', '2011-01-01', '2015-01-01'));
    }

}
