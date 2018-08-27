<?php

namespace Drupal\crowdfundingproject\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'days_to_go' formatter.
 *
 * @FieldFormatter(
 *   id = "days_to_go",
 *   label = @Translation("Days To Go"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DaysToGoFormatter extends FormatterBase
{

  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = array();

    foreach ($items as $delta => $date) {
      $date_val = $date->getValue();
      $days = \Drupal::service('crowdfundingproject.helper')->getDaysToGo($date_val['value']);
      $elements[$delta] = array(
        '#markup' => '<span class="top">'.$days.'</span>
         <span class="bottom">Dagen te gaan</span>
         <span class="nav-border"></span>',
      );
    }

    return $elements;
  }

}