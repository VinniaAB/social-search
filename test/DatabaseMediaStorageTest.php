<?php

/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 17:22
 */

namespace Vinnia\SocialTools\Test;

use Vinnia\SocialTools\DatabaseMediaStorage;
use Vinnia\SocialTools\Media;
use Vinnia\SocialTools\MediaStorageQuery;
use Vinnia\SocialTools\PDODatabase;
use PDO;

class DatabaseMediaStorageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var PDODatabase
     */
    public $db;

    /**
     * @var DatabaseMediaStorage
     */
    public $store;

    /**
     * @var Media
     */
    public $item;

    public function setUp() {
        parent::setUp();

        $dsn = $_ENV['DB_DSN'];
        $user = $_ENV['DB_USERNAME'];
        $pwd = $_ENV['DB_PASSWORD'];
        $pdo = new PDO($dsn, $user, $pwd, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        $this->db = new PDODatabase($pdo);

        $this->db->execute('delete from vss_media');

        $this->store = new DatabaseMediaStorage($this->db);

        $m = new Media(Media::SOURCE_INSTAGRAM);
        $m->originalId = '10000';
        $m->text = 'swag';
        $m->images = ['image.jpg'];
        $m->videos = ['video.mp4'];
        $m->lat = 40.0;
        $m->long = 30.0;
        $m->username = 'helmut';
        $m->createdAt = 100;
        $m->tags = ['swag', 'yolo'];

        $this->item = $m;
    }

    public function testInsertQuery() {
        $this->store->insert([$this->item]);

        $all = $this->store->query(new MediaStorageQuery());

        $this->assertCount(1, $all);

        $m1 = $all[0];

        $this->assertEquals(Media::SOURCE_INSTAGRAM, $m1->getSource());
        $this->assertEquals($this->item->originalId, $m1->originalId);
        $this->assertEquals($this->item->text, $m1->text);
        $this->assertEquals($this->item->images, $m1->images);
        $this->assertEquals($this->item->videos, $m1->videos);
        $this->assertEquals($this->item->lat, $m1->lat);
        $this->assertEquals($this->item->long, $m1->long);
        $this->assertEquals($this->item->username, $m1->username);
        $this->assertEquals($this->item->createdAt, $m1->createdAt);
        $this->assertEquals($this->item->tags, $m1->tags);
    }

    public function testInsertAlreadyExisting() {
        $qty = $this->store->insert([$this->item]);
        $this->assertEquals(1, $qty);
        $qty = $this->store->insert([$this->item]);
        $this->assertEquals(0, $qty);
    }

    public function queryProvider() {
        return [
            [new MediaStorageQuery(), ['123', '456', '600']],
            [new MediaStorageQuery(['tags' => ['boat']]), ['123']],
            [new MediaStorageQuery(['tags' => ['boat'], 'since' => 150]), []],
            [new MediaStorageQuery(['tags' => ['car'], 'since' => 149]), ['456', '600']],
            [new MediaStorageQuery(['tags' => ['horse', 'bike']]), ['456', '600']],
        ];
    }

    /**
     * @param MediaStorageQuery $query
     * @param int[] $expectedIds
     * @dataProvider queryProvider
     */
    public function testQuery(MediaStorageQuery $query, array $expectedIds) {
        $m = new Media(Media::SOURCE_INSTAGRAM);
        $m->tags = ['car', 'boat'];
        $m->username = 'Helmut';
        $m->originalId = '123';
        $m->createdAt = 100;

        $m2 = new Media(Media::SOURCE_TWITTER);
        $m2->tags = ['car', 'horse'];
        $m2->username = 'Helmut';
        $m2->originalId = '456';
        $m2->createdAt = 150;

        $m3 = new Media(Media::SOURCE_TWITTER);
        $m3->tags = ['car', 'bike'];
        $m3->username = 'Helmut';
        $m3->originalId = '600';
        $m3->createdAt = 200;

        $this->store->insert([$m, $m2, $m3]);

        $res = $this->store->query($query);

        $len = count($expectedIds);
        $this->assertCount($len, $res);

        for ( $i = 0; $i < $len; $i++ ) {
            $this->assertEquals($expectedIds[$i], $res[$i]->originalId);
        }
    }

}
