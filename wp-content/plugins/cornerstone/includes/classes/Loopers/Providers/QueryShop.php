<?php

namespace Themeco\Cornerstone\Loopers\Providers;

class Shop extends QueryMainWp {

  // public function query_begin() {
  //   woocommerce_product_loop_start(false);
  //   $this->has_total = wc_get_loop_prop( 'total' );
  // }

  // protected function query_advance() {
  //   if ($this->has_total && have_posts()) {
  //     the_post();
  //     global $product;
  //     if ( empty( $product ) ) {
  //       return false;
  //     }
  //     if ( ! $product->is_visible() ) {
  //       return $this->advance(); // skip products that are not visible
  //     }
  //     return $product;
  //   } else {
  //     return false;
  //   }
  // }
}