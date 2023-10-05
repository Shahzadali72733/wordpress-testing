<?php

namespace Themeco\Cornerstone\Services;

class VersionMigration implements Service {

  public function setup() {
    add_action( 'init', [ $this, 'versionMigration' ], -1000 );
  }

  public function versionMigration() {
    $prior = get_option( 'cornerstone_version', 0 );

    if ( version_compare( $prior, CS_VERSION, '>=' ) ) {
      return;
    }

    $this->update( $prior );
    do_action( 'cornerstone_updated', $prior );
    do_action( 'cs_purge_tmp' );
    do_action( 'cs_purge_cache' );

    update_option( 'cornerstone_version', CS_VERSION, true );

  }

  // Placeholder for upgrade logic
  public function update( $prior ) {

		/**
		 * Run if coming from a version prior to Before 1.0.7
		 * if ( version_compare( $prior, '1.0.7', '<' ) ) {
		 * }
		 */

    // if ( ! is_null( $prior ) ) {
    //
    // }

	}

}
