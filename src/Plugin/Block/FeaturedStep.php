<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/10/2018
 * Time: 9:56 AM
 */

namespace Drupal\crowdfundingproject\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * Featured step block
 *
 * @Block(
 *   id = "featured_step",
 *   admin_label = @Translation("Featured Step"),
 * )
 */
class FeaturedStep extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        // Get block config
        $config = $this->getConfiguration();

        $steps = ['one', 'two', 'three'];

        $items = '';
        foreach ($steps as $value) {

            $image = $config['step_' . $value . '_logo'];
            $image_obj = '';
            if (!empty($image)) {
                $image_obj = '<img src="' . \Drupal::service('crowdfundingproject.helper')->getImageUrl($image[0]) . '" />';
            }

            $items .= '<div class="step__item">
             <figure class="item__image">' . $image_obj . '</figure>
             <h4 class="item__header">' . $config['step_' . $value . '_title'] . '</h4>
             <p class="item__text">' . $config['step_' . $value . '_body'] . '</p>
          </div>';
        }

        $items .= '<div class="step__footer">
           <a role="button" class="aed_button" href="/project-create">
           Start een actie
           </a>
        </div>';

        return [
            '#markup' => '<div class="featured-items">' . $items . '</div>',
            '#attached' => [
                'library' => [
                    'crowdfundingproject/steps',
                ],
            ],
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

        $form['fieldset-step-one'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Step One'),
            '#attributes' => [
                'class' => ['fieldset-step-one'],
            ],
        ];

        $form['fieldset-step-one']['step_one_title'] = [
            '#type' => 'textfield',
            '#required' => TRUE,
            '#title' => t('Step Title'),
            '#attributes' => [
                'placeholder' => '',
            ],
            '#default_value' => isset($config['step_one_title']) ? $config['step_one_title'] : '',
        ];

        $form['fieldset-step-one']['step_one_logo'] = [
            '#type' => 'managed_file',
            '#multiple' => FALSE,
            '#description' => t('Step Logo'),
            '#upload_location' => file_default_scheme() . '://steps/',
            '#upload_validators' => [
                'file_validate_is_image' => [],
                'file_validate_extensions' => ['gif png jpg jpeg'],
                'file_validate_size' => [25600],
            ],
            '#title' => t('Step Logo'),
            '#default_value' => isset($config['step_one_logo']) ? $config['step_one_logo'] : '',
        ];

        $form['fieldset-step-one']['step_one_body'] = [
            '#type' => 'textarea',
            '#title' => t('Step body'),
            '#default_value' => isset($config['step_one_body']) ? $config['step_one_body'] : '',
        ];
        /*step 2*/
        $form['fieldset-step-two'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Step two'),
            '#attributes' => [
                'class' => ['fieldset-step-two'],
            ],
        ];

        $form['fieldset-step-two']['step_two_title'] = [
            '#type' => 'textfield',
            '#required' => TRUE,
            '#title' => t('Step Title'),
            '#attributes' => [
                'placeholder' => '',
            ],
            '#default_value' => isset($config['step_two_title']) ? $config['step_two_title'] : '',
        ];

        $form['fieldset-step-two']['step_two_logo'] = [
            '#type' => 'managed_file',
            '#multiple' => FALSE,
            '#description' => t('Step Logo'),
            '#upload_location' => file_default_scheme() . '://steps/',
            '#upload_validators' => [
                'file_validate_is_image' => [],
                'file_validate_extensions' => ['gif png jpg jpeg'],
                'file_validate_size' => [25600],
            ],
            '#title' => t('Step Logo'),
            '#default_value' => isset($config['step_two_logo']) ? $config['step_two_logo'] : '',
        ];

        $form['fieldset-step-two']['step_two_body'] = [
            '#type' => 'textarea',
            '#title' => t('Step body'),
            '#default_value' => isset($config['step_two_body']) ? $config['step_two_body'] : '',
        ];

        /*step 3*/
        $form['fieldset-step-three'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Step three'),
            '#attributes' => [
                'class' => ['fieldset-step-three'],
            ],
        ];

        $form['fieldset-step-three']['step_three_title'] = [
            '#type' => 'textfield',
            '#required' => TRUE,
            '#title' => t('Step Title'),
            '#attributes' => [
                'placeholder' => '',
            ],
            '#default_value' => isset($config['step_three_title']) ? $config['step_three_title'] : '',
        ];

        $form['fieldset-step-three']['step_three_logo'] = [
            '#type' => 'managed_file',
            '#multiple' => FALSE,
            '#description' => t('Step Logo'),
            '#upload_location' => file_default_scheme() . '://steps/',
            '#upload_validators' => [
                'file_validate_is_image' => [],
                'file_validate_extensions' => ['gif png jpg jpeg'],
                'file_validate_size' => [25600],
            ],
            '#title' => t('Step Logo'),
            '#default_value' => isset($config['step_three_logo']) ? $config['step_three_logo'] : '',
        ];

        $form['fieldset-step-three']['step_three_body'] = [
            '#type' => 'textarea',
            '#title' => t('Step body'),
            '#default_value' => isset($config['step_three_body']) ? $config['step_three_body'] : '',
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state);
        $fieldsetOne = $form_state->getValue('fieldset-step-one');
        $fieldsetTwo = $form_state->getValue('fieldset-step-two');
        $fieldseThree = $form_state->getValue('fieldset-step-three');
        $this->configuration['step_one_logo'] = $fieldsetOne['step_one_logo'];
        $this->configuration['step_one_title'] = $fieldsetOne['step_one_title'];
        $this->configuration['step_one_body'] = $fieldsetOne['step_one_body'];
        $this->configuration['step_two_logo'] = $fieldsetTwo['step_two_logo'];
        $this->configuration['step_two_title'] = $fieldsetTwo['step_two_title'];
        $this->configuration['step_two_body'] = $fieldsetTwo['step_two_body'];
        $this->configuration['step_three_logo'] = $fieldseThree['step_three_logo'];
        $this->configuration['step_three_title'] = $fieldseThree['step_three_title'];
        $this->configuration['step_three_body'] = $fieldseThree['step_three_body'];
    }
}