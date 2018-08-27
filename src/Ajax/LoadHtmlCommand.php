<?php

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;


class LoadHtmlCommand implements CommandInterface{
  protected $html;
  protected $id;
  protected $entrancesAnimation;
  protected $exitAnimation;
  protected $class;
  protected $role = 'dialog';
  protected $ariaHidden = 'true';

  public function __construct(
      array $html,
      $id = 'cfpModal',
      $class = '',
      $entrancesAnimation = 'fadeInUp',
      $exitAnimation = 'fadeOutDown'
  )
  {
    $this->html = $html;
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
    $responseHtml = '<div
                        id="'.$this->id.'"
                        class="'.$this->class.'"
                        data-easein="'.$this->entrancesAnimation.'"
                        data-easeout="'.$this->exitAnimation.'"
                        role="'.$this->role.'"
                        aria-hidden="'.$this->ariaHidden.'">
                        ';
    $responseHtml .= render($this->html);
    $responseHtml .= '</div>';

    return [
      'command' => 'loadHtmlCommand',
      'htmlId' => $this->id,
      'html' => $responseHtml,
    ];
  }
}