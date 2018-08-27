<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/9/2018
 * Time: 12:19 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

class MolliePaymentForm extends ConfigFormBase
{
  /**
   * {@inheritdoc}
   * @return string
   */
  public function getFormId()
  {
    return 'cfp_mollie_payment_settings';
  }

  /**
   * {@inheritdoc}
   * @return array
   */
  protected function getEditableConfigNames() {
    return [
      'cfp.mollie.settings',
    ];
  }

  /**
   * {@inheritdoc}
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $config = $this->config('cfp.mollie.settings');

    $form['mollie_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mollie Api Key'),
      '#description' => 'Get it from mollie dashboard',
      '#default_value' => $config->get('mollie_api_key'),
      '#required'=> true,
      '#disabled' => false
    ];

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->configFactory->getEditable('cfp.mollie.settings')
      // Set the submitted configuration setting
      ->set('mollie_api_key', $form_state->getValue('mollie_api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}