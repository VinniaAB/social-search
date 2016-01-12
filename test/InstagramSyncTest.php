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
        $instagramClient = new InstagramClient($guzzle, $_ENV['INSTAGRAM_ACCESS_TOKEN']);
        return new InstagramSync($instagramClient);
    }

    /**
     * @return string[][]
     */
    public function tagProvider() {
        return [
            ['schwarzenegger', strtotime('yesterday 12:00:00')]
        ];
    }

    /**
     * @return string[][]
     */
    public function usernameProvider() {
        return [
            ['joakimcarlsten', strtotime('2015-12-01 12:00:00')],
        ];
    }
}
