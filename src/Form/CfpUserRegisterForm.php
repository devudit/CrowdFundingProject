<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/20/2018
 * Time: 4:54 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\crowdfundingproject\Ajax\CfpMessageCommand;
use Drupal\crowdfundingproject\Ajax\DestinationRedirectCommand;
use Drupal\crowdfundingproject\Ajax\RefreshPageCommand;
use Drupal\crowdfundingproject\Ajax\RunAjaxCommand;
use Drupal\crowdfundingproject\Ajax\UpdateFragmentCommand;
use Drupal\crowdfundingproject\Fragments\UserLoginMenu;
use Drupal\user\Entity\User;

class CfpUserRegisterForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfp_user_register_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#prefix'] = '<div id="cfp-register-form-wrapper">';
    $form['#suffix'] = '</div>';

    $id = \Drupal::request()->get('id');
    $slug = \Drupal::request()->get('slug');

    // Form wrapper container
    $form['form-wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'cfp-register-form-wrapper',
        'class' => ['cfp-register-form-wrapper'],
      ],
    ];

    $form['form-wrapper']['redirect_url'] = [
      '#type' => 'hidden',
      '#value' => $slug,
    ];
    $form['form-wrapper']['random_id'] = [
      '#type' => 'hidden',
      '#value' => $id,
    ];

    $form['form-wrapper']['facebook-button-wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['facebook-login-wrapper'],
      ],
    ];
    $form['form-wrapper']['facebook-button-wrapper']['facebook_login'] = array(
      '#markup' => '<a class="blue_button button btn-default btn facebookLogin" 
                    href="javascript:void(0)"
                     data-id="'.$id.'"
                     data-wrapper="#cfp_user_login_form_block_wrapper"
                     data-redirect="'.$slug.'" >Meld je aan met Facebook</a>',
      '#suffix' => '<p><small>We plaatsen niets zonder jouw toestemming.</small></p>',
    );

    $form['form-wrapper']['register-field-wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'cfp-register-field-wrapper',
        'class' => ['cfp-register-field-wrapper'],
      ],
    ];

    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'register-field-wrapper-fields',
        'class' => ['register-field-wrapper-fields', 'hide-block-content'],
      ],
    ];

    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields']['form-name-wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'form-name-wrapper',
        'class' => ['form-name-wrapper', 'row'],
      ],
    ];
    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields']['form-name-wrapper']['firstname'] = [
      '#type' => 'textfield',
      '#title' => '',
      '#attributes' => [
        'placeholder' => 'Voornaam',
      ],
      '#prefix' => '<div class="col-sm-6">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    ];
    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields']['form-name-wrapper']['lastname'] = [
      '#type' => 'textfield',
      '#title' => '',
      '#attributes' => [
        'placeholder' => 'Achternaam',
      ],
      '#prefix' => '<div class="col-sm-6">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    ];
    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields']['email'] = [
      '#type' => 'email',
      '#title' => '',
      '#attributes' => [
        'placeholder' => ['E-mailadres'],
      ],
    ];
    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields']['conf_email'] = [
      '#type' => 'email',
      '#title' => '',
      '#attributes' => [
        'placeholder' => ['Voer e-mailadres nogmaals in'],
      ],
    ];
    $form['form-wrapper']['register-field-wrapper']['register-field-wrapper-fields']['password'] = [
      '#type' => 'password',
      '#title' => '',
      '#size' => 25,
      '#attributes' => [
        'placeholder' => ['Wachtwoord'],
      ],
    ];
    //    $form['form-wrapper']['register-field-wrapper']['show-block-content'] = [
    //      '#markup' => '<div class="show-block-content"><a class="btn btn-third" name="show-signup">Meld je aan met e-mail</a></div>',
    //    ];


    $form['form-wrapper']['form-actions'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'register-form-action',
        'class' => ['register-form-action'],
      ],
    ];

    $form['form-wrapper']['form-actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Doe mee'),
      '#attributes' => [
        'class' => [
          'aed_button',
          'use-ajax-submit',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'createUser'],
        'wrapper' => 'cfp-register-form-wrapper',
      ],
      '#suffix' => '<small class="terms-conditions">Door je te registreren, ga je akkoord met de <a href="/terms-and-conditions" target="_blank">algemene voorwaarden van AEDBuurtactie.nl</a></small>',
    ];

    if (!$slug) {
      $slug = '_none';
    }
    $items = '<div class="modal-footer"><span class="modal-btn-login">Heb je al een account? <a class="use-ajax" data-id="' . $id . '" data-animin="flipInY"  data-animout="flipOutY" data-redirect="' . $slug . '" data-type="login" href="/cfp/login/ajax/login/' . $id . '/' . $slug . '/flipInY/flipOutY">hier inloggen</a></span></div>';
    $form['user_links'] = [
      '#markup' => $items,
      '#weight' => 100,
    ];

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
  }

  public function createUser(array &$form, FormStateInterface $form_state) {

    global $base_url;

    $emailConfirmed = true;

    $response = new AjaxResponse();

    $email = $form_state->getValue('email');
    $confEmail = $form_state->getValue('conf_email');
    $password = $form_state->getValue('password');

    if (empty($email) || $email !== $confEmail) {
      drupal_set_message(t('De email adressen zijn niet gelijk aan elkaar'),'error');
      $emailConfirmed = false;
    }

    if(empty($password)){
      drupal_set_message(t('Je wachtwoord moet minstens 6 tekens bevatten'),'error');
    }


    if (!empty($email) && !empty($password) && $emailConfirmed) {

      $slug = $form_state->getValue('redirect_url');
      $id = $form_state->getValue('random_id');

      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $email = $form_state->getValue('email');
      $emailParts = explode("@", $email);
      $username = $emailParts[0];

      // creating user
      $user = User::create([
        'field_firstname' => $form_state->getValue('firstname'),
        'field_lastname' => $form_state->getValue('lastname'),
      ]);

      // Mandatory.
      $user->setPassword($password);
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

      // login user
      user_login_finalize($user);

      drupal_set_message(t('Registered successfully'),'status');


      if (substr($slug,0,6) == 'Modal:'){
        $modalClass = strtolower(str_replace('Form','',substr($slug,6)));
        $response->addCommand(new RunAjaxCommand($modalClass,$id));
      } elseif($slug != '_none' && !empty($slug)){
        $dest = $base_url.'/'.$slug;
        $response->addCommand(new DestinationRedirectCommand(TRUE, $dest));
      } else{
        $response->addCommand(new RefreshPageCommand(TRUE));
      }

      $markup = UserLoginMenu::render(['response' => 'ajax']);
      $response->addCommand(new UpdateFragmentCommand($markup['#markup'],'userMenuFragment'));

    }

    $renderer = \Drupal::service('renderer');
    $messages = ['#type' => 'status_messages'];
    $messages = $renderer->renderRoot($messages);

    $selector = '#cfp-register-form-wrapper';
    $response->addCommand(new AppendCommand($selector, $messages));

    return $response;
  }
}