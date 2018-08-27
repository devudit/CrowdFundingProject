<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/21/2018
 * Time: 6:47 PM
 */

namespace Drupal\crowdfundingproject\Fragments;


use Drupal\user\Entity\User;

class UserLoginMenu implements FragmentInterface {

  public static function render(array $data) {

    $html = '<div id="nav-right" class="nav-right ">';
    if (\Drupal::currentUser()->isAnonymous()) {
      $html .= '<ul id="nav-actions" class="nav-actions logged-out">
                 <li class="nav-signup-login">
                    <a class="nav-signin cfpLoginForm" data-type="login" href="/user/login">Log in</a>
                 </li>
              </ul>';
    } else {
      $user_id = \Drupal::currentUser()->id();
      $user = User::load($user_id);
      $picture_id = $user->get('user_picture')->target_id;
      if($picture_id){
        $imageObj = '<img src="'.\Drupal::service('crowdfundingproject.helper')->getImageUrl($picture_id).'" />';
      } else{
        $imageObj = '<span>'.\Drupal::service('crowdfundingproject.helper')->getNameAbbr($user).'</span>';
      }
      $html .= '<ul id="nav-actions" class="nav-actions ">
                     <li class="nav-member">
                        <span class="nav-member-dropdown ">
                           <div class="initialsAvatar avatarColor-1 sec-alt">'.$imageObj.'</div>
                        </span>
                        <ul class="nav-member-dropdown-menu">
                           <li>
                              <ul>
                                 <li>
                                    <a href="/user/'.$user->id().'/edit">Mijn profiel</a>
                                 </li>
                                 <li class="account-links__link">
                                    <a href="/user/'.$user->id().'" class="account-links__link--anchor">Mijn acties</a>
                                 </li>
                              </ul>
                           </li>
                           <li>
                              <a class="logout-action" href="/user/logout">Log uit</a>
                           </li>
                        </ul>
                     </li>
                  </ul>';
    }
    $html .= '</div>';

    return [
        '#markup' => $html,
    ];

  }

}