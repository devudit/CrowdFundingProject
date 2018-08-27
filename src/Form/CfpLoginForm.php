<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/20/2018
 * Time: 1:49 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\user\Form\UserLoginForm;
use Drupal\Core\Form\FormStateInterface;

class CfpLoginForm extends UserLoginForm
{

  /**
   * {@inheritdoc}
   */
  public function getFormID()
  {
    return 'cfp_user_login_form';
  }
}