<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-15
 * Time: 17:24
 */

namespace Vinnia\SocialTools;


class TwitterStatistics {

    /**
     * @var TwitterClient
     */
    private $client;

    function __construct(TwitterClient $client) {
        $this->client = $client;
    }

    public function getTweetCountForTagBetween($tag, $start, $end) {
        $res = $this->client->searchTweets([
            'include_entities' => 'false',
            'count' => 100,
            'since_id' => 1000,
            'q' => "#{$tag}"
        ]);

        var_dump($res);

        return (int) $res->search_metadata->count;
    }

}
