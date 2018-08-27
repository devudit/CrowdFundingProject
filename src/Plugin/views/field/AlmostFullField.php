<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/26/2018
 * Time: 11:40 AM
 */

namespace Drupal\crowdfundingproject\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("almost_full")
 */
class AlmostFullField extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {}

  /**
   * Define the available options
   *
   * @return array
   */
  protected function defineOptions() {
    return parent::defineOptions();
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    if ($node instanceof Node) {
      if ($node->getType() == 'crowdfunding_project') {
        $pledge = $node->get('field_contribution')->value;
        $goal = $node->get('field_to_be_pledged')->value;
        $percent = floor(($pledge / $goal) * 100);
        return $percent;
      }
    }
    return 0;
  }
}