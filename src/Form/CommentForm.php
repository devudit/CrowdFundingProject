<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/16/2018
 * Time: 4:01 PM
 */

namespace Drupal\crowdfundingproject\Form;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\crowdfundingproject\Ajax\CfpMessageCommand;
use Drupal\crowdfundingproject\Ajax\UpdateCommentCommand;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

class CommentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfp_comment_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $project = \Drupal::routeMatch()->getParameter('node');
    if($project instanceof \Drupal\node\Entity\Node) {
      $project_id = $project->id();

      $form['#prefix'] = '<div class="cfp-message-container"></div><div id="comment-form-wrapper">';

      $form['comment-form-container'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['cpf-comment-form'],
        ],
      ];

      $form['comment-form-container']['project_id'] = [
        '#type' => 'hidden',
        '#value' => $project_id,
      ];

      $markup = '<header class="wallpost-update-header ">
                    <span class="wallpost-update-title">Plaats een reactie</span>';
      if (!\Drupal::currentUser()->isAnonymous()) {
        $markup .= '<div class="wallpost-header-action">
                        <span class="wallpost-header-action-item">
                            <a class="action-add-photo" data-action-type="show-photo-upload">Voeg foto\'s toe</a>
                        </span>
                        <span class="wallpost-header-action-item">
                            <a class="action-add-video" data-action-type="show-video-input">Voeg een video toe</a>
                        </span>
                    </div>';
      }
      $markup .= '</header>';

      if (\Drupal::currentUser()->isAnonymous()) {
        $markup .= '<div class="signin">
                    <a class="btn btn-small small-login cfpLoginForm use-ajax" href="">Log in</a>
                    om een reactie te plaatsen.
                </div>';
      }

      $form['comment-form-container']['header'] = [
        '#markup' => $markup,
      ];

      if (!\Drupal::currentUser()->isAnonymous()) {
        $form['comment-form-container']['form-body'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['cpf-comment-form-body'],
          ],
        ];

        $form['comment-form-container']['form-body']['comment'] = [
          '#type' => 'textarea',
          '#title' => '',
          '#prefix' => '<div class="comment-text">',
          '#suffix' => '</div>',
          '#required' => TRUE,
          '#attribute' => [
            'placeholder' => 'Plaats een reactie op deze actie',
          ],
          '#resizable' => FALSE,
        ];

        $form['comment-form-container']['form-body']['comment-image-wrapper'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['comment-image-wrapper'],
          ],
        ];

        $form['comment-form-container']['form-body']['comment-image-wrapper']['comment_image'] = [
          '#type' => 'managed_file',
          '#multiple' => FALSE,
          '#upload_location' => file_default_scheme() . '://crowdfundingproject/comment',
          '#attributes' => [
            'class' => ['comment-image-file'],
          ],
          //        '#upload_validators' => [
          //          'file_validate_extensions' => array('png gif jpg jpeg'),
          //          'file_validate_size' => array(25600000),
          //        ],
        ];

        $form['comment-form-container']['form-body']['comment_video'] = [
          '#type' => 'textfield',
          '#title' => '',
          '#attributes' => [
            'class' => ['comment-video-url'],
            'placeholder' => "Youtube of Vimeo url",
          ],
        ];

        $form['comment-form-container']['footer'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['cpf-comment-form-footer'],
          ],
        ];

        $form['comment-form-container']['footer']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Verstuur'),
          '#attributes' => [
            'class' => [
              'btn',
              'btn-small',
              'btn-small-pri',
              'action-submit',
              'use-ajax-submit',
            ],
          ],
          '#ajax' => [
            'callback' => [$this, 'saveComment'],
            'wrapper' => 'comment-form-wrapper',
          ],
        ];

      }

      $form['#suffix'] = '<span class="history-timeline"></span></div>';
    }
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

  public function saveComment(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();

    if (!\Drupal::currentUser()->isAnonymous()) {

      $user_id = \Drupal::currentUser()->id();
      $project_id = $form_state->getValue('project_id');
      $comment_text = $form_state->getValue('comment');

      $comment_title = 'Comment on';
      if($project_id && !empty($comment_text)){
        $project = Node::load($project_id);
        if(is_object($project)){
          $comment_title = $comment_title.' '.$project->getTitle();

          $values = [
            'entity_type' => 'node',
            'entity_id'   => $project_id,
            'field_name'  => 'field_comment',
            'uid' => $user_id,
            'comment_type' => 'comment',
            'subject' => $comment_title,
            'comment_body' => $comment_text,
            'field_video' => $form_state->getValue('comment_video'),
            'field_image' => $form_state->getValue('comment_image'),
            'status' => 1,
          ];
          $comment = Comment::create($values);
          $comment->save();

          // save comment image permanent
          $image_id = $form_state->getValue('comment_image');
          if(!empty($image_id)){
            $image = File::load($image_id[0]);
            if(is_object($image)){
              $image->setPermanent();
              $image->save();
            }
          }

          $comment_data = \Drupal::service('crowdfundingproject.comment')->buildCommentArray($comment);
          $html = \Drupal::service('crowdfundingproject.comment')
            ->getFullCommentHtml($comment_data, $project->id());

          $response->addCommand(new UpdateCommentCommand(true,'#comment-form-wrapper',$html));

          return $response;
        }
      }
    }

    $response->addCommand(new CfpMessageCommand('error','Comment not created','cfp-message-container'));

    return $response;

  }
}