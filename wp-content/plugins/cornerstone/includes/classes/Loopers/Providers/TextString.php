<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class TextString extends ArraySource {

  public function items() {
    $content = $this->setting('content', '');
    $delimiter = $this->setting('delimiter', '');
    if ( $delimiter ) {
      return explode( $delimiter, $content );
    } else {
      return str_split( $content );
    }
  }

}