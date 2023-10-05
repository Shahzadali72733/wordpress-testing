<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\View;
use Themeco\Cornerstone\Plugin;

class FontAwesome implements Service {

  protected $plugin;
  protected $data;

  // Default types for icon picker
  public static $FA_DEFAULT_TYPES = [
    'x_font_awesome_light_enable' => 'l-hand-pointer',
    'x_font_awesome_solid_enable' => 'hand-pointer',
    'x_font_awesome_regular_enable' => 'o-hand-pointer',
    'x_font_awesome_brands_enable' => 'wordpress-simple',
  ];


  public function __construct(Plugin $plugin, Styling $styling, Config $config, View $view) {
    $this->plugin = $plugin;
    $this->styling = $styling;
    $this->config = $config;
    $this->view = $view;
  }

  public function setup() {
    add_action( 'template_redirect', array( $this, 'registerStyles' ) );
  }

  public function registerStyles() {
    $this->styling->addStyles( 'fa-config', $this->view->name('frontend/font-awesome')->render(false, $this->config(), true), 5 );
  }

  public function ensureData() {
    if (!isset($this->data)) {
      $this->data = apply_filters('cs_font_icon_data', array_merge( $this->config->group( 'font-icons' ), $this->config->group( 'font-icon-aliases' ), array(
        'groups' => array(
          'solid'   => __( 'Solid', 'cornerstone' ),
          'regular' => __( 'Regular', 'cornerstone' ),
          'light'   => __( 'Light', 'cornerstone' ),
          'brands'  => __( 'Brands', 'cornerstone ')
        )
      )));
    }
  }

  public function config() {
    $config = apply_filters( 'cs_fa_config', array(
      'fa_font_path'      => $this->plugin->url . '/assets/fonts/',
      'fa_solid_enable'   => true,
      'fa_regular_enable' => true,
      'fa_light_enable'   => true,
      'fa_brands_enable'  => true,
    ) );

    return $config;
  }

  public function resolveFontAlias( $key ) {
    $this->ensureData();
    return isset( $this->data['aliases'][$key] ) ? $this->data['aliases'][$key] : $key;
  }

  /**
   * Get Font Icon Unicode Value as a string
   * @return string
   */
  public function getFontIcon( $key ) {

    $key = $this->resolveFontAlias( $key );

    $set = 's';

    if ( 0 === strpos($key, 'o-' ) ) {
      $key = substr( $key, 2 );
      if ( in_array($key, $this->data['regular']) ) {
        $set = 'o';
      }
    }

    if ( 0 === strpos($key, 'l-' ) ) {
      $key = substr( $key, 2 );
      if ( in_array($key, $this->data['light']) ) {
        $set = 'l';
      }
    }

    if ( in_array($key, $this->data['brands']) ) {
      $set = 'b';
    }

    $icon = ( isset( $this->data['icons'][ $key] ) ) ? $this->data['icons'][$key] : 'f00d';

    return array( $set, $icon );
  }

  /**
   * Return font icon cache
   * @return array
   */
  public function getFontIcons() {
    $this->ensureData();
    return $this->data['icons'];
  }

  /**
   * Return font icon cache
   * @return array
   */
  public function getFontIds() {
    $this->ensureData();
    $ids = array_keys( $this->data['icons'] );

    foreach ($this->data['regular'] as $key) {
      $ids[] = "o-$key";
    }

    foreach ($this->data['light'] as $key) {
      $ids[] = "l-$key";
    }

    return $ids;

  }

  public function getFontIconsData() {
    $this->ensureData();
    return $this->data;
  }

  function attr( $key ) {
    list($name, $unicode) = $this->getFontIcon( $key );
    return [
      'attr' => 'data-x-icon-' . $name,
      'unicode' => $unicode,
      'entity'  => '&#x' . $unicode . ';'
    ];
  }

  /**
   * Default icon for an icon picker depending on what
   * font awesome icons are enabled
   */
  public static function getDefaultIcon($notEnabledDefault = 'l-hand-pointer') {
    foreach (static::$FA_DEFAULT_TYPES as $type => $default) {
      // Type is not enabled
      if (!get_option($type, false)) {
        continue;
      }

      return $default;
    }

    // FA not even enabled
    return $notEnabledDefault;
  }

}
