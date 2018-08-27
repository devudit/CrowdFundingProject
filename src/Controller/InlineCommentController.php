<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/17/2018
 * Time: 5:10 PM
 */

namespace Drupal\crowdfundingproject\Controller;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\crowdfundingproject\Ajax\DeleteCommentCommand;
use Drupal\crowdfundingproject\Ajax\UpdateInlineCommentCommand;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;

class InlineCommentController extends ControllerBase{


  public function inlineCommentCallback(Request $request){

    $response = new AjaxResponse();
    $action = $request->get('action');

    switch ($action){
      case "save":
        $result = $this->saveInlineComment($request);
        if($result['status']){
          $response->addCommand(new UpdateInlineCommentCommand($result['status'],$result['wrapper'],$result['html']));
        }
        break;
      case "delete":
        $result = $this->deleteComment($request);
        if($result['status']){
          $response->addCommand(new DeleteCommentCommand($result['status'],$result['wrapper']));
        }
        break;
    }

    return $response;

  }

  public function saveInlineComment(Request $request){

    $data_id = $request->get('data_id');
    $parent_id = $request->get('parent_id');
    $comment = $request->get('comment');

    $status = [
      'status' => false,
      'wrapper' => ''
    ];

    if($data_id && !empty($comment)){
      $entityObj = Node::load($data_id);
      if(is_object($entityObj)){

        if (!\Drupal::currentUser()->isAnonymous()) {

          $user_id = \Drupal::currentUser()->id();

          $comment_title = 'Comment on'.' '.$entityObj->getTitle();

          $values = [
            'entity_type' => 'node',
            'entity_id'   => $data_id,
            'field_name'  => 'field_comment',
            'uid' => $user_id,
            'pid' => $parent_id,
            'comment_type' => 'comment',
            'subject' => $comment_title,
            'comment_body' => $comment,
            'status' => 1,
          ];
          $comment = Comment::create($values);
          $comment->save();

          $comment_wrapper = $parent_id ? '.comments-wrapper-'.$parent_id : '.comments-wrapper-'.$data_id;
          $comment_data = \Drupal::service('crowdfundingproject.comment')->buildCommentArray($comment);
          $comment_html = \Drupal::service('crowdfundingproject.comment')->getCommentLi($comment_data);

          $status = [
            'status' => true,
            'wrapper' => $comment_wrapper,
            'html' => $comment_html
          ];
        }
      }
    }

    return $status;
  }

  public function deleteComment(Request $request){
    $cid = intval($request->get('parent_id'));
    $status = [
      'status' => false,
      'wrapper' => ''
    ];
    if($cid > 0){
      $comment = Comment::load($cid);
      if($comment){
        if($comment->getOwnerId() == \Drupal::currentUser()->id()){
          $wrapper = '#cid-'.$comment->id();
          $comment->delete();
          $status = [
            'status' => true,
            'wrapper' => $wrapper
          ];
        }
      }
    }
    return $status;
  }

}