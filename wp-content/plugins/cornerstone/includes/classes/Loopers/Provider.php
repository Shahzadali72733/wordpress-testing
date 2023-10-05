<?php

namespace Themeco\Cornerstone\Loopers;

use Themeco\Cornerstone\Loopers\Providers\Source;
use Themeco\Cornerstone\Vm\Runtime;
use Themeco\Cornerstone\Vm\Constants;

class Provider {

  protected $ctx;
  protected $didSetupPostData = false;

  public function ctx($ctx) {
    $this->ctx = $ctx;
    return $this;
  }

  public function setup( $settings ) {

    $this->frame = $this->ctx->newFrame();
    $this->frame->set(Constants::Looper, "provider", $this);
    $this->source = Source::create($this->ctx, $settings);
    $this->source->begin();
    add_action( 'the_post', function() {
      $this->didSetupPostData = true;
    });
    global $post;
    $this->originalPost = $post;
    return $this;
  }

  public function dispose() {
    $this->source->end();
    $this->restoreOriginalPost();
    $this->frame->dispose();
  }

  public function restoreOriginalPost() {
    global $post;
    if ($this->originalPost && $post && $post->ID !== $this->originalPost->ID ) {
      $post = $this->originalPost;
      setup_postdata( $this->originalPost );
    }
  }

  public function consume() {

    if (!$this->source->hasItems()) {
      return [false, null];
    }

    $this->didSetupPostData = false;
    $next = $this->source->next();
    $this->maybeSetupPostData($next);

    return [true, $next];
  }

  public function rewind() {
    $this->source->rewind();
  }

  public function maybeSetupPostData( $next ) {
    if ( is_a($next, 'WP_Post') && ! $this->didSetupPostData ) {
      global $post;
      $post = $next;
      setup_postdata( $post );
    }
  }

}