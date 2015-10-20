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

class DatabaseMediaStorageTest extends \PHPUnit_Framework_TestCase {

    public $db;

    /**
     * @var DatabaseMediaStorage
     */
    public $store;

    public function setUp() {
        parent::setUp();

        $dsn = $_ENV['DB_DSN'];
        $user = $_ENV['DB_USERNAME'];
        $pwd = $_ENV['DB_PASSWORD'];
        $pdo = new \PDO($dsn, $user, $pwd);

        $this->db = new PDODatabase($pdo);

        $this->db->execute('delete from vss_media');

        $this->store = new DatabaseMediaStorage($this->db);
    }

    public function testInsertQuery() {
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

        $this->store->insert([$m]);

        $all = $this->store->query(new MediaStorageQuery());

        $this->assertCount(1, $all);

        $m1 = $all[0];

        $this->assertEquals(Media::SOURCE_INSTAGRAM, $m1->getSource());
        $this->assertEquals($m->originalId, $m1->originalId);
        $this->assertEquals($m->text, $m1->text);
        $this->assertEquals($m->images, $m1->images);
        $this->assertEquals($m->videos, $m1->videos);
        $this->assertEquals($m->lat, $m1->lat);
        $this->assertEquals($m->long, $m1->long);
        $this->assertEquals($m->username, $m1->username);
        $this->assertEquals($m->createdAt, $m1->createdAt);
        $this->assertEquals($m->tags, $m1->tags);
    }

}
