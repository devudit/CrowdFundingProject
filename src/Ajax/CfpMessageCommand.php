<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/17/2018
 * Time: 12:11 PM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class CfpMessageCommand implements CommandInterface{

  protected $type;
  protected $message;
  protected $wrapper;

  public function __construct($type,$message,$wrapper) {
    $this->type = $type;
    $this->message = $message;
    $this->wrapper = $wrapper;
  }

  public function render()
  {
    return [
      'command' => 'cfpMessageCommand',
      'type' => $this->type,
      'message' => $this->message,
      'wrapper' => $this->wrapper,
    ];
  }

}