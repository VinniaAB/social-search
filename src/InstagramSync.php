<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 18:08
 */

namespace Vinnia\SocialTools;


class InstagramSync implements MediaSyncInterface {

    /**
     * @var InstagramClient
     */
    private $client;

    /**
     * @param InstagramClient $client
     */
    function __construct(InstagramClient $client) {
        $this->client = $client;
    }

    /**
     * @param \stdClass $item
     * @return Media
     */
    protected function toMedia(\stdClass $item) {
        $media = new Media(Media::SOURCE_INSTAGRAM);
        $media->originalId = $item->id;
        $media->username = $item->user->username;
        $media->createdAt = (int) $item->created_time;
        $media->tags = $item->tags;

        if ( $item->caption ) {
            $media->text = $item->caption->text;
        }

        if ( $item->location ) {
            $media->lat = $item->location->latitude;
            $media->long = $item->location->longitude;
        }

        switch ($item->type) {
            case 'image':
                $media->images[] = $item->images->standard_resolution->url;
                break;
            case 'video':
                $media->videos[] = $item->videos->standard_resolution->url;
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown media type %s', $item->type));
        }

        return $media;
    }

    /**
     * Instagarm does not have a combination of timestamp/tag endpoint. Instead we use their
     * tag endpoint from the current timestamp and loop backwards until we reach a timestamp
     * that is earlier than the $since parameter.
     *
     * @param string $tag tag to sync
     * @param int $since unix timestamp to start from
     * @param MediaStorageInterface $store storage to sync to
     * @return int number of synced items
     */
    public function run($tag, $since, MediaStorageInterface $store) {
        $query = [
            'count' => 100
        ];
        $earliestTimestamp = time();
        $nextMaxTagId = null;
        $qty = 0;
        do {

            // we're not on the first iteration - use pagination in the api and fetch earlier grams
            if ( $nextMaxTagId !== null ) {
                $query['max_tag_id'] = $nextMaxTagId;
            }

            $data = $this->client->tagsMediaRecent($tag, $query);
            $n = $data->pagination->next_max_tag_id;

            // prevent an infinite loop if we have reached the first gram
            if ( $n === $nextMaxTagId ) {
                break;
            }

            $nextMaxTagId = $n;
            $media = array_map([$this, 'toMedia'], $data->data);

            // find the smallest timestamp
            $timestamps = array_map(function($it) { return $it->createdAt; }, $media);
            $timestamps[] = $earliestTimestamp;
            $earliestTimestamp = min($timestamps);

            // since we are looping backwards, make sure we only include items that were created after the limit
            $media = array_filter($media, function($it) use ($since) { return $it->createdAt > $since; });

            $store->insert($media);

            $qty += count($media);

        } while ( $earliestTimestamp > $since );

        return $qty;
    }
}
