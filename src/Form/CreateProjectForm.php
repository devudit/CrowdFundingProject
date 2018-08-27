<?php

namespace Drupal\crowdfundingproject\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;


class CreateProjectForm extends FormBase {

  /**
   * {@inheritdoc}
   * @return string
   */
  public function getFormId() {
    return 'cfp_create_project';
  }

  /**
   * {@inheritdoc}
   * @param array $form
   * @param FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $user = FALSE;
    if (!\Drupal::currentUser()->isAnonymous()) {
      $user = User::load(\Drupal::currentUser()->id());
    }

    // Add Prefix & Suffix to form with class "form-content"
    $form['#prefix'] = '<div class="row"><div class="cfp-create-project-form col-sm-12 col-sm-offset-0 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">';
    $form['#suffix'] = '</div></div>';

    /*Action location hidden fields*/
    $form['lat'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $form['lng'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $form['country_short'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $form['locality'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $form['postal_code'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $form['formatted_address'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $form['sublocality'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    // Form wrapper container
    $form['form-wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'cfp-form-wrapper',
        'class' => ['cfp-form-wrapper'],
      ],
    ];

    // Form Header container
    $form['form-wrapper']['form-header'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cfp-form-header', 'text-center', 'form-header'],
      ],
    ];

    // Form header
    $form['form-wrapper']['form-header']['header'] = [
      '#markup' => '<h1>Start een Project</h1><p>Goed dat je een project wil starten! Vul hier de project details in.</p>',
    ];

    // Form elements container
    $form['form-wrapper']['form-elements'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cfp-form-elements'],
      ],
    ];

    // Form project field container
    $form['form-wrapper']['form-elements']['project-fields'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cfp-form-project-fields'],
      ],
    ];

    // Project packages
    $packages = \Drupal::service('crowdfundingproject.helper')
      ->getAllPackages();
    if (!empty($packages)) {
      $form['form-wrapper']['form-elements']['project-fields']['pakage_title'] = [
        '#markup' => '<h4 class="pacjage-title">Welk type pakket kies je?</h4>',
      ];
      $form['form-wrapper']['form-elements']['project-fields']['project-packages'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['cfp-form-project-packages'],
        ],
      ];
      foreach ($packages as $package) {

        $package_image_id = $package->get('field_package_image')->target_id;
        $package_image_url = \Drupal::service('crowdfundingproject.helper')
          ->getImageUrl($package_image_id, 'large');

        $prefix = '<div class="package-item-wrapper"><div class="package-item">
                    <h4>' . $package->getTitle() . '</h4>
                    <img src="' . $package_image_url . '" />
                    <p>' . $package->get('body')->value . '</p>'.
                    '<ul><li><strong>Winkelwaarde</strong> €'.$package->get('field_to_be_pledged')->value.'</li>
                    <li><strong>Ontvangen bijdrage</strong> €'.$package->get('field_contribution')->value.'</li>
                    <li><strong>Nog in te zamelen</strong> €'.$package->get('field_to_be_raised')->value.'</li></ul>'
        ;

        $key = 'package_' . $package->id();
        $form['form-wrapper']['form-elements']['project-fields']['project-packages'][$key] = [
          '#type' => 'checkbox',
          '#title' => '',
          '#prefix' => $prefix,
          '#suffix' => '</div></div>',
          '#field_suffix' => '<span></span>',
        ];
      }
    }

    // Project title
    $form['form-wrapper']['form-elements']['project-fields']['title'] = [
      '#type' => 'textfield',
      '#title' => t('Titel van je actie'),
      '#attributes' => [
        'placeholder' => 'BuurtAED voor <vul je straat, postcode en plaatsnaam in>',
      ],
      '#prefix' => '<div class="control-group form-element form-element-title">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    ];

    // Project summary
    $form['form-wrapper']['form-elements']['project-fields']['summary'] = [
      '#type' => 'textarea',
      '#title' => t('Jouw persoonlijke motivatie<small class="control-label__hint">Korte beschrijving van jouw persoonlijke motivatie om een BuurtAED actie te starten.</small>'),
      '#prefix' => '<div class="control-group form-element form-element-summary">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    ];

    // Project image
    $form['form-wrapper']['form-elements']['project-fields']['project-image'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cfp-form-project-image'],
      ],
    ];

    $form['form-wrapper']['form-elements']['project-fields']['project-image']['project_image'] = [
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => file_default_scheme() . '://crowdfundingproject/',
      '#attributes' => [
        'class' => ['project-image-file'],
      ],
      '#upload_validators' => [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['png jpg jpeg'],
      ],
    ];

    $form['form-wrapper']['form-elements']['project-fields']['project-image']['project-image-html'] = [
      '#markup' => '<div class=" control-group">
                     <div class="control-label">
                     Actie afbeelding
                     <small>Dit is het hoofdbeeld en beschrijft je actie.</small>
                     </div>
                     <div class="controls image-upload project-image">
                        <div class="image-upload-drag">
                           <figure class="image-upload-view"></figure>
                        </div>
                        <div class="image-upload-feedback">
                           <div class="image-upload-feedback__group">
                              <p class="image-upload-feedback__group--text">
                                 <small>
                                 Dit is een voorbeeld van hoe je afbeelding weergegeven wordt
                                 </small>
                              </p>
                              <p class="image-upload-feedback__group--text">
                                 <small>
                                 Niet blij met het resultaat? Knip je afbeelding op
                                 <a target="_blank" href="http://www.imageresize.org">
                                 www.imageresize.org
                                 </a>
                                 </small>
                              </p>
                           </div>
                        </div>
                        <div class="image-upload-controls">
                           <a class="image-upload-btn" role="button">
                           Selecteer een nieuwe afbeelding van je computer om te uploaden
                           </a>
                           <p class="image-upload-instructions">
                              Upload een liggende foto, aspectverhouding 16:9
                           </p>
                           <small class="image-upload-allowed">
                           JPG of PNG minimale afmeting 800x450
                           </small>
                        </div>
                     </div>                          
                  </div>',
    ];

    // Project body
    $form['form-wrapper']['form-elements']['project-fields']['body'] = [
      '#type' => 'text_format',
      '#title' => t('Info over jouw actie<small class="control-label__hint">Gebruik deze ruimte om het verhaal achter je actie te vertellen en zo supporters te motiveren deel te nemen.De tekst hieronder is een voorbeeldtekst.</small>'),
      '#prefix' => '<div class="control-group form-element form-element-body">',
      '#suffix' => '</div>',
      '#format' => 'cfp_editor',
    ];

    // Project video
    $form['form-wrapper']['form-elements']['project-fields']['video'] = [
      '#type' => 'textfield',
      '#title' => t('Video (optioneel)<small class="control-label__hint">Heb je een video? Voeg het aan je actie toe!</small>'),
      '#attributes' => [
        'placeholder' => 'Youtube of Vimeo (startend met https://)',
      ],
      '#prefix' => '<div class="control-group form-element form-element-video">',
      '#suffix' => '</div>',
    ];

    // Project start date
    $form['form-wrapper']['form-elements']['project-fields']['start_date'] = [
      '#type' => 'date',
      '#date_date_format' => 'd-m-Y',
      '#title' => t('Startdatum <small class="control-label__hint">Wanneer start je actie?</small>'),
      '#prefix' => '<div class="control-group form-element form-element-start-date">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      //'#default_value' => \Drupal\Core\Datetime\DrupalDateTime::createFromTimestamp(strtotime(' + 2 day', time()))
    ];

    // Project end date
    $form['form-wrapper']['form-elements']['project-fields']['end_date'] = [
      '#type' => 'date',
      '#date_date_format' => 'd-m-Y',
      '#title' => t('Einddatum <small class="control-label__hint">Wanneer eindigt je actie?</small>'),
      '#prefix' => '<div class="control-group form-element form-element-end-date">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      //'#default_value' => \Drupal\Core\Datetime\DrupalDateTime::createFromTimestamp(strtotime(' + 30 day', time()))
    ];

    // Project location
    $form['form-wrapper']['form-elements']['project-fields']['map-search'] = [
      '#type' => 'textfield',
      '#title' => t('Actie locatie<small class="control-label__hint">Zoek op straatnaam en woonplaats en sleep de pin naar de locatie waar de AED naar verwachting komt te hangen. Je adres wordt niet op de site vermeld.</small>'),
      '#attributes' => [
        'placeholder' => 'Zoek de actie locatie (stad, land, straat, etc))',
        'class' => ['map-search'],
      ],
      '#prefix' => '<div class="control-group form-element form-element-location">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    ];

    $form['form-wrapper']['form-elements']['project-fields']['gmap'] = [
      '#markup' => '<div id="map" class="map_canvas"></div>',
      '#prefix' => '<div class="map-wrapper">',
      '#suffix' => '</div>',
    ];

    // form user fields
    $form['form-wrapper']['form-elements']['user-fields'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cfp-form-user-fields'],
      ],
    ];

    $form['form-wrapper']['form-elements']['user-fields']['user-header'] = [
      '#markup' => '<h2 class="about-user">Vertel meer over jezelf</h2>',
    ];

    /* user avatar */
    $form['form-wrapper']['form-elements']['user-fields']['user-avatar'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cfp-form-user-avatar'],
      ],
    ];

    $form['form-wrapper']['form-elements']['user-fields']['user-avatar']['avatar'] = [
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => file_default_scheme() . '://crowdfundingproject/',
      '#attributes' => [
        'class' => ['user-avatar-image'],
      ],
      '#upload_validators' => [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['png jpg jpeg'],
      ],
      '#default_value' => $user ? [$user->get('user_picture')->target_id] : [],
    ];

    $avatar_id = $user ? $user->get('user_picture')->target_id : 0;
    $avatar_src = "/" . drupal_get_path('module', 'crowdfundingproject') . "/images/default-avatar.png";
    if ($avatar_id) {
      $avatar_src = \Drupal::service('crowdfundingproject.helper')
        ->getImageUrl($avatar_id);
    }

    $form['form-wrapper']['form-elements']['user-fields']['user-avatar']['user-avatar-html'] = [
      '#markup' => '<div class="control-group">
                      <div class="control-label">Profielfoto</div> 
                      <div class="controls image-upload avatar-upload">
                        <div class="image-upload-drag">
                           <figure class="image-upload-view">
                              <img alt="Foto" src="' . $avatar_src . '">
                           </figure>
                        </div>
                        <div class="image-upload-controls">
                           <a class="image-upload-btn">
                            Kies een afbeelding van je computer
                            </a>
                           <small class="image-upload-allowed">JPG of PNG minimale afmeting 100x100</small>
                        </div>
                      </div>
                    </div>',
    ];

    $form['form-wrapper']['form-elements']['user-fields']['user-name-row'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cpf-user-row'],
      ],
    ];

    // Form Element First name
    $form['form-wrapper']['form-elements']['user-fields']['user-name-row']['firstname'] = [
      '#type' => 'textfield',
      '#title' => t('Voornaam'),
      '#attributes' => [
        'placeholder' => 'Voornaam',
      ],
      '#prefix' => '<div class="control-group form-element form-element-first_name col-md-6 col-lg-6 col-sm-12">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_firstname')->value : '',
    ];

    // Form Element lastname
    $form['form-wrapper']['form-elements']['user-fields']['user-name-row']['lastname'] = [
      '#type' => 'textfield',
      '#title' => t('Achternaam'),
      '#attributes' => [
        'placeholder' => 'Achternaam',
      ],
      '#prefix' => '<div class="control-group form-element form-element-last_name col-md-6 col-lg-6 col-sm-12">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_lastname')->value : '',
    ];

    $form['form-wrapper']['form-elements']['user-fields']['email'] = [
      '#type' => 'email',
      '#prefix' => '<div class="control-group form-element form-element-email">',
      '#suffix' => '</div>',
      '#title' => $this->t('Emailadres'),
      '#attributes' => [
        'placeholder' => 'youremail@host.com',
      ],
      '#required' => TRUE,
      '#default_value' => $user ? $user->getEmail() : '',
    ];

    // Form Element user Bio
    $form['form-wrapper']['form-elements']['user-fields']['bio'] = [
      '#type' => 'textarea',
      '#title' => t('Over jou'),
      '#prefix' => '<div class="control-group form-element form-element-bio">',
      '#suffix' => '</div>',
      '#attributes' => [
        'placeholder' => 'Vertel meer over jezelf en waarom jij in actie komt voort een AED in jouw buurt?',
      ],
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_bio')->value : '',
    ];

    // Form Element Address
    $form['form-wrapper']['form-elements']['user-fields']['address'] = [
      '#type' => 'textfield',
      '#title' => t('Adres'),
      '#prefix' => '<div class="control-group form-element form-element-address">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_address')->address_line1 : '',
    ];

    $form['form-wrapper']['form-elements']['user-fields']['user-address-row'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cpf-user-row'],
      ],
    ];

    // Form Element postcode
    $form['form-wrapper']['form-elements']['user-fields']['user-address-row']['postcode'] = [
      '#type' => 'textfield',
      '#title' => t('Postcode'),
      '#attributes' => [
        'placeholder' => 'Postcode',
      ],
      '#prefix' => '<div class="control-group form-element form-element-postcode col-md-6 col-lg-6 col-sm-12">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_address')->postal_code : '',
    ];


    // Form Element stad
    $form['form-wrapper']['form-elements']['user-fields']['user-address-row']['stad'] = [
      '#type' => 'textfield',
      '#title' => t('Stad'),
      '#attributes' => [
        'placeholder' => 'Stad',
      ],
      '#prefix' => '<div class="control-group form-element form-element-stad col-md-6 col-lg-6 col-sm-12">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_address')->locality : '',
    ];

    // Form Element Phone Number
    $form['form-wrapper']['form-elements']['user-fields']['phone'] = [
      '#type' => 'textfield',
      '#title' => t('Telefoonnummer<small class="control-label__hint">Je nummer wordt niet getoond, we gebruiken het alleen om je te bellen over je actie</small>'),
      '#attributes' => [
        'placeholder' => 'Je telefoonnummer',
      ],
      '#prefix' => '<div class="control-group form-element form-element-phone">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#default_value' => $user ? $user->get('field_tel')->value : '',
    ];

    // Form Element Terms & condition
    $form['form-wrapper']['form-elements']['condition'] = [
      '#type' => 'checkbox',
      '#title' => t('Voorwaarden'),
      '#prefix' => '<div class="control-group form-element form-element-condition"><div class="custom-label">Voorwaarden</div>',
      '#suffix' => '<small class="control-label__hint"><a href="/" target="_blank" class="cfp-view">Algemene Voorwaarden ProCardio</a></small><small class="control-label__hint"><a href="/" target="_blank" class="cfp-view">Algemene Voorwaarden Service Pack BuurtAED</a></small></div>',
      '#required' => TRUE,
    ];

    $form['form-wrapper']['form-elements']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Project indienen'),
      '#attributes' => [
        'class' => ['cfp-submit'],
      ],
    ];

    // attach location library
    $form['#attached']['library'][] = 'crowdfundingproject/location';

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    global $base_url;

    /* create user */
    $user_id = 0;
    $email = $form_state->getValue('email');
    if (\Drupal::currentUser()->isAnonymous()) {
      if (!user_load_by_mail($email)) {
        $username = explode('@', $email);
        $user = User::create([
          'field_firstname' => $form_state->getValue('firstname'),
          'field_lastname' => $form_state->getValue('lastname'),
          'name' => $username[0],
          'mail' => $email,
          'status' => 1,
          'user_picture' => $form_state->getValue('avatar'),
          'field_bio' => $form_state->getValue('bio'),
          'field_address' => [
            'locality' => $form_state->getValue('stad'),
            'postal_code' => $form_state->getValue('postcode'),
            'address_line1' => $form_state->getValue('address'),
          ],
          'field_tel' => $form_state->getValue('phone'),
        ]);

        $userpicture_id = $form_state->getValue('avatar');
        if (!empty($userpicture_id)) {
          $userpicture = File::load($userpicture_id[0]);
          if (is_object($userpicture)) {
            $userpicture->setPermanent();
            $userpicture->save();
          }
        }
        $user->save();
        $user_id = $user->id();
        user_login_finalize($user);
      }
      else {
        drupal_set_message('Email Already Exist','error');
      }
    }
    else {
      $user_id = \Drupal::currentUser()->id();
      $user = User::load($user_id);
      $user->set('field_firstname', $form_state->getValue('firstname'));
      $user->set('field_lastname', $form_state->getValue('lastname'));
      $user->set('user_picture', $form_state->getValue('avatar'));
      $user->set('field_bio', $form_state->getValue('bio'));
      $user->set('field_address', [
        'locality' => $form_state->getValue('stad'),
        'postal_code' => $form_state->getValue('postcode'),
        'address_line1' => $form_state->getValue('address'),
      ]);
      $user->set('field_tel', $form_state->getValue('phone'));
      $user->save();
    }

    /* create project */
    if ($user_id) {
      $selected_package = $this->getSelectedPackage($form_state);
      $package = Node::load($selected_package);
      $percentage = ($package->get('field_contribution')->value/$package->get('field_to_be_pledged')->value)*100;

      $body = $form_state->getValue('body');
      $body['summary'] = $form_state->getValue('summary');
      $project = Node::create([
        'type' => 'crowdfunding_project',
        'status' => 0,
        'title' => $form_state->getValue('title'),
        'body' => $body,
        'field_images' => $form_state->getValue('project_image'),
        'field_project_banner' => $form_state->getValue('project_image'),
        'field_video' => $form_state->getValue('video'),
        'field_to_be_pledged' => $package->get('field_to_be_pledged')->value,
        'field_contribution' => $package->get('field_contribution')->value,
        'field_to_be_raised' => $package->get('field_to_be_raised')->value,
        'field_funding_status' => $percentage,
        'field_number_backers' => 1,
        'field_address' => [
          'country_code' => \Drupal::request()->get('country_short'),
          'locality' => \Drupal::request()->get('locality'),
          'postal_code' => \Drupal::request()->get('postal_code'),
          'address_line1' => \Drupal::request()->get('formatted_address'),
          'address_line2' => \Drupal::request()->get('sublocality'),
        ],
        'field_geolocation' => [
          'lat' => \Drupal::request()->get('lat'),
          'lng' => \Drupal::request()->get('lng'),
        ],
        'field_project_package' => $selected_package,
        'uid' => $user_id,
        'field_funding_start' => $form_state->getValue('start_date'),
        'field_funding_end' => $form_state->getValue('end_date'),
      ]);

      // make project image permanent
      $image = $form_state->getValue('project_image');
      if (!empty($image)) {
        $image = File::load($image[0]);
        if (is_object($image)) {
          $image->setPermanent();
          $image->save();
        }
      }

      $project->set('uid', $user_id);
      $status = $project->save();

      // send email
      if($status){
        // get system email
        $system_site_config = \Drupal::config('system.site');
        $site_email = $system_site_config->get('mail');

        \Drupal::service('emailtemplate.email')->send(
          'New Project:- '.$form_state->getValue('title'),
          'Administrator',
          $site_email,
          $site_email,
          'email_template_project',
          [
            'title' => $form_state->getValue('title'),
            'package' => $package->getTitle(),
            'email' => $user->getEmail(),
            'start' => $form_state->getValue('start_date'),
            'end' => $form_state->getValue('end_date'),
            'link' => $base_url.'/node/'.$project->id().'/edit'
          ]
        );
      }

      drupal_set_message('Project created!!');
      $form_state->setRedirect('user.page');
      return;
    }
  }

  private function getSelectedPackage(FormStateInterface $form_state) {

    $package_id = 0;
    $values = $form_state->getValues();
    if (!empty($values)) {
      foreach ($values as $key => $value) {
        if (substr($key, 0, 8) == 'package_' &&
          $value === 1
        ) {
          $package_id = intval(substr($key, 8));
        }
      }
    }

    return $package_id;

  }
}