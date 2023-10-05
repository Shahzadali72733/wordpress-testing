<?php

$_preferenceList = [
  [
    'key'         => 'dynamic_content',
    'label'       => __( 'Dynamic Content', 'cornerstone' ),
    'description' => __( 'Show controls to open Dynamic Content wherever it is supported.', 'cornerstone' ),
    'full'        => true,
  ],
  [
    'key'         => 'show_wp_toolbar',
    'label'       => __( 'WordPress Toolbar', 'cornerstone' ),
    'description' => __( 'Allow WordPress to display the toolbar above the app. Requires a page refresh to take effect.', 'cornerstone' ),
    'full'        => true,
  ],
  [
    'key'         => 'context_menu',
    'label'       => __( 'Context Menu', 'cornerstone' ),
    'description' => __( 'Allow context menu to appear when alt-clicking in the live preview.', 'cornerstone' ),
    'full'        => true,
  ],
  [
    'key'         => 'code_editors',
    'label'       => __( 'Code Editors', 'cornerstone' ),
    'description' => __( 'Add custom CSS and JavaScript to your documents or entire site', 'cornerstone' ),
    'full'        => true,
  ],
  [
    'key'         => 'dev_toolkit',
    'label'       => __( 'Dev Toolkit', 'cornerstone' ),
    'description' => __( 'Experimental functionality used by Themeco developers. Use at your own risk.', 'cornerstone' ),
    'full'        => true,
  ],
];

if (apply_filters('cs_max_enabled', true)) {
  // Max
  $_preferenceList[] = [
    'key'         => 'use_max',
    'label'       => __( 'Enable Max', 'cornerstone' ),
    'disabled'    => !apply_filters("cs_max_enabled", true),
    'description' => __( 'The Best Templates & Training Right In Your Builder.', 'cornerstone' ),
    'full'        => true,
  ];
}

return [
  [
    'type'     => 'group',
    'label'    => __( 'Interface', 'cornerstone' ),
    'controls' => [
      [
        'key'         => 'ui_theme',
        'type'        => 'choose',
        'title'       => __( 'Theme', 'cornerstone' ),
        'description' => __( 'Select how you would like the application UI to appear.', 'cornerstone' ),
        'condition'   => [ 'user_can:preference.ui_theme.user' => true ],
        'options'     => [
          'choices' => [
            [ 'value' => 'light', 'label' => __( 'Light', 'cornerstone' ) ],
            [ 'value' => 'dark',  'label' => __( 'Dark', 'cornerstone' )  ],
          ],
        ],
      ],
      [
        'key'         => 'workspace_side',
        'type'        => 'choose',
        'title'       => __( 'Workspace', 'cornerstone' ),
        'description' => __( 'Decide which side of the screen you prefer the workspace', 'cornerstone' ),
        'condition'   => [ 'user_can:preference.ui_theme.user' => true ],
        'options'     => [
          'choices' => [
            [ 'value' => 'left',  'label' => __( 'Left', 'cornerstone' )  ],
            [ 'value' => 'right', 'label' => __( 'Right', 'cornerstone' ) ]
          ],
        ],
      ],
      [
        'key'         => 'status_indicators',
        'type'        => 'select',
        'title'       => __( 'Status<br/>Indicators', 'cornerstone' ),
        'description' => __( 'Select the contexts where you would like to see element status indicators.', 'cornerstone' ),
        'condition'   => [ 'user_can:preference.content_layout_element.user' => true ],
        'options'     => [
          'choices' => [
            [ 'value' => 'all',         'label' => __( 'Workspace and Breadcrumbs', 'cornerstone' ) ],
            [ 'value' => 'breadcrumbs', 'label' => __( 'Breadcrumbs Only', 'cornerstone' )          ],
            [ 'value' => 'workspace',   'label' => __( 'Workspace Only', 'cornerstone' )            ],
            [ 'value' => 'off',         'label' => __( 'Off', 'cornerstone' )                       ],
          ],
        ],
      ],
    ],
  ],
  [
    'type'     => 'group',
    'label'    => __( 'Functionality', 'cornerstone' ),
    'controls' => [
      [
        'type'    => 'checkbox-list',
        // 'label'   => '&nbsp;',
        'options' => [
          'list' => $_preferenceList,
        ],
      ],
    ],
  ],
  [
    'type'     => 'group',
    'label'    => __( 'Workflow', 'cornerstone' ),
    'controls' => [
      [
        'type'    => 'checkbox-list',
        // 'label'   => '&nbsp;',
        'options' => [
          'list' => [
            [
              'key'         => 'preview_inset',
              'label'       => __( 'Inset Preview', 'cornerstone' ),
              'full'        => true,
            ],
            [
              'key'         => 'rich_text_default',
              'label'       => __( 'Rich Text Editor Default', 'cornerstone' ),
              'description' => __( 'By default, start text editors in rich text mode whenever possible.', 'cornerstone' ),
              'full'        => true,
            ],
            [
              'key'         => 'preserve_nav_group',
              'label'       => __( 'Preserve Inspector Group', 'cornerstone' ),
              'description' => __( 'When navigating between elements this will keep the same group open if it exists on the subsequent element. Hold cmd/ctrl to invert.', 'cornerstone' ),
              'full'        => true,
            ],
          ],
        ],
      ],
    ],
  ]
  // [
  //   'type'     => 'group',
  //   'label'    => __( 'Default Child Element', 'cornerstone' ),
  //   'controls' => [
  //     [
  //       'key'         => 'content_layout_element',
  //       'type'        => 'select',
  //       'title'       => __( 'Section', 'cornerstone' ),
  //       'description' => __( 'Select which element you would like to be the default child when adding new sections.', 'cornerstone' ),
  //       'condition'   => [ 'user_can:preference.content_layout_element.user' => true ],
  //       'options'     => [
  //         'choices' => apply_filters( 'cs_content_layout_element_options', [
  //              [ 'value' => 'layout-row', 'label' => __( 'Row', 'cornerstone' )         ],
  //              [ 'value' => 'row',        'label' => __( 'Classic Row', 'cornerstone' ) ]
  //         ] )
  //       ],
  //     ],
  //   ],
  // ],
];
