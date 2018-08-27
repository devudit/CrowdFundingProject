<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/9/2018
 * Time: 6:19 PM
 */

namespace Drupal\crowdfundingproject\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\crowdfundingproject\Ajax\StepCommand;

class StepCallbackController extends ControllerBase implements StepCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public function ajaxCallback($direction, $step) {
    $response = new AjaxResponse();
    $response->addCommand(new StepCommand($direction,$step));
    return $response;
  }

}