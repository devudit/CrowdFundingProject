<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/27/2018
 * Time: 5:23 PM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class UpdateCommentCommand  implements CommandInterface{

  protected $status;
  protected $wrapper;
  protected $html;

  public function __construct($status,$wrapper,$html)
  {
    $this->status = $status;
    $this->wrapper = $wrapper;
    $this->html = $html;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render()
  {
    return [
      'command' => 'updateCommentCommand',
      'status' => $this->status,
      'wrapper' => $this->wrapper,
      'html' => $this->html
    ];
  }

}