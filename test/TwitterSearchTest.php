<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-12
 * Time: 18:22
 */

namespace Vinnia\SocialSearch\Test;

use Vinnia\SocialSearch\TwitterSearch;

class TwitterSearchTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var TwitterSearch
     */
    public $search;

    public function setUp() {
        parent::setUp();

        $this->search = new TwitterSearch();
    }

    public function testFindByUsername() {
        $this->fail('Wow such fail');
    }

    public function testFindByTag() {
        $this->fail('Wow such fail');
    }

}
