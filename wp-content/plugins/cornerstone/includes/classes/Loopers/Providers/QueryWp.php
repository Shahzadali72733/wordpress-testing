<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class QueryWp extends Source {

  private $previousWpQuery;

  public function setupQuery() {
    $query = $this->setting('wp-query', null);

    if ( ! is_a($query, \WP_Query::class) ) {
      $this->setError( 'No WP_Query found' );
    }

    return $query;

  }

  public function setup() {

  }

  public function hasItems() {
    return $this->query->have_posts();
  }

  public function next() {
    $this->query->the_post();
    global $post;
    return $post;
  }

  public function rewind() {
    $this->query->rewind_posts();
  }

  public function begin() {

    $this->query = $this->setupQuery();
    if ( is_a($this->query, \WP_Query::class)) {

      $previousWpQuery = $this->getProperty('wp-query');
      if ($previousWpQuery) {
        $this->previousWpQuery = $previousWpQuery;
        // PAUSE $this->previousWpQuery
      }

      $this->setProperty('wp-query', $this );
      $this->setProperty('type:WP_Query', $this->query);
      $this->setProperty('context', $this->query);
      $this->setProperty('size', $this->query->post_count);
      $this->setProperty('index', $this->query->current_post);

    } else {
      $this->setProperty('size', 0);
      $this->setProperty('index', 0);
      $this->setError( $this->query );
    }

  }

  public function end() {
    if ($this->previousWpQuery) {
      // RESUME $this->previousWpQuery
    }
  }

  public function makeQueryArgsFromSettings() {

    $args = [];

    if ( ! $this->setting('include_sticky', false ) ) {
      $args['ignore_sticky_posts'] = true;
    }

    $count = $this->setting( 'count', 0 );
    $offset = $this->setting( 'offset', 0 );

    if ($count !== 0) {
      $args['posts_per_page'] = max(1, $count);
    }

    if ($offset !== 0 && $offset) {
      $args['offset'] = max(1, $offset);
    }

    $order = $this->setting('order', '');

    if ($order) {
      $args['order'] = $order;
    }

    $orderby = $this->setting('orderby', '');

    if ($orderby) {
      $args['orderby'] = $orderby;
    }


    return $args;
  }

}