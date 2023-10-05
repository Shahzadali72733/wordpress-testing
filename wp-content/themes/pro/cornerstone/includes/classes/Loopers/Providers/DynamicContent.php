<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class DynamicContent {

  public function setup() {

    $result = [];

    // Allow Dynamic Content to return a WP_Query object and have it converted to the correct provider type
    if ( is_a($result, \WP_Query::class) ) {
      return $this->proxy([ 'type' => 'query-wp', 'wp-query' => $result ]);
    }

    return $result;

  }
}