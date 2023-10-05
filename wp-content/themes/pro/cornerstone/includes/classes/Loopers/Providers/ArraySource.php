<?php

namespace Themeco\Cornerstone\Loopers\Providers;

abstract class ArraySource extends Source {

  public function setup() {

    $items = $this->items();

    // Handle WP errors
    if ( is_wp_error( $items ) ) {
      $this->setError( $items );
      $items = [];
    }

    // If a single non array item is passed through we will consider it an array of a single item
    if ( ! is_array( $items ) || $this->hasStringKeys( $items ) ) {
      $items = [ $items ];
    }

    $items = array_values( $items );

    $offset = $this->setting( 'offset', 0 );
    $this->items = $offset === 0 ? $items : array_slice( $items, $offset );

    $this->setProperty('context', $this->items);
    $this->setProperty('size', count($this->items));
    $this->setProperty('index', 0);

  }

  abstract function items();

  public function hasItems() {
    return count($this->items) > 0;
  }

  public function next() {
    $result = array_shift($this->items);
    $this->setProperty('index', $this->getProperty('size') - (count($this->items) + 1));
    return $result;
  }

  public function rewind() {
    $this->items = $this->getProperty('context');
    $this->setProperty('index', 0);
  }


  public function hasStringKeys($input) {
    return count(array_filter(array_keys($input), 'is_string')) > 0;
  }
}
