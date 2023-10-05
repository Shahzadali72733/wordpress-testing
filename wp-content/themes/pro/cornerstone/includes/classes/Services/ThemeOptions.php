<?php

namespace Themeco\Cornerstone\Services;
use Themeco\Cornerstone\Elements\BreakpointData;
use Themeco\Cornerstone\Parsy\P;

class ThemeOptions implements Service {

  protected $defaults = [];
  protected $groups = [];
  protected $designations = [];
  protected $updates = [];
  protected $preload = [];
  protected $data = [];
  protected $registered = false;

  public function __construct(ThemeManagement $themeManagement, Config $config) {
    $this->themeManagement = $themeManagement;
    $this->config = $config;
  }

  public function setup() {
    add_action(
      'after_setup_theme',
      [ $this, 'register' ],
      apply_filters('cs_after_theme_options_setup_priority', 0)
    );
  }

  public function register() {

    if ( current_theme_supports( 'cornerstone-managed' ) ) {
      $this->register_options( $this->config->group( 'theme-option-defaults' ), [
        'option'     => 'cs_option_data',
        'responsive' => true
      ]);
    } else {
      $this->register_options( $this->config->group( 'theme-option-standalone-defaults' ), [
        'option'     => 'cs_option_data',
        'responsive' => true
      ]);
    }


    $this->register_option($this->get_global_css_key(), '');
    $this->register_option($this->get_global_js_key(), '');

    $this->register_option('cs_global_parameter_json', '');
    $this->register_option('cs_global_parameter_data', '');
  }

  public function get_global_js_key() {
    return apply_filters( 'cs_global_js_option', 'cs_v1_custom_js' );
  }

  public function get_global_css_key() {
    return apply_filters( 'cs_global_css_option', 'cs_v1_custom_css' );
  }

  public function get_global_js() {
    return $this->get_value( $this->get_global_js_key() );
  }

  public function get_global_css() {
    return $this->get_value( $this->get_global_css_key() );
  }

  public function get_config() {

    $global_css_key = $this->get_global_css_key();
    $global_js_key = $this->get_global_js_key();

    return array(
      'globalCssKey' => $global_css_key,
      'globalJsKey'  => $global_js_key,
      'previewExclusions' => array_merge(
        [ $global_css_key, $global_js_key ],
        apply_filters('cs_theme_option_preview_exclusions',[])
      )
    );
  }

  public function get_controls() {

    $data = [];

    if ( ! $this->themeManagement->isStandalone() ) {
      $data = $this->config->group( 'theme-option-controls' );
    } else {
      $data = $this->config->group('theme-option-standalone-controls');
    }

    return apply_filters( 'cs_theme_options_controls', $data );

  }

  public function register_option( $name, $default_value, $designation = 'markup', $group = null ) {
    $this->defaults[ $name ] = $default_value;
    $this->designations[ $name ] = $designation;
    if ($group) {
      $this->groups[ $name ] = $group;
    }
  }

  public function register_options( $options, $args = [] ) {

    $group = !empty($args['option']) ? $args['option'] :null;
    $responsive = isset( $args['responsive'] ) && $args['responsive'];
    foreach ( $options as $name => $item ) {

      if ( $responsive) {
        list($value, $designation) = $item;
      } else {
        $value = $item;
        $designation = 'markup';
      }
      $this->register_option( $name, $value, $designation, $group );
    }
  }

  public function get_default( $name ) {
    return isset( $name) ? $this->defaults[ $name ] : null;
  }

  public function getValues() {

    $data = [];
    $defaultKeys = array_keys( $this->defaults );
    $defaults = [];

    foreach ($defaultKeys as $key) {
      $data[$key] = $this->get_value( $key );
      $defaults[$key] = [$this->defaults[$key], $this->designations[$key]];
    }

    $data = array_merge( $data, $this->preload );

    // Standalone addons
    if ( ! current_theme_supports( 'cornerstone-managed' ) ) {

      $data = array_merge( $this->data['cs_option_data'], $data); // merge in any saved values that were not registered as options (e.g. _bp_data)
      list($base, $ranges, $size) = cornerstone('Breakpoints')->breakpointConfig();

      if ( ! isset( $data['_bp_base'] ) ) {
        $data['_bp_base'] = $base . '_' . $size;
      }

      if ($base . '_' . $size !== $data['_bp_base'] ) { // current element does not match current base breakpoint
        $breakpointData = cornerstone()->resolve(BreakpointData::class);
        $breakpointData->setElement($data, $defaults);
        $data = $breakpointData->convertTo($base, $size);
      }
    }

    return [$data, $this->defaults,$this->designations];

  }

  public function get_value( $name ) {

    if ( isset( $this->groups[ $name ] ) ) {
      $group = $this->groups[ $name ];

      if ( ! isset( $this->data[$group] ) ) {
        $this->data[$group] = get_option( $group, []);
      }

      if ( ! isset( $this->data[$group][$name] ) ) {
        $this->data[$group][$name] = isset( $this->defaults[ $name ] ) ? $this->defaults[ $name ] : null;
      }
      return $this->data[$group][$name];
    }
    return get_option( $name, $this->get_default( $name ) );
  }

  public function update_value( $name, $value ) {

    if ( is_bool($value) ) {
      $value = sanitize_key($value); // Convert bool to "1" and ""
    }

    if ( 0 === strpos( $name, '_bp_') )  {
      $this->groups[$name] = 'cs_option_data';
    }
    if ( isset( $this->groups[$name] ) ) {
      $group = $this->groups[$name];
      if ( ! isset( $this->updates[$group] ) ) {
        $this->updates[$group] = [];
      }
      $this->updates[$group][$name] = $value;
    } else {
      update_option( $name, $value );
    }

  }

  public function commit() {
    // save groups
    foreach ( $this->updates as $group => $value ) {
      unset($this->data[$group]);
      $existing = get_option($group, []);
      $existing = !empty($existing) && is_array( $existing ) ? $existing : [];
      update_option( $group, array_merge( $existing, $value ));
    }
  }

  public function previewPreFilter( $data ) {
    $this->preload = $data;


    $handler = function( $value ) {
      $option_name = preg_replace( '/^pre_option_/', '', current_filter() );

      if ( isset( $this->preload[ $option_name ] ) ) {
        $value = apply_filters( 'option_' . $option_name, $this->preload[ $option_name ] );
      }

      return $value;
    };


    $exclude = apply_filters( 'cs_theme_option_preview_exclusions', [] );
    foreach ($this->preload as $key => $value) {
      if ( ! empty( $this->groups[ $key ] ) ) {
        continue;
      }
      if ( in_array( $key, $exclude, true ) ) continue;
      add_filter( "pre_option_$key", $handler );
    }
  }

}
