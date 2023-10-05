<?php

// @TODO support data
add_filter( 'cs_breakpoint_ranges', function($data) {
  return [ 480, 767, 979, 1200 ];
});

// Default range to match defaults
add_filter( 'cs_breakpoint_default_ranges', function($data) {
  return [ 480, 767, 979, 1200 ];
});
