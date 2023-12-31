<?php

namespace Themeco\ElementDesignations;

function designationLoop($element, $definition, $key, $handler) {
  // Process markup colors
  $keys = $definition->get_designated_keys($key);

  // No weight keys
  if (empty($keys)) {
    return $element;
  }

  foreach ($keys as $key) {
    // Will use default definition value
    if (!isset($element[$key])) {
      continue;
    }

    // Main run
    $element[$key] = $handler($element[$key]);
  }

  return $element;
}

/**
 * Process colors prior to sending to element render
 */
add_filter("cs_element_decorate", function($element, $definition) {
  // Process markup colors
  $colorKeys = $definition->get_designated_keys('markup:color');
  foreach ($colorKeys as $key) {
    if (empty($element[$key])) {
      continue;
    }

    // Run globalcolor post processing
    $element[$key] = apply_filters("cs_css_post_process_color", $element[$key]);
  }

  return $element;
}, 0, 2);

/**
 * Process font familys prior to sending to element render
 */
add_filter("cs_element_decorate", function($element, $definition) {
  // Process markup colors
  $fontFamilyKeys = $definition->get_designated_keys('markup:font-family');
  foreach ($fontFamilyKeys as $key) {
    if (empty($element[$key])) {
      continue;
    }

    // Run globalcolor post processing
    $element[$key] = apply_filters("cs_css_post_process_font-family", $element[$key]);
  }

  return $element;
}, 0, 2);

/**
 * Process font weights prior to sending to element render
 * Runs before font-family to use raw font-family
 */
add_filter("cs_element_decorate", function($element, $definition) {
  // Process markup colors
  $weightKeys = $definition->get_designated_keys('markup:font-weight');

  // No weight keys
  if (empty($weightKeys)) {
    return $element;
  }

  // The 3rd array item is
  // the options where font_family is set
  $values = $definition->get_aggregated_values();

  foreach ($weightKeys as $key) {
    // No font_family option
    if (empty($values[$key]) || empty($values[$key][2]['font_family'])) {
      if (WP_DEBUG) {
        trigger_error("No font_family passed to markup:font-weight " . $key);
      }
      continue;
    }

    // No value
    if (empty($element[$key])) {
      continue;
    }

    $fontFamily = !empty($element[$values[$key][2]['font_family']])
      ? $element[$values[$key][2]['font_family']]
      : 'inherit';

    // Run global font weight post processing
    // Queues up font
    $element[$key] = apply_filters("cs_css_post_process_font-weight", $fontFamily . '|' . $element[$key]);
  }

  return $element;
}, -5, 2);

/**
 * markup:int
 */
add_filter("cs_element_decorate", function($element, $definition) {
  return designationLoop($element, $definition, "markup:int", function($value) {
    return (int)cs_dynamic_content_string($value);
  });
}, 0, 2);

/**
 * markup:float
 */
add_filter("cs_element_decorate", function($element, $definition) {
  return designationLoop($element, $definition, "markup:float", function($value) {
    return (float)cs_dynamic_content_string($value);
  });
}, 0, 2);
