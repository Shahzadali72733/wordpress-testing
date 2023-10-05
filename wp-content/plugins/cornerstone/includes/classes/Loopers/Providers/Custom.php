<?php

namespace Themeco\Cornerstone\Loopers\Providers;

/*

add_filter( 'cs_looper_custom_my_data', function( $result, $args ) {

  return [];
} );
*/

class Custom extends ArraySource {

  public function items() {
    $args = json_decode( $this->setting('json', '' ), true );
    $hook = str_replace( '-', '_', sanitize_title( $this->setting('custom', '' ) ) );
    $result = apply_filters( "cs_looper_custom_$hook", [], array_merge( $this->settings, is_array( $args ) ? $args : [] ) );
    return is_array( $result ) ? $result : [];
  }

}
