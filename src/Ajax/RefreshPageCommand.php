<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/21/2018
 * Time: 10:55 AM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class RefreshPageCommand implements CommandInterface
{
  protected $closePopup;

  public function __construct($closePopup)
  {
    $this->closePopup = $closePopup;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render()
  {
    return [
      'command' => 'refreshPageCommand',
      'closePopup' => $this->closePopup,
    ];
  }
}