<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class ElementLibrary implements Service {

  protected $prefabs = [];
  protected $groups = [];

  public function __construct(Plugin $plugin, Elements $elements) {
    $this->plugin = $plugin;
    $this->elements = $elements;
  }

  public function setup() {
    $this->register_group('deprecated', __( 'Deprecated', 'cornerstone' ));
    $this->register_group('layout', __( 'Layout', 'cornerstone' ));
  }

  public function register_group( $name, $title ) {
    $this->groups[ $name ] = $title;
  }

  public function register_prefab_element( $group, $name, $options ) {
    if ( ! isset( $this->prefab[ $group ] ) ) {
      $this->prefab[ $group ] = [];
    }

    try {
      $this->prefabs[ $group ][ $name ] = $this->normalize_prefab_element( $options );
    } catch (\Exception $e) {
      trigger_error('Unabled to register prefab: ' . $e->getMessage( ) );
    }

  }

  public function normalize_prefab_element( $options ) {

    if (!isset($options['type'])) {
      throw new \Exception('type required');
    }

    $options = array_merge( [
      'scope'  => 'all',
      'title'  => $options['type'],
      'icon'   => '',
      'values' => []
    ], $options );

    $prefab = array_merge( [ '_type' => $options['type'] ], $options['values'] );
    $options['values'] = $this->elements->migrations()->migrate( [ $prefab ] )[0];

    return $options;
  }

  public function unregister_prefab_element( $group, $name ) {
    if (isset( $this->prefab[ $group ] ) ) {
      unset( $this->prefabs[ $group][ $name ] );
    }
  }

  public function get_library() {

    if ( !did_action( 'cs_register_dynamic_elements') ) {
      require_once( $this->plugin->path . '/includes/elements/prefab-elements.php' );
      do_action( 'cs_register_prefab_elements' );
    }

    return [ 'groups' => $this->groups, 'prefabs' => $this->prefabs ];

  }


}
