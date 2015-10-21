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
        $media->url = $mediaRow['url'];
        $media->active = (bool) $mediaRow['active'];

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
  source,
  original_id,
  `text`,
  images,
  videos,
  lat,
  `long`,
  username,
  created_at,
  url,
  active
) values (
  :source,
  :originalId,
  :text,
  :images,
  :videos,
  :lat,
  :long,
  :username,
  :createdAt,
  :url,
  :active
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
                    ':createdAt' => $it->createdAt,
                    ':url' => $it->url,
                    ':active' => (int) $it->active
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
        $where = ['vm.active = 1'];
        $join = [];
        $paramValues = [];
        if ( $query->since ) {
            $where[] = 'vm.created_at > :since';
            $paramValues[':since'] = $query->since;
        }

        if ( count($query->tags) !== 0 ) {

            $i = 0;
            $params = [];
            foreach ( $query->tags as $tag ) {
                $key = ':qp' . $i;
                $params[] = $key;
                $paramValues[$key] = $tag;
                $i++;
            }

            $paramString = implode(',', $params);

            $join[] = <<<EOD
inner join vss_tag vt
on vm.vss_media_id = vt.vss_media_id
and vt.name in ($paramString)
EOD;
        }

        $joins = implode(' ', $join);
        $wheres = implode(' and ', $where);

        if ( $wheres !== '' ) {
            $wheres = 'where ' . $wheres;
        }

        $sql = <<<EOD
select *
from vss_media vm
inner join (
    select vm.vss_media_id
    from vss_media vm
    {$joins}
    {$wheres}
    group by vm.vss_media_id
) t1
on t1.vss_media_id = vm.vss_media_id
order by vm.created_at asc
EOD;

        $data = $this->db->queryAll($sql, $paramValues);

        /* @var Media[] $media */
        $media = array_map([$this, 'toMedia'], $data);

        if ( count($media) !== 0 ) {
            $tags = $this->getTags(array_map(function($it) { return $it->id; }, $media));

            foreach ( $media as $item ) {
                $item->tags = $tags[$item->id];
            }
        }

        return $media;
    }

    public function createTables() {
        $sql = file_get_contents(__DIR__ . '/../schema.sql');
        $parts = explode(';', $sql);

        foreach ( $parts as $part ) {
            $part = trim($part);
            if ( !empty($part) ) {
                $this->db->execute($part);
            }
        }
    }

    public function dropTables() {
        $sql = <<<EOD
drop table vss_tag;
drop table vss_media;
EOD;
        $parts = explode(';', $sql);
        foreach ( $parts as $part ) {
            $part = trim($part);
            if ( !empty($part) ) {
                $this->db->execute($part);
            }
        }

    }

}
