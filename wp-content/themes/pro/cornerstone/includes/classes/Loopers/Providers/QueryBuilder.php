<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class QueryBuilder extends QueryWp {


  public function makeQueryBuilderArgs() {

    $args = $this->makeQueryArgsFromSettings();

    $before = $this->setting('before', '');
    $after = $this->setting('after', '');

    if ($before || $after) {

      $date_query = [];

      if ($before) {
        $date_query['before'] = $before;
      }

      if ($after) {
        $date_query['after'] = $after;
      }

      $args['date_query'] = $date_query;
    }

    $post_types = $this->setting('post_types', []);

    if ( ! empty( $post_types ) ) {
      $args['post_type'] = $post_types;
    }

    $post_ids = $this->setting('post_ids', []);

    if ( ! empty( $post_ids ) ) {
      $key = $this->setting( 'post_in', true ) ? 'post__in' : 'post__not_in';
      $args[$key] = $post_ids;
    }

    $term_ids = $this->setting('term_ids', []);

    if ( ! empty( $term_ids ) ) {

      $taxonomies = [];
      $tax_query_relation = 'OR';
      $term_count = count($term_ids);

      $term_and = $this->setting('term_and', false);
      $term_in = $this->setting('term_in', true);

      if ( $term_and ) {
        $tax_query = [];

        foreach ($term_ids as $term) {
          list($taxonomy, $term_id) = explode('|', $term);
          $clause = [ 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => [$term_id] ];

          if ( ! $term_in ) {
            $clause['operator'] = 'NOT IN';
          }

          $tax_query[] = $clause;

        }

        $tax_query_relation = 'AND';

      } else {

        foreach ($term_ids as $term) {
          list($taxonomy, $term_id) = explode('|', $term);
          if ( ! isset( $taxonomies[$taxonomy] ) ) {
            $taxonomies[$taxonomy] = [ 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => [] ];
            if ( ! $term_in ) {
              $taxonomies[$taxonomy]['operator'] = 'NOT IN';
            }
          }
          $taxonomies[$taxonomy]['terms'][] = $term_id;

        }

        $tax_query = array_values($taxonomies);

      }

      if ( count( $tax_query ) > 0 ) {
        $tax_query['relation'] = $tax_query_relation;
        $args['tax_query'] = $tax_query;
      }

    }

    $author_ids = $this->setting('author_ids', []);

    if ( !empty( $author_ids ) ) {
      $key = $this->setting('author_in', true) ? 'author__in' : 'author__not_in';
      $args[$key] = $author_ids;
    }

    var_dump($args);
    return $args;

  }

  public function setupQuery() {
    return new \WP_Query( $this->makeQueryBuilderArgs() );
  }
}