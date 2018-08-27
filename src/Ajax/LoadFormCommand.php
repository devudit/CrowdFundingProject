<?php
/**
 * d8-aed
 *
 * @package   d8-aed
 * @author    Udit Rawat <eklavyarwt@gmail.com>
 * @license   GPL-2.0+
 * @link      http://emerico.in/
 * @copyright Emerico Web Solutions
 * Date: 07-Apr-18
 * Time: 6:51 PM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;


class LoadFormCommand implements CommandInterface
{
    protected $form;
    protected $title;
    protected $subTitle;
    protected $id;
    protected $entrancesAnimation;
    protected $exitAnimation;
    protected $class;
    protected $role = 'dialog';
    protected $ariaHidden = 'true';

    public function __construct(
        array $form,
        $title,
        $subTitle,
        $id = 'cfpModal',
        $class = '',
        $entrancesAnimation = 'fadeInUp',
        $exitAnimation = 'fadeOutDown'
    )
    {
        $this->form = $form;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->id = $id;
        $this->class = 'modal fade ' . $class;
        $this->entrancesAnimation = $entrancesAnimation;
        $this->exitAnimation = $exitAnimation;
    }

    /**
     * Implements Drupal\Core\Ajax\CommandInterface:render().
     */
    public function render()
    {
        // Header
        $header = '<h4 class="modal-form-title">'.$this->title.'</h4>';
        $header .= '<p>'.$this->subTitle.'</p>';
        // Body
        $body = render($this->form);
        // Footer
        $footer = '';

        $responseHtml = '<div
                        id="'.$this->id.'"
                        class="'.$this->class.'"
                        data-easein="'.$this->entrancesAnimation.'"
                        data-easeout="'.$this->exitAnimation.'"
                        role="'.$this->role.'"
                        aria-hidden="'.$this->ariaHidden.'">
                        ';
        $responseHtml .= \Drupal::service('crowdfundingproject.modal_services')->buildModalWrapper($header,$body,$footer);
        $responseHtml .= '</div>';

        return [
            'command' => 'loadFormCommand',
            'htmlId' => $this->id,
            'html' => $responseHtml,
            'exitAnimation' => $this->exitAnimation,
        ];
    }
}
