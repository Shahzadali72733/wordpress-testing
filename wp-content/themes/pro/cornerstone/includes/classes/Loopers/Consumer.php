<?php

namespace Themeco\Cornerstone\Loopers;
use Themeco\Cornerstone\Vm\Constants;
class Consumer {

  protected $ctx;

  protected $repeat;
  protected $rewind;
  protected $frame = null;
  protected $provider;
  protected $repeatAll;
  protected $counter = 1;

  public function ctx($ctx) {
    $this->ctx = $ctx;
    return $this;
  }

  public function setup( $settings = [] ) {

    $this->rewind = $this->ctx->setting( $settings, 'rewind', false );

    $count = $this->ctx->setting( $settings, 'count', -1 );
    $this->counter = max(1, $count); // should be at least 1
    $this->repeatAll = $count === -1;

    return $this;
  }

  public function start() {
    if ( ! $this->frame ) {
      $this->provider = $this->ctx->get(Constants::Looper, "provider");
      if ( $this->provider ) {
        $this->frame = $this->ctx->newFrame();
      }
    }
  }

  public function end() {
    $this->frame->dispose();
    if ($this->rewind) {
      $this->provider->rewind();
    }
  }

  public function iterate() {

    if (!$this->repeatAll) {
      if($this->counter-- <= 0) {
        return false;
      }
    }

    list($hasResult, $result) = $this->provider->consume();

    if (!$hasResult) {
      return false;
    }

    if ($hasResult) {
      $type = gettype( $result );
      $type = $type === 'object' ? get_class( $result ) : $type;
      $this->frame->set(Constants::Looper, "current", $result);
      $this->frame->set(Constants::Looper, "type:" . $type, $result);
    }

    return true;
  }

  public function run($callback) {
    $results = [];
    $this->start();

    if ( $this->frame ) {
      $first = true;
      while ( $this->iterate() ) {
        $results[] = $callback($first);
        $first = false;
      }
      $this->end();
    }

    return $results;

  }

}