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
    public function queryProvider() {
        return [
            [['schwarzenegger', 'ladygaga'], strtotime('now -1 day')]
        ];
    }

}
