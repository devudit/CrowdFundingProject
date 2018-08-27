<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/10/2018
 * Time: 4:35 PM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class RunAjaxCommand implements CommandInterface
{
  protected $type;
  protected $id;

  public function __construct($type,$id)
  {
    $this->type = $type;
    $this->id = $id;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render()
  {
    return [
      'command' => 'runAjaxCommand',
      'type' => $this->type,
      'id' => $this->id
    ];
  }
}