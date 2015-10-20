<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 16:42
 */

namespace Vinnia\SocialTools\Test;

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
    abstract public function queryProvider();

    /**
     * @dataProvider queryProvider
     * @param string[] $tags
     * @param int $since timestamp
     */
    public function testSync($tags, $since) {

        $store = new ArrayMediaStorage();
        $this->sync->run($tags, $since, $store);

        $q = new MediaStorageQuery();
        $items = $store->query($q);

        // this might fail if the query is too strict.
        $this->assertNotEmpty($items);

        foreach ( $items as $item ) {
            $t = array_map('strtolower', $item->tags);

            $occurences = 0;
            foreach ( $tags as $expectedTag ) {
                $occurences += (int) in_array($expectedTag, $t);
            }

            $this->assertTrue($occurences !== 0);
            $this->assertTrue($item->createdAt >= $since);
        }
    }

}
