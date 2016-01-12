<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 16:42
 */

namespace Vinnia\SocialTools\Test;

use Vinnia\DbTools\PDODatabase;
use Vinnia\SocialTools\DatabaseMediaStorage;
use Vinnia\SocialTools\MediaStorageQuery;
use Vinnia\SocialTools\MediaSyncInterface;

abstract class AbstractSyncTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var MediaSyncInterface
     */
    public $sync;

    public function setUp() {
        parent::setUp();

        $this->sync = $this->getMediaSyncObject();
    }

    /**
     * @return MediaSyncInterface
     */
    abstract public function getMediaSyncObject();

    /**
     * @return string[][]
     */
    abstract public function tagProvider();

    /**
     * @return string[][]
     */
    abstract public function usernameProvider();

    /**
     * @dataProvider tagProvider
     * @param string $tag
     * @param int $since timestamp
     */
    public function testSyncWithTag($tag, $since) {

        $dsn = $_ENV['DB_DSN'];
        $user = $_ENV['DB_USERNAME'];
        $pwd = $_ENV['DB_PASSWORD'];

        $db = PDODatabase::build($dsn, $user, $pwd);
        $db->execute('delete from vss_media');
        $store = new DatabaseMediaStorage($db);

        $this->sync->runWithTag($tag, $since, $store);

        $q = new MediaStorageQuery([
            'tags' => [$tag]
        ]);
        $items = $store->query($q);

        // this might fail if the query is too strict.
        $this->assertNotEmpty($items);

        foreach ( $items as $item ) {
            $t = array_map('strtolower', $item->tags);

            $this->assertTrue(in_array($tag, $t));
            $this->assertTrue($item->createdAt >= $since);
        }

        var_dump(count($items));
    }

    /**
     * @dataProvider usernameProvider
     * @param string $username
     * @param string $since
     */
    public function testSyncWithUsername($username, $since) {
        $dsn = $_ENV['DB_DSN'];
        $user = $_ENV['DB_USERNAME'];
        $pwd = $_ENV['DB_PASSWORD'];

        $db = PDODatabase::build($dsn, $user, $pwd);
        $db->execute('delete from vss_media');
        $store = new DatabaseMediaStorage($db);

        $this->sync->runWithUsername($username, $since, $store);

        $q = new MediaStorageQuery();

        $items = $store->query($q);

        $this->assertNotEmpty($items);

        foreach ( $items as $item ) {
            var_dump($item->text);
            $this->assertEquals($username, $item->username);
        }
    }

}
