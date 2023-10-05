<?php

/**
 * Admin Max extensions rows
 */
add_action("cs_max_output_admin_extensions", function() {
  // Disabled
  if(!apply_filters('cs_max_enabled', true)) {
    return;
  }

  include(__DIR__ . "/views/page-home-box-max.php");
});
