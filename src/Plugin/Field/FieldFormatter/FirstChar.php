<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/13/2018
 * Time: 5:25 PM
 */

namespace Drupal\crowdfundingproject\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'first_char' formatter.
 *
 * @FieldFormatter(
 *   id = "first_char",
 *   label = @Translation("First char"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class FirstChar extends FormatterBase{

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['text_case'] = [
      '#title' => t('Text Case'),
      '#type' => 'select',
      '#options' => [
        'upper' => $this->t('Uppercase'),
        'lower' => $this->t('Lowercase'),
      ],
      '#default_value' => $this->getSetting('text_case'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = [];

    foreach ($items as $delta => $string) {
      $char = '';
      $case = $this->getSetting('text_case');
      $string = $string->value;
      if($string) {
        if (!$case || $case == 'upper') {
          $string = strtoupper($string);
        }
        else {
          $string = strtolower($string);
        }
        $char = $string[0];
      }
      $elements[$delta] = array(
        '#markup' => $char,
      );

    }

    return $elements;
  }
}