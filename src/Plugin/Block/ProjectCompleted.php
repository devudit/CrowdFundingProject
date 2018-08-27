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
 * Time: 1:33 PM
 */

namespace Drupal\crowdfundingproject\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Project completed
 *
 * @Block(
 *   id = "project_completed",
 *   admin_label = @Translation("Project Completed"),
 * )
 */
class ProjectCompleted extends BlockBase {


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

    // get current node
    $project = \Drupal::routeMatch()->getParameter('node');

    $blockHtml = '';

    if($project instanceof \Drupal\node\Entity\Node) {
      $blockHtml .= $this->getMapBlock($project);
      $blockHtml .= $this->getShareResults($project);
      $blockHtml .= $this->getTopDonor($project);

      $locationData = [
        'latitude' => $project->get('field_geolocation')->lat,
        'longitude' => $project->get('field_geolocation')->lng,
      ];

      return [
        '#markup' => '<div class="project-detail-view">' . $blockHtml . '</div>',
        '#attached' => [
          'library' => [
            'crowdfundingproject/completed',
          ],
          'drupalSettings' => [
            'cfpMapSettings' => [
              'locations' => $locationData,
              'projectCompleted' => $project->get('field_completed')->value,
            ],
          ],
        ],
      ];
    }
    return [
      '#markup' => '<div class="project-detail-view">' . $blockHtml . '</div>',
      ];
  }

  public function getMapBlock($project) {

    $code = $project->get('field_address')->country_code;
    $country = \Drupal::service('crowdfundingproject.helper')->getCountryName($code);

    return '<div class="map-block">
                   <section id="impact-result-map">
                      <div class="impact-result-container">
                         <div class="map-container" id="map_canvas"></div>
                         <div class="map-overlay-content is-active">
                            <div class="content-container wide impact-result-block-left">
                               <div class="text-icon-group">
                                  <span class="text-icon icon-impact-map"></span>
                                  <div class="text-group">
                                     <span class="intro">Onze impact op</span>
                                     <span class="title-name">BuurtAED
                                     <small class="title-name-addition">
                                     in ' . $country . '
                                     </small>
                                     </span>
                                  </div>
                               </div>
                            </div>
                            <div class="impact-result-block-right">
                               <div class="impact-result-list ">
                                  <div class="impact-result-content">
                                     <h1>Wat we hebben gedaan</h1>
                                  </div>
                               </div>
                            </div>
                         </div>
                      </div>
                   </section>
                </div>';

  }

  public function getShareResults($project) {
    $image_url = '';

    if ($project instanceof \Drupal\node\Entity\Node) {
      $image_id = $project->get('field_images')->target_id;
      $image_url = \Drupal::service('crowdfundingproject.helper')->getImageUrl($image_id,'large');
    }
    $html = '<div class="share-result" id="share-result">
                   <div class="content-container">
                      <div class="title-container result-title">
                         <div class="text-icon-group">
                            <div class="text-group">
                               <span class="intro">Inspireer jouw netwerk</span>
                               <span class="title-name">Deel de gemaakte impact!</span>
                            </div>
                         </div>
                      </div>
                      <div class="share-block">
                         <div class="share-preview">
                            <div class="share-preview-left">
                               <div class="share-preview-left-blur" data-image="' . $image_url . '"></div>
                               <div class="share-preview-left-color"></div>
                               <div class="share-preview-left-content share-preview-left-project-info">
                                  <h2 class="share-preview-left-content-heading">
                                     Het is ons gelukt, ' . $project->getTitle() . '
                                  </h2>
                                  <strong class="share-preview-left-content-custom">
                                  ' . $project->get('field_number_backers')->value . ' mensen doneerden €' . $project->get('field_contribution')->value . ' en €' . $project->get('field_to_be_pledged')->value . ' is aangevuld
                                  </strong>
                               </div>
                               <div class="share-preview-left-content share-preview-left-project-share">
                                  <div class="share-options">
                                     <div>
                                        <ul class="project-social-share round">
                                           <li class="social-share-item facebook-item ">
                                              <a class="action-facebook"></a>
                                           </li>
                                           <li class="social-share-item twitter-item ">
                                              <a class="action-twitter"></a>
                                           </li>
                                           <li class="social-share-item linkedin-item ">
                                              <a class="action-linkedin"></a>
                                           </li>
                                        </ul>
                                     </div>
                                  </div>
                               </div>
                            </div>
                            <div class="share-preview-right" data-image="' . $image_url . '"></div>
                         </div>
                      </div>
                   </div>
                </div>';

    return $html;
  }

  public function getTopDonor($project) {

    $topDonor = \Drupal::service('crowdfundingproject.helper')
      ->getTopDonor($project->id(), 2);

    return '<div id="thank-you" class="thank-you ">
               <div class="content-container">
                  <div class="title-container">
                     <div class="text-icon-group">
                        <div class="text-group">
                           <span class="intro">En bovenal</span>
                           <span class="title-name">Dank voor jullie support!</span>
                        </div>
                     </div>
                  </div>
               </div>
               <ul class="supporters">
               </ul>
               <div class="see-all-supporters">
                  <a class="btn btn-sec switch-tab-supporter">Bekijk alle supporters</a>
               </div>
            </div>';

  }
}