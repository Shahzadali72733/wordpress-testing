<?php
/*
Plugin Name: Cornerstone
Plugin URI: https://theme.co/cornerstone
Description: The WordPress Page Builder
Author: Themeco
Author URI: https://theme.co/
Version: 7.3.8
Text Domain: cornerstone
Domain Path: lang
*/

defined( 'ABSPATH' ) || exit;
update_option( 'cs_product_validation_key', 'activated' );
require_once 'includes/boot.php';
cornerstone_boot([ 'path' => plugin_dir_path( __FILE__ ), 'url' => plugin_dir_url( __FILE__ ) ]);
