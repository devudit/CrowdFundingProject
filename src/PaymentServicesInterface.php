<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/10/2018
 * Time: 12:57 PM
 */

namespace Drupal\crowdfundingproject;


interface PaymentServicesInterface {

  /**
   * Make payment through mollie payment
   *
   * @param $amount
   * @param $description
   * @param $issuer
   * @param null $customerId
   * @param array $metadata
   * @param string $method
   *
   * @return mixed
   */
  public function makePayment(
    $amount,
    $description,
    $payment_id,
    $issuer,
    $customerId = null,
    array $metadata = [],
    $method = 'ideal'
  );

  /**
   * Get payment data from mollie
   *
   * @param $id
   *
   * @return mixed
   */
  public function getPaymentData($id);

}