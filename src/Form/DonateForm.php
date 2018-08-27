<?php
/**
 * d8-aed
 *
 * @package   d8-aed
 * @author    Udit Rawat <eklavyarwt@gmail.com>
 * @license   GPL-2.0+
 * @link      http://emerico.in/
 * @copyright Emerico Web Solutions
 * Date: 07-Apr-18
 * Time: 6:53 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\crowdfundingproject\Ajax\RunAjaxCommand;
use Drupal\node\Entity\Node;

class DonateForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'cfp_donate_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $project_id = \Drupal::request()->get('id');

        $form['#prefix'] = '<div id="donation-form-wrapper">';

        $form['donation-from-container'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => ['cpf-donation-form'],
            ],
        ];

        $form['donation-from-container']['donation-amount-container'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => ['donation-amount'],
            ],
        ];

        $form['donation-from-container']['donation-amount-container']['amount'] = [
            '#type' => 'number',
            '#title' => t('Donation Amount'),
            '#required' => TRUE,
            '#prefix' => '<div class="currency-pre">€</div>',
            '#attributes' => [
                'placeholder' => '50,-',
                'class' => ['donation-amount-field'],
            ],
            '#weight' => 1,
        ];

        $form['donation-from-container']['donation-amount-container']['project_id'] = [
            '#type' => 'hidden',
            '#value' => $project_id,
        ];

        $form['donation-from-container']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Ga naar betaling'),
            '#weight' => 2,
            '#attributes' => [
                'class' => [
                    'aed_button',
                    'use-ajax-submit',
                ],
            ],
            '#ajax' => [
                'callback' => [$this, 'savePayment'],
                'wrapper' => 'donation-form-wrapper',
            ],
        ];

        $form['hide_name'] = [
            '#type' => 'checkbox',
            '#title' => '<span>Verberg mijn naam voor deze donatie</span>',
            '#weight' => 3,
            '#attributes' => [
                'class' => ['hide-my-name'],
            ],
        ];

        /**
         * TODO: Make this field dynamic
         */
        $form['#suffix'] = '<ul class="donation-select">
                        <li><a data-price="25">€25,-</a></li>
                        <li><a data-price="50">€50,-</a></li>
                        <li><a data-price="99">€99,-</a></li>
                    </ul></div>';

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (!$form_state->getValue('amount')) {
            $form_state->setErrorByName('amount', $this->t('Donation amount is not valid!'));
        }
        if (!$form_state->getValue('project_id')) {
            $form_state->setErrorByName('project_id', $this->t('Project id is not valid!'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        /*TODO:Maybe move code from ajax callback to here*/
    }

    /**
     * Save payment
     *
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    public function savePayment(array &$form, FormStateInterface $form_state)
    {
        /* TODO: Add error notification */
        $response = new AjaxResponse();

        if ($form_state->getValue('project_id')) {

            $project = Node::load($form_state->getValue('project_id'));

          $amount = $form_state->getValue('amount');
            if(empty($amount)){
              $amount = 50;
            }

            $payment = Node::create([
                'type' => 'payment',
                'title' => 'Pledge To: ' . $project->getTitle(),
                'field_amount' => $amount,
                'field_hide_name' => $form_state->getValue('hide_name') ? true : false,
                'field_project' => $project->id(),
                'field_user' => \Drupal::currentUser()->id() ? \Drupal::currentUser()->id() : '',
                'status' => 1,
                'uid' => \Drupal::currentUser()->id() ? \Drupal::currentUser()->id() : '',
            ]);
            $payment->save();

          if (\Drupal::currentUser()->isAnonymous()) {
            $response->addCommand(new RunAjaxCommand('register', $payment->id()));
          } else {
            $response->addCommand(new RunAjaxCommand('payment', $payment->id()));
          }
        }

        return $response;

    }

}