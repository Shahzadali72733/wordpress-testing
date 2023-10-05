<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class PageChildren extends ArraySource {

  public function items() {

    $post_type = get_post_type();

    if ( ! is_post_type_hierarchical( $post_type )) {
      return [];
    }

    return get_children([
      'post_parent' => get_the_ID(),
      'post_type' => $post_type,
      'order' => 'ASC',
      'orderby' => 'menu_order'
    ]);
  }

}