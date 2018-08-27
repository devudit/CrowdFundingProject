<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/9/2018
 * Time: 5:55 PM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class StepCommand implements CommandInterface
{
  protected $moveTo;
  protected $stepId;

  public function __construct($moveTo,$stepId)
  {
    $this->moveTo = $moveTo;
    $this->stepId = $stepId;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render()
  {
    return [
      'command' => 'stepCommand',
      'direction' => $this->moveTo,
      'stepId' => $this->stepId
    ];
  }
}