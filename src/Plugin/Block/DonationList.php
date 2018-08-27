<?php
/**
 * d8-aed
 *
 * @package   d8-aed
 * @author    Udit Rawat <eklavyarwt@gmail.com>
 * @license   GPL-2.0+
 * @link      http://emerico.in/
 * @copyright Emerico Web Solutions
 * Date: 15-Apr-18
 * Time: 10:15 AM
 */

namespace Drupal\crowdfundingproject\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

/**
 * Donation list block
 *
 * @Block(
 *   id = "donation_list",
 *   admin_label = @Translation("Donation List"),
 * )
 */
class DonationList extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {


    $form = \Drupal::formBuilder()
      ->getForm('Drupal\crowdfundingproject\Form\CommentForm');

    $project = \Drupal::routeMatch()->getParameter('node');
    if ($project instanceof \Drupal\node\Entity\Node) {
      $payments = \Drupal::service('crowdfundingproject.helper')
        ->getDonationList($project->id());

      $blockHtml = '<article class="wall-posts" data-id="' . $project->id() . '">';
      $blockHtml .= render($form);
      $blockHtml .= $this->getProjectComments($project);
      $count = 0;

      foreach ($payments as $payment) {

        $count += 1;

        // Get payment user
        $donor_role = 'Donatie';
        $user = User::load($payment->get('field_user')->target_id);
        $user_basic = \Drupal::service('crowdfundingproject.helper')->getUserBasicDetails($user);

        $comment = $this->getCommentList($payment);

        $days = \Drupal::service('crowdfundingproject.helper')
          ->getDaysAgo($payment->getCreatedTime());

        $blockHtml .= '<article class="m-wallpost with-donation is-post-with-donation">';
        $blockHtml .= '<span class="history-timeline top"></span>';
        $blockHtml .= '<header class="wallpost-header with-donation">
                                  <div class="wallpost-header-profile">
                                     <figure role="button" class="user-avatar">' . $user_basic['image'] . '</figure>
                                     <a role="button" class="user-name showProfile" data-id="'.$user_basic['id'].'">
                                         <strong>' . $user_basic['name'] . '</strong>
                                         <span class="supported">' . $donor_role . '</span>
                                     </a>
                                  </div>
                                  <div class="wallpost-header-amount">
                                     <span class="donation-amount">
                                        <span class="money-format">
                                           €<span>' . $payment->get('field_amount')->value . '</span>
                                        </span>
                                     </span>
                                     <span class="reward-title">
                                     </span>
                                  </div>
                                  <div role="button" class="wallpost-header-timestamp-comment action-load-comment">
                                     <abbr class="timestamp"><span>' . $days . '</span></abbr>
                                     <span class="wallpost-comment-notification"><strong>'.$comment['commentCount'].'</strong></span>
                                  </div>
                               </header>';
        $blockHtml .= $comment['commentHtml'];
        $blockHtml .= '</article>';
      }

      $blockHtml .= '</article>';

      /* sidebar */
      $blockHtml .= '<aside class="wall-sidebar">
                       <div class="hide">
                          <div id="fundraiser-list">
                             <div class="activity">
                                <header class="activity-header">
                                   <h2 class="activity-title ">Fundraisers</h2>
                                </header>
                                <div>
                                   <footer class="activity-footer">
                                      <a class="activity-action">
                                         <div><strong>Wees de eerste fundraiser »</strong><br>
                                            Organiseer een feest of ren een marathon om geld op te halen
                                         </div>
                                      </a>
                                   </footer>
                                </div>
                             </div>
                          </div>
                       </div>
                    </aside>';

      return [
        '#markup' => '<div class="wall-post-wrapper">' . $blockHtml . '</div>',
        '#allowed_tags' => [
          'div',
          'aside',
          'span',
          'a',
          'footer',
          'header',
          'p',
          'abbr',
          'article',
          'figure',
          'strong',
          'h2',
          'ul',
          'li',
          'textarea',
          'button',
          'img',
          'input',
          'label',
          'form',
          'iframe',
        ],
        '#attached' => [
          'library' => [
            'crowdfundingproject/comment',
          ],
        ],
      ];
    }
    return [
      '#markup' => '<div class="wall-post-wrapper"></div>',
    ];

  }

  public function getCommentList($payment) {

    $comments = \Drupal::service('crowdfundingproject.comment')
      ->loadComments($payment->id());

    $inline_comment_form = \Drupal::service('crowdfundingproject.comment')->getInlineCommentForm($payment->id());

    $html = \Drupal::service('crowdfundingproject.comment')->getCommentHtml($comments);

    $html = '<div class="comments-wrapper comments-wrapper-' . $payment->id() . '">
                ' . $html['commentHtml'] . $inline_comment_form . '
            </div>';

    return [
      'commentHtml' => $html,
      'commentCount' => count($comments)
    ];
  }

  public function getProjectComments($project) {

    $comments = \Drupal::service('crowdfundingproject.comment')
      ->loadComments($project->id());

    $html = '';

    foreach ($comments as $comment) {
      $html .= \Drupal::service('crowdfundingproject.comment')
        ->getFullCommentHtml($comment, $project->id());
    }

    return $html;
  }
}