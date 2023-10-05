<?php

/*

add_filter( 'cs_looper_custom_my_data', function( $result, $args, $element ) {

  return [];
} );
*/

class Cornerstone_Looper_Provider_Custom extends Cornerstone_Looper_Provider_Generic_Array {

  public function get_array_items( $element ) {
    $args = cs_dynamic_content($element['looper_provider_json']);
    $args = is_string($args)
      ? json_decode( $args, true )
      : $args;
    $hook = str_replace( '-', '_', sanitize_title( cs_dynamic_content( $element['looper_provider_custom'] ) ) );
    $result = apply_filters( "cs_looper_custom_$hook", [], $args, $element );
    return is_array( $result ) ? $result : [];
  }

}
