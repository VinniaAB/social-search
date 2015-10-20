<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 17:04
 */

namespace Vinnia\SocialTools;


class DatabaseMediaStorage implements MediaStorageInterface {

    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @param DatabaseInterface $db
     */
    function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    /**
     * @return int
     */
    private function getLastId() {
        $sql = 'select max(vss_media_id) as maxId from vss_media';
        $result = $this->db->query($sql);

        if ( !$result ) {
            return 0;
        }

        return (int) $result['maxId'];
    }

    private function toMedia(array $mediaRow) {
        $media = new Media((int) $mediaRow['source']);
        $media->id = (int) $mediaRow['vss_media_id'];
        $media->originalId = $mediaRow['original_id'];
        $media->text = $mediaRow['text'];
        $media->images = json_decode($mediaRow['images']);
        $media->videos = json_decode($mediaRow['videos']);
        $media->lat = $mediaRow['lat'] ? (float) $mediaRow['lat'] : null;
        $media->long = $mediaRow['long'] ? (float) $mediaRow['long'] : null;
        $media->username = $mediaRow['username'];
        $media->createdAt = (int) $mediaRow['created_at'];

        return $media;
    }

    /**
     * @param int[] $mediaIds
     * @return string[][] multi-dimensional array of tags indexed by the media id
     */
    private function getTags(array $mediaIds) {

        $tags = array_fill_keys($mediaIds, []);

        $params = [];
        $values = [];

        $i = 0;
        foreach ( $mediaIds as $id ) {
            $key = ':param' . $i;
            $params[] = $key;
            $values[$key] = $id;
            $i++;
        }

        $str = implode(',', $params);

        $sql = "select * from vss_tag where vss_media_id in ({$str})";
        $rows = $this->db->queryAll($sql, $values);

        foreach ( $rows as $row ) {
            $id = (int) $row['vss_media_id'];
            $tags[$id][] = $row['name'];
        }

        return $tags;
    }

    /**
     * @param Media[] $media
     * @return int number of saved medias
     */
    public function insert(array $media) {

        $sql = <<<EOD
insert into vss_media(
  "source",
  "original_id",
  "text",
  "images",
  "videos",
  "lat",
  "long",
  "username",
  "created_at"
) values (
  :source,
  :originalId,
  :text,
  :images,
  :videos,
  :lat,
  :long,
  :username,
  :createdAt
)
EOD;

        $tagSql = <<<EOD
insert into vss_tag(
    name,
    vss_media_id
) values (
    :name,
    :id
)
EOD;


        $inserts = 0;
        foreach ( $media as $it ) {

            try {
                $this->db->execute($sql, [
                    ':source' => $it->getSource(),
                    ':originalId' => $it->originalId,
                    ':text' => $it->text,
                    ':images' => json_encode($it->images),
                    ':videos' => json_encode($it->videos),
                    ':lat' => $it->lat,
                    ':long' => $it->long,
                    ':username' => $it->username,
                    ':createdAt' => $it->createdAt
                ]);

                $maxId = $this->getLastId();

                foreach ( $it->tags as $tag ) {
                    $this->db->execute($tagSql, [
                        ':name' => $tag,
                        ':id' => $maxId
                    ]);
                }

                $inserts++;
            }
            catch ( \Exception $e ) {}

        }

        return $inserts;
    }

    /**
     * @param MediaStorageQuery $query
     * @return Media[]
     */
    public function query(MediaStorageQuery $query) {
        $sql = 'select * from vss_media';
        $data = $this->db->queryAll($sql);

        /* @var Media[] $media */
        $media = array_map([$this, 'toMedia'], $data);
        $tags = $this->getTags(array_map(function($it) { return $it->id; }, $media));

        foreach ( $media as $item ) {
            $item->tags = $tags[$item->id];
        }

        return $media;
    }

}
