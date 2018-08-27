<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/25/2018
 * Time: 10:13 AM
 */

namespace Drupal\crowdfundingproject\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\crowdfundingproject\Ajax\DestinationRedirectCommand;
use Drupal\crowdfundingproject\Ajax\RefreshPageCommand;
use Drupal\crowdfundingproject\Ajax\RunAjaxCommand;
use Drupal\crowdfundingproject\Ajax\UpdateFragmentCommand;
use Drupal\crowdfundingproject\Fragments\UserLoginMenu;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class SocialController extends ControllerBase{

  public function loginResolver(Request $request){

    global $base_url;

    $response = new AjaxResponse();

    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $login_data = $request->get('login_data');
    $redirect_data = $request->get('redirect_data');

    if($redirect_data){
      $redirect_data = json_decode($redirect_data);
    }

    if(isset($login_data['email']) && !empty($login_data['email'])){

      $email = $login_data['email'];
      $username = explode('@',$email)[0];
      $fb_user_id = $login_data['id'] ? $login_data['id'] : '';
      $firstname = $login_data['first_name'] ? $login_data['first_name'] : '';
      $lastname = $login_data['last_name'] ? $login_data['last_name'] : '';
      $gender = $login_data['gender'] ? $login_data['gender'] : '';
      $locale = $login_data['locale'] ? $login_data['locale'] : '';
      $picture = '';
      if(isset($login_data['picture']) && !empty($login_data['picture'])){
        $picture_data = $login_data['picture'];
        $picture = $picture_data['data']['url'];
      }

      $user = user_load_by_mail($email);
      if(!$user){
        // register user
        // creating user
        $user = User::create([
          'field_firstname' => $firstname,
          'field_lastname' => $lastname,
          'field_facebook_id' => $fb_user_id
        ]);

        // Mandatory.
        $user->setPassword($fb_user_id);
        $user->enforceIsNew();
        $user->setEmail($email);
        $user->setUsername($username);

        // Optional.
        $user->set('init', $email);
        $user->set('langcode', $language);
        $user->set('preferred_langcode', $language);
        $user->set('preferred_admin_langcode', $language);
        $user->addRole('initiator');
        $user->activate();

        // Save user account.
        $user->save();
      }

      // login user
      if($fb_user_id == $user->get('field_facebook_id')->value){
        user_login_finalize($user);
      }

      if($user->id()){
        if (substr($redirect_data->redirect,0,6) == 'Modal:'){
          $modalClass = strtolower(str_replace('Form','',substr($redirect_data->redirect,6)));
          $response->addCommand(new RunAjaxCommand($modalClass,$redirect_data->id));
        } elseif($redirect_data->redirect != '_none' && !empty($redirect_data->redirect)){
          $dest = $base_url.'/'.$redirect_data->redirect;
          $response->addCommand(new DestinationRedirectCommand(TRUE, $dest));
        } else{
          $response->addCommand(new RefreshPageCommand(TRUE));
        }
        drupal_set_message(t('Logged in successfully!'),'status');
      } else{
        drupal_set_message(t('There is some error in facebook login!'),'error');
      }

      // update user fragment
      $markup = UserLoginMenu::render(['response' => 'ajax']);
      $response->addCommand(new UpdateFragmentCommand($markup['#markup'],'userMenuFragment'));

    } else{
      drupal_set_message(t('Email address is missing!'),'error');
    }

    $renderer = \Drupal::service('renderer');
    $messages = ['#type' => 'status_messages'];
    $messages = $renderer->renderRoot($messages);

    $response->addCommand(new AppendCommand($redirect_data->wrapper, $messages));

    return $response;
  }

}