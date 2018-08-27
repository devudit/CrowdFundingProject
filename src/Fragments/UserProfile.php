<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/21/2018
 * Time: 6:40 PM
 */

namespace Drupal\crowdfundingproject\Fragments;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\user\Entity\User;

class UserProfile implements FragmentInterface
{

    public static function render(array $data)
    {
        $markup = '';

        if (isset($data['id']) && !empty($data['id'])) {
            $user = User::load($data['id']);
            if ($user instanceof \Drupal\user\Entity\User) {

                /* User name */
                $title = $user->getDisplayName();
                if ($user->get('field_firstname')->value) {
                    $title = $user->get('field_firstname')->value . ' ' . $user->get('field_lastname')->value;
                }

                /* User image*/
                $target_id = $user->get('user_picture')->target_id;
                $image_src = \Drupal::service('crowdfundingproject.helper')->getImageUrl($target_id);
                $image_src = empty($image_src) ? "/" . drupal_get_path('module', 'crowdfundingproject') . "/images/default-avatar.png" : $image_src;


                /* Modal header */
                $header = '<div class="top-icon"><img src="' . $image_src . '" /></div><h4>' . $title . '</h4>';

                /* Modal body */
                $body = '<div id="profile-preview">
                        <ul class="profile-tags"><li class="info-date"><span>' . date('Y-m-d', $user->getCreatedTime()) . '</span></li></ul>' . $user->get('field_bio')->value . '</div>';

                /* Modal footer */
                $footer = '<div class="modal-profile-info"><div class="info-profile"><span class="info-projects__number"><h4>1</h4></span><span class="info-projects__description">Project Geïnitialiseerd</span></div>
                           <div class="info-profile"><span class="info-projects__number"><h4>5</h4></span><span class="info-projects__description">Projects Gedoneerd</span></div></div>';

                $markup = \Drupal::service('crowdfundingproject.modal_services')->buildModalWrapper($header, $body, $footer);
            }
        }


        return [
            '#title' => 'User Profile',
            '#markup' => $markup,
        ];
    }

}