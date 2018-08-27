<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/27/2018
 * Time: 11:48 AM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class DeleteCommentCommand implements CommandInterface{

  protected $status;
  protected $wrapper;

  public function __construct($status,$wrapper)
  {
    $this->status = $status;
    $this->wrapper = $wrapper;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render()
  {
    return [
      'command' => 'deleteCommentCommand',
      'status' => $this->status,
      'wrapper' => $this->wrapper
    ];
  }

}