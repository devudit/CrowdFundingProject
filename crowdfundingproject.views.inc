<?php

/**
 * @file
 * Include file for defining views handlers and data.
 */

/**
 * Implements hook_views_data_alter().
 */
function crowdfundingproject_views_data_alter(array &$data) {
  $data['node__field_contribution']['views_cfp_almost_full'] = array(
    'title' => t('CFP Almost Full'),
    'group' => t('Content'),
    'help' => t('Project almost full'),
    'field' => [
      'id' => 'almost_full',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'field' => 'field_contribution_value',
      'id' => 'funding-percent',
    ],
  );
}