<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class QueryString extends QueryWp {

  public function setupQuery() {
    return new \WP_Query( $this->setting( 'query', '' ) );
  }
}