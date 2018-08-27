<?php
/**
 * d8-aed
 *
 * @package   d8-aed
 * @author    Udit Rawat <eklavyarwt@gmail.com>
 * @license   GPL-2.0+
 * @link      http://emerico.in/
 * @copyright Emerico Web Solutions
 * Date: 22-Apr-18
 * Time: 1:32 AM
 */

namespace Drupal\crowdfundingproject\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\crowdfundingproject\Fragments\UserLoginMenu;

/**
 * Member right navigation
 *
 * @Block(
 *   id = "member_navigation",
 *   admin_label = @Translation("Member Navigation"),
 * )
 */
class MemberMenu extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $markup = UserLoginMenu::render(['response' => 'markup']);

        return [
            '#markup' => $markup['#markup'],
            '#prefix' => '<div class="navbar-right " id="userMenuFragment">',
            '#suffix' => '</div>'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account)
    {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $form = parent::blockForm($form, $form_state);
        $config = $this->getConfiguration();
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
    }
}