<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class QueryRecentPosts extends QueryWp {

  public function setupQuery() {
    return new \WP_Query( $this->makeQueryArgsFromSettings());
  }
}