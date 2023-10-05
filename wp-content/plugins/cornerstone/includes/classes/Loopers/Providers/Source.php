<?php

namespace Themeco\Cornerstone\Loopers\Providers;

use Themeco\Cornerstone\Vm\Runtime;
use Themeco\Cornerstone\Vm\Constants;
use Themeco\Cornerstone\Util\Factory;

abstract class Source {

  protected $settings;
  protected $ctx;

  public function setupSource($ctx, $settings) {
    $this->ctx = $ctx;
    $this->settings = $settings;

    // Define default properties for Dynamic Content
    // These should all be updated by the inheriting class
    $this->setProperty('size', 0);
    $this->setProperty('index', 0);
    $this->setProperty('context', $this);

    $this->setup();
    return $this;
  }

  public function setProperty($key, $value) {
    $this->ctx->set(Constants::Looper, $key, $value);
  }

  public function getProperty($key) {
    return $this->ctx->get(Constants::Looper, $key);
  }

  public function getData($key) {
    return $this->ctx->get(Constants::Looper, $key);
  }

  public function setError($error) {
    $this->ctx->set(Constants::Looper, "error", $error);
  }

  public function getError() {
    return $this->ctx->get(Constants::Looper, "error");
  }

  public function setting( $key, $default = null) {
    return $this->ctx->setting( $this->settings, $key, $default );
  }

  abstract function setup();
  abstract function hasItems();
  abstract function next();
  abstract function rewind();

  public function begin() {}
  public function end() {}

  public static function resolve($type) {
    switch ($type) {
      case 'query-wp':
        return Factory::create(QueryWp::class);
      case 'query-recent':
        return Factory::create(QueryRecentPosts::class);
      case 'query-builder':
        return Factory::create(QueryBuilder::class);
      case 'query-string':
        return Factory::create(QueryString::class);
      case 'taxonomy':
        return Factory::create(Taxonomy::class);
      case 'page-children':
        return Factory::create(PageChildren::class);
      case 'terms':
        return Factory::create(Terms::class);
      case 'json':
        return Factory::create(Json::class);
      case 'string':
        return Factory::create(TextString::class);
      case 'custom':
        return Factory::create(Custom::class);
      case 'key-array':
        return Factory::create(KeyArray::class);
      case 'dc':
        return Factory::create(DynamicContent::class);
      default:
        return null;
    }
  }

  public function proxy( $settings ) {
    return self::create($this->ctx, $settings);
  }

  public static function create($ctx, $settings) {
    return self::resolve($settings['type'])->setupSource($ctx, $settings);
  }

}
