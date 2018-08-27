<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/24/2018
 * Time: 5:02 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

class FacebookForm extends ConfigFormBase
{
  /**
   * {@inheritdoc}
   * @return string
   */
  public function getFormId()
  {
    return 'cfp_facebook_settings';
  }

  /**
   * {@inheritdoc}
   * @return array
   */
  protected function getEditableConfigNames() {
    return [
      'cfp.facebook.settings',
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

    $config = $this->config('cfp.facebook.settings');

    $form['app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App Id'),
      '#description' => 'Get app id form facebook console',
      '#default_value' => $config->get('app_id'),
      '#required'=> true,
      '#disabled' => false
    ];

    $form['app_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App Secret'),
      '#description' => 'Get app secret form facebook console',
      '#default_value' => $config->get('app_secret'),
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
    $this->configFactory->getEditable('cfp.facebook.settings')
      // Set the submitted configuration setting
      ->set('app_id', $form_state->getValue('app_id'))
      ->set('app_secret', $form_state->getValue('app_secret'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}