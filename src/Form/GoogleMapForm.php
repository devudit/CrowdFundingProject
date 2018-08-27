<?php
/**
 * d8-aed
 *
 * @package   d8-aed
 * @author    Udit Rawat <eklavyarwt@gmail.com>
 * @license   GPL-2.0+
 * @link      http://emerico.in/
 * @copyright Emerico Web Solutions
 * Date: 06-Apr-18
 * Time: 11:20 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

class GoogleMapForm extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getFormId()
    {
        return 'cfp_google_map_settings';
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    protected function getEditableConfigNames() {
        return [
            'cfp.gmap.settings',
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

        $config = $this->config('cfp.gmap.settings');

        $form['google_map_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Google Map Api Key'),
            '#description' => 'Get it from google console',
            '#default_value' => $config->get('google_map_key'),
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
        $this->configFactory->getEditable('cfp.gmap.settings')
            // Set the submitted configuration setting
            ->set('google_map_key', $form_state->getValue('google_map_key'))
            ->save();

        parent::submitForm($form, $form_state);
    }
}