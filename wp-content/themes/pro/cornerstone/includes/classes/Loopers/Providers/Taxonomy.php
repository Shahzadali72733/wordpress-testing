<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class Taxonomy extends ArraySource {

  public function items() {
    return get_terms([
      'taxonomy'   => $this->setting('taxonomy', 'category'),
      'hide_empty' => $this->setting('hide_empty', false),
    ]);
  }

}