<?php

namespace Themeco\Cornerstone\Parsy\Util;

class ParseException extends \Exception {
  private $states;

  public function __construct($message, $states = [])  {
    $this->states = $states;
    parent::__construct($message);
  }

  public function getStates() {
    return $this->states;
  }
}