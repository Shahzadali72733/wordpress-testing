<?php

/**
 * Filter to add Font Awesome controls
 */

add_filter("cs_theme_options_fontawesome_group", function() {

  return [
    'type' => 'group',
    'group'       => 'x:layout-and-design',
    'label' => __( 'Font Awesome', '__x__' ),
    'description' => __( 'Below is a list of the various Font Awesome icon weights available. Enable or disable them depending on your preferences for usage (for example, if you only plan on using the "Light" icons, you can disable all other weights for a slight performance boost). Keep in mind that completely disabling all Font Awesome icons means that you will not be able to utilize any of the icon pickers throughout our builders and that the markup for icons will still be output to the frontend of your site.', '__x__' ),
    'controls'    => [
      [
        'key'     => 'x_font_awesome_solid_enable',
        'type'    => 'toggle',
        'label'   => __( 'Solid', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
      [
        'key'     => 'x_font_awesome_regular_enable',
        'type'    => 'toggle',
        'label'   => __( 'Regular', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
      [
        'key'     => 'x_font_awesome_light_enable',
        'type'    => 'toggle',
        'label'   => __( 'Light', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
      [
        'key'     => 'x_font_awesome_brands_enable',
        'type'    => 'toggle',
        'label'   => __( 'Brands', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
    ],
  ];
});
