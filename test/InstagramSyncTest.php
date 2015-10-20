<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 18:32
 */

namespace Vinnia\SocialTools\Test;

use GuzzleHttp\Client;
use Vinnia\SocialTools\InstagramClient;
use Vinnia\SocialTools\InstagramSync;
use Vinnia\SocialTools\MediaSyncInterface;

class InstagramSyncTest extends AbstractSyncTest {

    /**
     * @return MediaSyncInterface
     */
    public function getMediaSyncObject() {
        $guzzle = new Client();
        $instagramClient = new InstagramClient($guzzle, $_ENV['INSTAGRAM_CLIENT_ID']);
        return new InstagramSync($instagramClient);
    }

    /**
     * @return string[][]
     */
    public function queryProvider() {
        return [
            ['schwarzenegger', strtotime('yesterday 12:00:00')]
        ];
    }
}
