<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 15:51
 */

namespace Vinnia\SocialTools\Test;

use GuzzleHttp\Client;
use Vinnia\SocialTools\TwitterSync;
use Vinnia\SocialTools\TwitterClient;

class TwitterSyncTest extends AbstractSyncTest {

    public function getMediaSyncObject() {

        $key = $_ENV['TWITTER_KEY'];
        $secret = $_ENV['TWITTER_SECRET'];
        $guzzle = new Client();
        $twitterClient = new TwitterClient($guzzle, $key, $secret);

        return new TwitterSync($twitterClient);
    }

    /**
     * @return string[][]
     */
    public function tagProvider() {
        return [
            ['schwarzenegger', strtotime('yesterday 12:00:00')],
        ];
    }

    /**
     * @return string[][]
     */
    public function usernameProvider() {
        return [
            ['PoppyDrayton', strtotime('2015-06-01 00:00:00')],
        ];
    }
}
