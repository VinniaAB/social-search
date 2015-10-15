<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2015-08-08
 * Time: 17:40
 */

namespace Vinnia\SocialTools;

class Collection {

    /**
     * @var array
     */
    private $items;

    /**
     * @param array $items
     */
    function __construct(array $items) {
        $this->items = $items;
    }

    /**
     * @param Callable $predicate
     * @return self
     */
    public function map(Callable $predicate) {
        return new self(array_map($predicate, $this->items));
    }

    /**
     * Filter an array with the supplied predicate. If $predicate is a function,
     * it will be called on each item to determine its thruthiness. If $predicate
     * is a string it is assumed we are looping over an array of objects or arrays.
     * $predicate them represents the attribute name/array key that will determine
     * each item's truthiness.
     * @param Callable|string $predicate
     * @return self
     */
    public function filter($predicate) {
        if ( is_string($predicate) ) {
            $func = function($item) use ($predicate) {
                $item = (object) $item;
                return $item->{$predicate};
            };

            return new self(array_filter($this->items, $func));
        }

        return new self(array_filter($this->items, $predicate));
    }

    /**
     * Loop over an array of objects/arrays and extract the specified key
     * @param string $attribute
     * @return self
     */
    public function pluck($attribute) {
        return $this->map(function($item) use ($attribute) {
            $item = (object) $item;
            return $item->{$attribute};
        });
    }

    /**
     * @return array
     */
    public function all() {
        return $this->items;
    }

}
