<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/9/2018
 * Time: 11:55 AM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;


class PaymentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfp_payment_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $payment_id = \Drupal::request()->get('id');

    $form['#prefix'] = '<div id="payment-form-wrapper">';

    $form['payment-from-container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cpf-payment-form'],
      ],
    ];

    $form['payment-from-container']['banks-fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('iDEAL'),
      '#attributes' => [
        'class' => ['cpf-bank-fieldset'],
      ],
    ];

    $form['payment-from-container']['banks-fieldset']['payment_id'] = [
      '#type' => 'hidden',
      '#value' => $payment_id,
    ];

    $form['payment-from-container']['banks-fieldset']['bank-info'] = [
      '#markup' => '<h6>Doneer met iDeal</h6>We verbinden je met iDeal waar je veilig je donatie kunt voltooien.',
    ];

    $paymentBanks = [];
    $mollie = new \Mollie_API_Client();
    $mollie_api_key = \Drupal::config('cfp.mollie.settings')
      ->get('mollie_api_key');
    if ($mollie_api_key) {
      $mollie->setApiKey($mollie_api_key);
      $issuers = $mollie->issuers->all();
      if (!empty($issuers)) {
        foreach ($issuers as $issuer) {
          if ($issuer->method == \Mollie_API_Object_Method::IDEAL) {
            $issuer_id = htmlspecialchars($issuer->id);
            $paymentBanks[$issuer_id] = htmlspecialchars($issuer->name);
          }
        }
      }
    }

    $form['payment-from-container']['banks-fieldset']['bank_option'] = [
      '#type' => 'select',
      '#title' => $this->t('Bank Option'),
      '#options' => $paymentBanks,
    ];

    $form['payment-from-container']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Ga verder'),
      '#attributes' => [
        'class' => [
          'aed_button',
          'use-ajax-submit',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'makePayment'],
        'wrapper' => 'payment-form-wrapper',
      ],
    ];

    $form['payment-security-container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cpf-payment-security'],
      ],
    ];
    $form['payment-security-container']['payment-security'] = [
      '#markup' => '<span class="modal-payment-secure">De betaling wordt veilig afgehandeld door
                        <a target="_blank" class="provider-link Docdata" href="#">
                            <span>Docdata</span>
                        </a>
                    </span>',
    ];

    $form['#suffix'] = '</div>';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*TODO:Maybe move code from ajax callback to here*/
  }

  /**
   * Make payment
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function makePayment(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    /* TODO: Add error notification */
    $payment_id = $form_state->getValue('payment_id');
    $issuer = $form_state->getValue('bank_option');
    if ($payment_id && $issuer) {
      $payment = Node::load($payment_id);

      if (is_object($payment)) {

        // add current user reference to payment
        if (!\Drupal::currentUser()->isAnonymous()) {
          $payment->set('uid',\Drupal::currentUser()->id());
          $payment->set('field_user',\Drupal::currentUser()->id());
          $payment->save();
        }

        // end add current user reference to payment

        $meta = [
          'payment_id' => $payment_id,
        ];

        $status = \Drupal::service('crowdfundingproject.payment_services')
          ->makePayment(
            $payment->get('field_amount')->value,
            $payment->getTitle(),
            $payment->id(),
            $issuer,
            NULL,
            $meta
          );

        if ($status['status'] == 'success') {
          $paymentData = $status['data'];
          $payment->set('field_status', $paymentData['status']);
          $payment->set('field_mollie_payment_id', $paymentData['paymentId']);
          $payment->save();
          $response->addCommand(new RedirectCommand($paymentData['paymentUrl']));
        }
      }
    }

    return $response;
  }

}