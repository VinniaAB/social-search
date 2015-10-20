<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 15:44
 */

namespace Vinnia\SocialTools;


class TwitterSync implements MediaSyncInterface {

    /**
     * @var TwitterClient
     */
    private $client;

    function __construct(TwitterClient $client) {
        $this->client = $client;
    }

    /**
     * @param \stdClass $tweet
     * @return Media
     */
    private function toMedia(\stdClass $tweet) {
        $media = new Media(Media::SOURCE_TWITTER);
        $media->originalId = $tweet->id_str;
        $media->text = $tweet->text;
        $media->username = $tweet->user->screen_name;
        $media->createdAt = strtotime($tweet->created_at);

        if ( isset($tweet->entities->hashtags) && $tweet->entities->hashtags ) {
            $hashTags = $tweet->entities->hashtags;

            foreach ( $hashTags as $tag ) {
                $media->tags[] = $tag->text;
            }
        }

        if ( isset($tweet->entities->media) && $tweet->entities->media ) {
            $tweetMedia = $tweet->entities->media;

            foreach ( $tweetMedia as $m ) {
                switch ( $m->type ) {
                    case 'photo':
                        $media->images[] = $m->media_url_https;
                        break;
                    case 'video':
                        $media->videos[] = $m->media_url_https;
                        break;
                }
            }
        }

        if ( isset($tweet->coordinates) && $tweet->coordinates ) {
            $media->lat = $tweet->coordinates->coordinates[1];
            $media->long = $tweet->coordinates->coordinates[0];
        }

        return $media;
    }

    /**
     * @param string[] $tag tags to sync, not prefixed with #. ex: ['cars', 'boats']
     * @param int $since unix timestamp to start from
     * @param MediaStorageInterface $store storage to sync to
     * @return int number of synced items
     */
    public function run($tag, $since, MediaStorageInterface $store) {
        $query = [
            'q' => '#' . $tag . ' since:' . date('Y-m-d', $since),
            'count' => 100
        ];

        $nextMin = null;
        $qty = 0;
        do {
            // we're not on the first iteration anymore - utilize pagination in the api
            if ( $nextMin !== null ) {
                $query['since_id'] = $nextMin;
            }

            $data = $this->client->searchTweets($query);
            $medias = array_map([$this, 'toMedia'], $data->statuses);
            $nextMin = $data->search_metadata->max_id;
            $store->insert($medias);
            $qty += count($medias);

        } while ( count($medias) !== 0 );

        return $qty;
    }
}
