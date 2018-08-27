<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/21/2018
 * Time: 10:52 AM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class DestinationRedirectCommand implements CommandInterface
{
  protected $closePopup;
  protected $destination;

  public function __construct($closePopup, $destination)
  {
    $this->closePopup = $closePopup;
    $this->destination = $destination;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render()
  {
    return [
      'command' => 'destinationRedirectCommand',
      'closePopup' => $this->closePopup,
      'destination' => $this->destination,
    ];
  }
}
