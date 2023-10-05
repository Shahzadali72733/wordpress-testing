<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class Terms extends ArraySource {

  public function items() {
    return get_the_terms( get_the_ID(), $this->setting('taxonomy', 'category'));
  }

}