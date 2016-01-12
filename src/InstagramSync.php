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
        $media->url = $item->link;

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
     * @param \Closure $getData closure that should return the items
     * @param $since
     * @param MediaStorageInterface $store
     * @return int
     */
    public function runInternal(\Closure $getData, $since, MediaStorageInterface $store) {
        $query = [
            'count' => 100
        ];
        $earliestTimestamp = time();
        $prevNextMaxTaxId = null;
        $qty = 0;
        do {
            $data = $getData($this->client, $query);
            $media = array_map([$this, 'toMedia'], $data->data);

            // find the smallest timestamp
            $timestamps = array_map(function($it) { return $it->createdAt; }, $media);
            $timestamps[] = $earliestTimestamp;
            $earliestTimestamp = min($timestamps);

            // since we are looping backwards, make sure we only include items that were created after the limit
            $media = array_filter($media, function($it) use ($since) { return $it->createdAt > $since; });

            $store->insert($media);
            $qty += count($media);

            // if there is less than a full page of grams, instagram skips this pagination property.
            if ( !isset($data->pagination->next_max_tag_id) ) {
                break;
            }

            $n = $data->pagination->next_max_tag_id;

            // prevent an infinite loop if we have reached the first gram
            if ( $n === $prevNextMaxTaxId ) {
                break;
            }

            // save max_tag_id for the next iteration
            $query['max_tag_id'] = $n;
            $prevNextMaxTaxId = $n;

        } while ( $earliestTimestamp > $since );

        return $qty;
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
    public function runWithTag($tag, $since, MediaStorageInterface $store) {
        $getData = function(InstagramClient $client, array $query) use ($tag) {
            return $client->tagsMediaRecent($tag, $query);
        };

        return $this->runInternal($getData, $since, $store);
    }

    /**
     * @param string $username username to sync
     * @param int $since unix timestamp to start from
     * @param MediaStorageInterface $store
     * @return int number of synced items
     */
    public function runWithUsername($username, $since, MediaStorageInterface $store) {
        // first we need to get the user id of this username
        $result = $this->client->usersSearch([
            'q' => $username,
        ]);

        if ( empty($result->data) ) {
            return 0;
        }

        $id = $result->data[0]->id;

        $getData = function(InstagramClient $client, array $query) use ($id) {
            return $client->usersMediaRecent($id, $query);
        };

        return $this->runInternal($getData, $since, $store);
    }
}
