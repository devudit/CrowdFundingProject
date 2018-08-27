<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/9/2018
 * Time: 6:17 PM
 */

namespace Drupal\crowdfundingproject\Controller;


interface StepCallbackInterface {

  /**
   * Steps callback
   * @param $direction
   * @param $step
   */
  public function ajaxCallback($direction, $step);

}