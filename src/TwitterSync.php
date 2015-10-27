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
        $media->url = "http://twitter.com/{$media->username}/statuses/{$media->originalId}";

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
     * @param string $tag tag to sync, not prefixed with #. ex: ['cars', 'boats']
     * @param int $since unix timestamp to start from
     * @param MediaStorageInterface $store storage to sync to
     * @return int number of synced items
     */
    public function run($tag, $since, MediaStorageInterface $store) {
        $query = [
            'q' => '#' . $tag . ' since:' . date('Y-m-d', $since) . ' -filter:retweets',
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
            $medias = array_filter($medias, function($it) use ($since) { return $it->createdAt > $since; });
            $n = $data->search_metadata->max_id;

            if ( $n === $nextMin ) {
                break;
            }

            $nextMin = $n;
            $store->insert($medias);
            $qty += count($medias);

        } while ( count($medias) !== 0 );

        return $qty;
    }
}
