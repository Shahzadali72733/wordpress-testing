<?php

namespace Themeco\Cornerstone\Loopers\Providers;

use Themeco\Cornerstone\Vm\Callstack\Context;
use Themeco\Cornerstone\Util\StringPath;
use Themeco\Cornerstone\Vm\Constants;

class KeyArray extends ArraySource {

  public function __construct(StringPath $stringPath) {
    $this->stringPath = $stringPath;
  }

  public function items() {

    $path = $this->setting('path', '');
    $data = $this->ctx->get(Constants::Looper, "current");

    if (!$path) {
      return is_null( $data ) ? [] : $data;
    }

    $lookup = $this->stringPath->data( $data )->get( $path );
    return is_null( $lookup ) ? [] : $lookup;

  }

}