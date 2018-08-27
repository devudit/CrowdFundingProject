<?php

namespace Drupal\crowdfundingproject\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\crowdfundingproject\Ajax\LoadFormCommand;
use Drupal\crowdfundingproject\Ajax\LoadHtmlCommand;
use Drupal\crowdfundingproject\Ajax\UpdateFragmentCommand;
use Drupal\crowdfundingproject\Fragments\UserLoginMenu;
use Drupal\crowdfundingproject\Fragments\UserProfile;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CfpAjaxCallbackController extends ControllerBase implements CfpAjaxCallbackInterface
{

    /**
     * The form buidler service
     *
     * @var \Drupal\Core\Form\FormBuilder
     */
    protected $formBuilder;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static
        (
            $container->get('form_builder')
        );
    }

    /**
     * Constructs the object
     *
     * @param \Drupal\Core\Form\FormBuilder $formBuilder
     *   The form builder service
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function ajaxCallback($type, $id)
    {
        $response = new AjaxResponse();

        switch ($type) {
            case "profile":
                $html = UserProfile::render(['id' => $id]);
                if ($html) {
                    $response->addCommand(new LoadHtmlCommand($html, 'profileModal'));
                }
                break;
            case "donate":
                $form = $this->formBuilder->getForm('Drupal\crowdfundingproject\Form\DonateForm');
                $response->addCommand(new LoadFormCommand(
                        $form,
                        'Jouw steun telt',
                        'Selecteer een bedrag of vul zelf een bedrag in',
                        'donate-form',
                        'donate-form',
                        'fadeInUp',
                        'fadeOutDown')
                );
                break;
            case 'payment':
                $form = $this->formBuilder->getForm('Drupal\crowdfundingproject\Form\PaymentForm');
                $response->addCommand(new LoadFormCommand(
                        $form,
                        'Betaal met iDEAL',
                        'Je gaat het volgende actie steunen BuurtAED voor Vogelpark, 2106 DD Heemstede met €50 . Dank je wel!',
                        'payment-form',
                        'payment-form',
                        'fadeInRight',
                        'fadeOutLeft')
                );
                break;
            case 'login':
                $form = $this->formBuilder->getForm('Drupal\crowdfundingproject\Form\CfpLoginForm');
                $response->addCommand(new LoadFormCommand(
                        $form,
                        'Log in met jouw account',
                        '',
                        'logn-form',
                        'login-form',
                        'fadeInRight',
                        'fadeOutRight')
                );
                break;
            case 'register':
                $form = $this->formBuilder->getForm('Drupal\crowdfundingproject\Form\CfpUserRegisterForm');
                $response->addCommand(new LoadFormCommand(
                        $form,
                        'Leuk je te ontmoeten!',
                        '',
                        'register-form',
                        'register-form',
                        'fadeInRight',
                        'fadeOutRight')
                );
                break;
        }

        return $response;
    }

    public function ajaxLoginCallback($type, $id, $slug, $anim_in, $anim_out)
    {
        $response = new AjaxResponse();

        switch ($type) {
            case "login":
                $form = $this->formBuilder->getForm('Drupal\crowdfundingproject\Form\CfpLoginForm');
                $response->addCommand(new LoadFormCommand(
                        $form,
                        'Log in met jouw account',
                        '',
                        'logn-form',
                        'login-form',
                        $anim_in,
                        $anim_out)
                );
                break;
            case "register":
                $form = $this->formBuilder->getForm('Drupal\crowdfundingproject\Form\CfpUserRegisterForm');
                $response->addCommand(new LoadFormCommand(
                        $form,
                        'Leuk je te ontmoeten!',
                        '',
                        'register-form',
                        'register-form',
                        $anim_in,
                        $anim_out)
                );
                break;
        }

        return $response;
    }

    public function ajaxFragmentCallback($type)
    {
        $response = new AjaxResponse();

        switch ($type) {
            case "usermenublock":
                $markup = UserLoginMenu::render(['response' => 'ajax']);
                $response->addCommand(new UpdateFragmentCommand($markup['#markup'],'userMenuFragment'));
                break;
        }

        return $response;
    }
}
