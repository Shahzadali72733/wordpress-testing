<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Util\ErrorHandler;
use Themeco\Cornerstone\Util\CssAsset;
use Themeco\Cornerstone\Util\MinifyCss;

class Styling implements Service {

  public $styles = [];
  public $flushed = [];
  protected $didOutput = false;
  protected $docs = [];
  protected $components = [];

  public function __construct(Plugin $plugin, ErrorHandler $errorHandler, CssAsset $cssAsset, MinifyCss $minifier, ThemeManagement $themeManagement, ThemeOptions $themeOptions, Breakpoints $breakpoints) {
    $this->plugin = $plugin;
    $this->errorHandler = $errorHandler;
    $this->cssAsset = $cssAsset;
    $this->minifier = $minifier;
    $this->themeManagement = $themeManagement;
    $this->themeOptions = $themeOptions;
    $this->breakpoints = $breakpoints;
  }

  public function setup() {

    $this->errorHandler->setHandler(function( $error ) {
      echo '/*' . str_replace('/*', '/\*', str_replace('*/', '*\/', $error ) ) . '*/';
    });

    add_action( 'wp_footer', [ $this, 'lateStyles' ] );

    add_action( 'template_redirect', [ $this, 'globalStyles' ] );
    add_action( 'wp_enqueue_scripts', [$this, 'registerStyles'], 200 );
    add_action( 'wp_enqueue_scripts', [$this, 'outputStyleTag'], 9999 );
  }


  public function registerStyles() {

    list($support) = get_theme_support( 'cornerstone-managed' );

    // Not CS Standalone
    if ( ! is_null($support) ) {
      $deps = isset($support['handle']) ? [ $support['handle'] ] : [];
      wp_register_style( 'cs', false, $deps ); // register an empty asset so we can still call wp_add_inline_style
    } else {
      // CS Standalone
      $csThemeless = "cs-themeless";
      //$stylesheet_name = $this->themeManagement->allowTheming() ? $csThemeless : 'cs';
      $stylesheet_name = 'cs';

      // Classic elements style support for themeless / cornerstone standalone
      if ($stylesheet_name === $csThemeless && $this->themeManagement->isClassicElementsEnabled()) {
        $stylesheet_name = "cs-themeless-classic";
      }

      $url = $this->cssAsset->get( 'assets/css/site/' . $stylesheet_name );
      wp_register_style( 'cs', $url['url'], [], $url['version'] );
    }

    wp_enqueue_style( 'cs' );
  }

  public function outputStyleTag() {
    $this->didOutput = true;
    do_action( 'cs_enqueue_css' );

    $handles = $this->sortHandles();

    if ( did_action( 'cs_before_preview_frame' ) || apply_filters( 'cs_debug_css', false ) ) {
      foreach ($handles as $handle) {
        wp_register_style( "cs-$handle", false, [ 'cs' ] );
        wp_enqueue_style( "cs-$handle" );
        $css = $this->processStyle( $handle );
        $this->flushed[] = $handle;
        if ( ! $css ) $css = '/*  */'; // ensures the style is added in the live preview
        wp_add_inline_style("cs-$handle", $css);
      }
    } else {

      ob_start();
      foreach ($handles as $handle) {
        echo $this->processStyle( $handle );
        $this->flushed[] = $handle;
      }

      wp_add_inline_style('cs', ob_get_clean());
    }

    $this->errorHandler->flush();

  }

  public function globalStyles() {
    if ( ! current_theme_supports( 'cornerstone-managed' ) ) {
      $this->addStyles( 'breakpoints', $this->breakpoints->hideClassOutput(), 60 );
    }
    $this->addStyles( 'global-custom', $this->themeOptions->get_global_css(), 60 );
  }

  public function sortHandles() {
    $handles = array_filter( array_keys($this->styles), function($handle) {
      return ! in_array( $handle, $this->flushed, true );
    });
    usort($handles, function($a, $b) {
      return $this->styles[$a][1] - $this->styles[$b][1];
    });
    return $handles;
  }

  public function lateStyles() {
    $handles = $this->sortHandles();
    foreach ($handles as $handle) {
      $css = $this->processStyle( $handle );
      if ( $css ) {
        $id = esc_attr( $handle );
        echo "<script type=\"text/late-css\" data-cs-late-style=\"$id\">$css</script>";
      }
    }
    $this->errorHandler->flush();
  }

  public function addStyles($handle, $css, $priority = 1 ) {

    if ( isset( $this->styles[$handle] ) || in_array( $handle, $this->flushed, true) ) {
      return;
    }

    $minify = ! apply_filters( 'cs_debug_css', false );

    if ( ! $this->validateStyle( $css ) ) {
      trigger_error("Invalid CSS [$handle] not output: $css");
      $css = "/* Invalid CSS for $handle found. You may be missing a closing bracket. */";
      $minify = false;
    }

    $this->styles[$handle] = [$css, $priority, $minify];

    do_action( 'cs_styling_add_styles', $handle, $css, $minify );

    /**
     * To avoid FOUC we output the style tag asap by default. This results in
     * invalid markup, although most browsers render it just fine. If you need
     * strict markup you can set this constant
     *
     * define('CS_STRICT_LATE_STYLES', true );
     *
     * This will use a more robust and strict style loader. It outputs late
     * CSS as script tags (templates) and adds them t the head after page load.
     */

    if ( $this->didOutput && ! apply_filters( 'cs_is_preview_render', false) ) {
      if (! (defined('CS_STRICT_LATE_STYLES') && CS_STRICT_LATE_STYLES) || apply_filters('cs_strict_late_styles', false) ) {
        echo '<style>';
        echo $this->processStyle( $handle );
        $this->flushed[] = $handle;
        echo '</style>';
        $this->errorHandler->flush();
      }

    }

  }

  public function processStyle( $handle ) {

    list($css, $priority, $minify) = $this->styles[$handle];
    $result = '';

    if ($css) {
      $result = $this->postProcess($css);
      if ( $minify ) {
        $result = $this->minifier->run( $result );
      }
    }

    return $result;
  }


  public function postProcess( $css ) {
    $this->errorHandler->start();
    $css = $this->legacyPostProcess( $css );
    $this->errorHandler->stop();

    $css = preg_replace_callback('/cs-dc\(\'(.*)\'\)/', function( $matches ){
      return $matches[1];
    }, $css );
    return apply_filters( 'cs_css_post_process', $css );
  }

  protected function legacyPostProcess( $css ) {
    $output = $css;

    if ( apply_filters( 'cs_css_post_processing', true ) ) {
      $output = preg_replace_callback('/%%post ([\w:\-]+?)%%([\s\S]*?)%%\/post%%/', function( $matches ){
        return apply_filters('cs_css_post_process_' . $matches[1], $matches[2]);
      }, $output );
    }

    return $output;
  }

  public function validateStyle( $css ) {

    if ( ! apply_filters('cs_validate_syles', false ) ) {
      return true;
    }

    // Remove anything inside a string
    $css = preg_replace('/".*?"/', '""', $css );
    $css = preg_replace("/'.*?'/", "''", $css );

    // If counted occurances of brackets dont match, get outa there
    return substr_count( $css, '{' ) === substr_count( $css, '}' );

  }

  public function addCustomCssFromDocument( $document ) {
    $id = $document->id();
    if ( ! in_array( $id, $this->docs, true ) ) {
      $this->addStyles( $id . '-custom', $document->getCustomCss(), $document->getStylePriority()[1] );
      $this->addStylesForReferencedComponents($document->getReferencedComponents());
      $this->docs[] = $id;
    }
  }

  public function addStylesForReferencedComponents( $ids ) {

    if ( count( $ids ) < 0 ) {
      return;
    }

    list($data, $componentIncludes) = $this->plugin->service('Components')->enumerate();

    foreach ($ids as $id ) {
      if ( ! in_array( $id, $this->components, true ) && ! in_array( $id, $this->docs, true ) ) {
        $key = 'c' . $id;
        if (isset( $componentIncludes[$key][0])) {
          $this->addStyles( $id . '-component', $componentIncludes[$key][0], 100 );
        }
        $this->components[] = $id;
      }
    }

  }

}
