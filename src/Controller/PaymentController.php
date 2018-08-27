<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/10/2018
 * Time: 1:11 PM
 */

namespace Drupal\crowdfundingproject\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentController extends ControllerBase{

  public function statusMessage(){

    /* TODO: Format markup as per success page */
    $payment = \Drupal::request()->get('node');

    if($payment instanceof \Drupal\node\Entity\Node){

      return [
        '#markup' => '<h2>'.$payment->getTitle().'</h2><p>Payment status is: '.$payment->get('field_status')->value.'</p>'
      ];
    }

    return [
      '#markup' => 'Node object is missing'
    ];
  }

  public function webhook(){

    /* TODO: Move related code to payment services */
    $response = [
      'status' => 'failed'
    ];

    $mollie_payment_id = \Drupal::request()->request->get('id');
    if($mollie_payment_id){
      $payment = \Drupal::service('crowdfundingproject.payment_services')->getPaymentData($mollie_payment_id);
      if($payment instanceof \Mollie_API_Object_Payment){
        $metaData = $payment->metadata;
        if(isset($metaData->payment_id) && !empty($metaData->payment_id)){
          $intrPayment = Node::load($metaData->payment_id);
          if(is_object($intrPayment)){
            if($mollie_payment_id == $intrPayment->get('field_mollie_payment_id')->value){
              $intrPayment->set('field_status',$payment->status);
              $intrPayment->set('field_log',\json_encode($payment));
              $intrPayment->save();

              /*Update project*/
              $this->updateProject($intrPayment);
              $response['status'] = 'success';
            }
          }
        }
      }
    }

    return new JsonResponse($response);

  }

  private function updateProject($currentPayment){
    // get project id
    $project_id = $currentPayment->get('field_project')->target_id;
    if($project_id){
      // get project
      $project = Node::load($project_id);
      if($project instanceof \Drupal\node\Entity\Node) {
        // all paid payments
        $allpayments = \Drupal::entityQuery('node')
          ->condition('field_status', 'paid')
          ->condition('type', 'payment')
          ->sort('nid', 'DESC')
          ->execute();
        $goalAmount = $project->get('field_to_be_pledged')->value;
        $fundingAmount = 0;
        $backers = [];
        // Calculation
        // TODO: Improve it
        if (!empty($allpayments)) {
          foreach ($allpayments as $payment_id){
            $payment = Node::load($payment_id);
            if($payment instanceof \Drupal\node\Entity\Node) {
              $fundingAmount += floatval($payment->get('field_amount')->value);
              $user_id = $payment->get('field_user')->target_id;
              if($user_id && !in_array($user_id,$backers)){
                $backers[] = $user_id;
              }
            }
          }
        }
        $project->set('field_contribution',$fundingAmount);
        $project->set('field_number_backers',count($backers)+1);
        $project_status = ( $fundingAmount / $goalAmount ) * 100;
        $project->set('field_funding_status',$project_status);
        $project->save();
      }
    }
  }

}