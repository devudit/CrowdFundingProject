<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/27/2018
 * Time: 4:54 PM
 */

namespace Drupal\crowdfundingproject;


use Drupal\comment\Entity\Comment;
use Drupal\user\Entity\User;

class CommentServices {

  public function getInlineCommentForm($id,$cid=0) {
    $inline_comment_form = '';
    if (!\Drupal::currentUser()->isAnonymous() && $id) {

      $loggedin_user_id = \Drupal::currentUser()->id();
      $loggedin_user = User::load($loggedin_user_id);
      $inline_comment_form = '<div class="comment-footer ">
                 <div class="m-comment-form">              
                     <div class="comment-form-textarea">
                        <textarea placeholder="Schrijf een reactie, ' . ucfirst($loggedin_user->getDisplayName()) . '" name="reaction" class="comment-input "></textarea>
                     </div>
                     <div class="comment-form-footer">
                        <div class="comment-form-share"></div>
                        <div class="comment-form-actions">
                           <button class="btn btn-small btn-small-pri action-inline-comment" data-id="' . $id . '" data-cid="'.$cid.'">Verstuur</button>
                        </div>
                     </div>
                 </div>
              </div>';
    }

    return $inline_comment_form;
  }

  public function loadComments($node_id) {

    $comments = [];

    if ($node_id) {
      $cids = \Drupal::entityQuery('comment')
        ->condition('entity_id', $node_id)
        ->condition('entity_type', 'node')
        ->sort('cid', 'DESC')
        ->execute();

      if (!empty($cids)) {
        foreach ($cids as $cid) {
          $comment = Comment::load($cid);

          $commentData = $this->buildCommentArray($comment);

          $pid = $comment->get('pid')->target_id;
          if ($pid) {
            $comments[$pid]['childs'][$comment->id()] = $commentData;
          }
          else {
            foreach ($commentData as $key => $value) {
              $comments[$comment->id()][$key] = $value;
            }
            $comments[$comment->id()]['childs'] = null;
          }
        }
      }
    }
    return $comments;
  }

  public function buildCommentArray($comment){
    $commentData = [];
    if($comment) {

      $image_id = $comment->get('field_image')->target_id;
      $image_url = \Drupal::service('crowdfundingproject.helper')
        ->getImageUrl($image_id, 'large');

      // load user
      $user = NULL;
      if ($comment->getOwnerId()) {
        $user = User::load($comment->getOwnerId());
      }

      $commentData = [
        'cid' => $comment->id(),
        'user' => $user,
        'subject' => $comment->get('subject')->value,
        'body' => $comment->get('comment_body')->value,
        'image' => $image_url,
        'video' => $comment->get('field_video')->value,
        'created' => $comment->get('created')->value,
      ];
    }
    return $commentData;
  }

  public function getCommentHtml($comments){
    $lis = '';

    if (!empty($comments)) {
      $lis .= '<ul class="comments">';
      foreach ($comments as $key => $comment) {
        $lis .= $this->getCommentLi($comment);
      }
      $lis .= '</ul>';
    }

    return [
      'commentHtml' => $lis,
      'commentCount' => count($comments)
    ];
  }

  public function getCommentLi($comment){

    $user =  \Drupal::service('crowdfundingproject.helper')->getUserBasicDetails($comment['user']);
    $daysAgo = \Drupal::service('crowdfundingproject.helper')
      ->getDaysAgo($comment['created']);

    $lis = '<li class="m-comment" data-comment-id="' . $comment['cid'] . '">
                      <div class="comment-block">
                         <div class="wallpost-comment-header-profile">
                            <figure role="button" class="user-avatar">
                               <div class="initialsAvatar avatarColor-1 pri">' . $user['image'] . '</div>
                            </figure>
                         </div>
                         <div class="wallpost-comment-block">
                            <div class="wallpost-comment-block-name">
                               <a role="button" class="user-name showProfile" data-id="'.$user['id'].'"><strong>' . $user['name'] . '</strong></a>
                               <abbr class="timestamp"><span class="">' . $daysAgo . '</span></abbr>
                            </div>
                            <div class="comment-body ">
                               <div>
                                  <p>' . $comment['body'] . '</p>
                               </div>     
                            </div>
                         </div>
                      </div>
                   </li>';

    return $lis;

  }

  public function getFullCommentHtml($comment,$project_id){
    $html = '';
    if($comment) {
      $user = \Drupal::service('crowdfundingproject.helper')
        ->getUserBasicDetails($comment['user']);
      $daysAgo = \Drupal::service('crowdfundingproject.helper')
        ->getDaysAgo($comment['created']);

      $showDeletebutton = (\Drupal::currentUser()
          ->id() === $comment['user']->id()) ? TRUE : FALSE;

      $video = FALSE;
      $provider = \Drupal::service('video_embed_field.provider_manager')
        ->loadProviderFromInput($comment['video']);
      if ($provider) {
        $provider_element = $provider->renderEmbedCode(600, 380, FALSE);
        $video = render($provider_element);
      }

      $html .= '<article class="m-wallpost show-more" id="cid-' . $comment['cid'] . '">
               <span class="history-timeline top"></span>
               <header class="wallpost-header ">
                  <figure role="button" class="user-avatar" >
                     <div class="initialsAvatar avatarColor-1 pri">' . $user['image'] . '</div>
                  </figure>
                  <span role="button" class="user-name" ><a class="showProfile" data-id="'.$user['id'].'">' . $user['name'] . '</a>
                  </span>
                  <abbr class="timestamp"><span>' . $daysAgo . '</span></abbr>';
      if ($showDeletebutton) {
        $html .= '<ul class="owner-actions">
                     <li class="actions-dropdown">
                        <span class="actions-dropdown__icon--arrow"></span>
                        <ul>
                           <li><a href="#" data-cid="' . $comment['cid'] . '" class="action-delete">Verwijder</a></li>
                        </ul>
                     </li>
                  </ul>';
      }
      $html .= '</header>
               <div class="wallpost-body">
                  <div><p>' . $comment['body'] . '</p></div>';
      $html .= $video ? '<div class="video">' . $video . '</div>' : '';
      if ($comment['image']) {
        $html .= '<ul class="photo-viewer">
                     <li class="cover">
                        <a href="' . $comment['image'] . '">
                        <img src="' . $comment['image'] . '" alt="Foto">
                        </a>
                     </li> 
                  </ul>';
      }
      $html .= '<span class="show-more-less" role="button">
                  Toon meer
                  </span>
               </div>';
      $html .= '<div class="comments comments-wrapper comments-wrapper-' . $comment['cid'] . '">';
      if (count($comment['childs']) > 0) {
        $child = \Drupal::service('crowdfundingproject.comment')
          ->getCommentHtml($comment['childs']);
        $html .= $child['commentHtml'];
      }
      $html .= \Drupal::service('crowdfundingproject.comment')
        ->getInlineCommentForm($project_id, $comment['cid']);
      $html .= '</div>
            </article>';
    }
    return $html;
  }

}