<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-13
 * Time: 14:34
 */

namespace Vinnia\SocialSearch\Test;

use Vinnia\SocialSearch\CompositeSearch;
use Vinnia\SocialSearch\Media;
use Vinnia\SocialSearch\SearchInterface;

class CompositeMockSearch implements SearchInterface {

    /**
     * @var Media[]
     */
    private $tagMedia;

    /**
     * @var Media[]
     */
    private $usernameMedia;

    /**
     * @param Media[] $tagMedia
     * @param Media[] $usernameMedia
     */
    function __construct(array $tagMedia, array $usernameMedia) {
        $this->tagMedia = $tagMedia;
        $this->usernameMedia = $usernameMedia;
    }

    /**
     * @param string $tag
     * @return Media[]
     */
    public function findByTag($tag) {
        return $this->tagMedia;
    }

    /**
     * @param string $username
     * @return Media[]
     */
    public function findByUsername($username) {
        return $this->usernameMedia;
    }
}

class CompositeSearchTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Media
     */
    public $m1;

    /**
     * @var Media
     */
    public $m2;

    public function setUp() {
        parent::setUp();

        $m1 = new Media();
        $m1->createdAt = 1000;
        $m2 = new Media();
        $m2->createdAt = 2000;

        $this->m1 = $m1;
        $this->m2 = $m2;
    }

    public function testFindByTag() {
        $mock1 = new CompositeMockSearch([$this->m1], []);
        $mock2 = new CompositeMockSearch([$this->m2], []);
        $comp = new CompositeSearch([$mock1, $mock2]);

        $all = $comp->findByTag('yolo');

        $this->assertCount(2, $all);
        $this->assertSame($this->m2, $all[0]);
        $this->assertSame($this->m1, $all[1]);
    }

    public function testFindByUsername() {
        $mock1 = new CompositeMockSearch([], [$this->m2, $this->m2]);
        $mock2 = new CompositeMockSearch([], [$this->m1]);
        $comp = new CompositeSearch([$mock1, $mock2]);

        $all = $comp->findByUsername('yolo');

        $this->assertCount(3, $all);
        $this->assertSame($this->m2, $all[0]);
        $this->assertSame($this->m2, $all[1]);
        $this->assertSame($this->m1, $all[2]);
    }

}
