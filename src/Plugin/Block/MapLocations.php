<?php

namespace Drupal\crowdfundingproject\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "map_locations",
 *   admin_label = @Translation("Map Locations"),
 * )
 */
class MapLocations extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {


    $locations = \Drupal::service('crowdfundingproject.helper')->getMapLocations();

    return [
      '#markup' => '<div class="cfp-map-wrapper"><div class="cfp-map" id="map"></div></div>',
        '#attached' => [
            'library' =>  [
                'crowdfundingproject/map'
            ],
            'drupalSettings' => [
              'cfpMapSettings' => [
                'locations' => $locations
              ]
            ]
        ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    //$this->configuration['google_map_api_key'] = $form_state->getValue('google_map_api_key');
  }
}