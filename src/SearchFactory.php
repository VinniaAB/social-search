<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-13
 * Time: 14:49
 */

namespace Vinnia\SocialSearch;


use GuzzleHttp\Client;

class SearchFactory {

    /**
     * @param string $instagramClientId
     * @param string $twitterAppKey
     * @param string $twitterAppSecret
     * @return SearchInterface
     */
    public static function build($instagramClientId, $twitterAppKey, $twitterAppSecret) {
        $guzzle = new Client([
            'timeout' => 5
        ]);

        $twitterClient = new TwitterClient($guzzle, $twitterAppKey, $twitterAppSecret);

        $insta = new InstagramSearch($guzzle, $instagramClientId);
        $twitter = new TwitterSearch($twitterClient);

        return new CompositeSearch([$insta, $twitter]);
    }

}
