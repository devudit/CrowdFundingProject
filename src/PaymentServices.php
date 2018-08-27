<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/10/2018
 * Time: 12:58 PM
 */

namespace Drupal\crowdfundingproject;


use Zend\Diactoros\Response\RedirectResponse;

class PaymentServices implements PaymentServicesInterface {

  /**
   * {@inheritdoc}
   */
  public function makePayment($amount,
                              $description,
                              $payment_id,
                              $issuer,
                              $customerId = NULL,
                              array $metadata = [],
                              $method = 'ideal') {

    global $base_url;

    /* TODO: Convert array to error object */
    /* TODO: On success return payment object */

    $status = [
      'status' => 'error',
      'data' => [
        'message' => 'Unknown error occur!!',
      ],
    ];

    if (empty($amount) || empty($payment_id)) {
      $status['data']['message'] = 'Payment fields are missing';
    }

    $redirectUrl = $base_url . '/donation/status/' . $payment_id;
    $webhookUrl = $base_url . '/donation/webhook';

    $mollie = new \Mollie_API_Client();
    $mollie_api_key = \Drupal::config('cfp.mollie.settings')
      ->get('mollie_api_key');
    if ($mollie_api_key) {
      $mollie->setApiKey($mollie_api_key);
      try {
        $payment = $mollie->payments->create(
          [
            'amount' => $amount,
            'description' => $description,
            'redirectUrl' => $redirectUrl,
            'webhookUrl' => $webhookUrl,
            'metadata' => $metadata,
          ]
        );

        $status = [
          'status' => 'success',
          'data' => [
            'message' => 'Success',
            'paymentUrl' => $payment->getPaymentUrl(),
            'paymentId' => $payment->id,
            'status' => $payment->status,
          ],
        ];

      } catch (\Mollie_API_Exception $e) {
        $status['data']['message'] = "API call failed: " . htmlspecialchars($e->getMessage()) . " on field " . htmlspecialchars($e->getField());
      }
    }

    return $status;
  }

  /**
   * {@inheritdoc}
   */
  public function getPaymentData($id) {

    $mollie = new \Mollie_API_Client();
    $mollie_api_key = \Drupal::config('cfp.mollie.settings')
      ->get('mollie_api_key');
    if ($mollie_api_key) {
      $mollie->setApiKey($mollie_api_key);

      try {
        return $mollie->payments->get($id);
      } catch (\Mollie_API_Exception $e) {
        return $e;
      }
    }

    return null;
  }

}