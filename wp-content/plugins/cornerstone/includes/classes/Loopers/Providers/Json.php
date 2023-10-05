<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class Json extends ArraySource {

  public function items() {
    // If the JSON is immediately valid, dynamic content will not be run until the fields are accessed
    $items = isset($this->settings['json']) ? json_decode( $this->settings['json'], true ) : null;

    // If the JSON isn't valid, run the string through dynamic content to see if the user is supplying a field that returns valid JSON.
    // If we get valid JSON, the fields will still be parsed when fields are accessed
    if ( is_null( $items ) ) {

      $decoded = json_decode( $this->setting('json', ''), true );

      if ( is_null( $decoded ) ) {
        return new \WP_Error('cs-loopers', 'Unable to decode JSON');
      }

      return $decoded;

    }

    return $items;
  }

}