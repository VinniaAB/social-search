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
        $guzzle = new Client();

        $insta = new InstagramSearch($guzzle, $instagramClientId);
        $twitter = new TwitterSearch($guzzle, $twitterAppKey, $twitterAppSecret);

        return new CompositeSearch([$insta, $twitter]);
    }

}
