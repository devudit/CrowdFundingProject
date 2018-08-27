<?php
/**
 * d8-aed
 *
 * @package   d8-aed
 * @author    Udit Rawat <eklavyarwt@gmail.com>
 * @license   GPL-2.0+
 * @link      http://emerico.in/
 * @copyright Emerico Web Solutions
 * Date: 22-Apr-18
 * Time: 11:38 AM
 */

namespace Drupal\crowdfundingproject\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class UpdateFragmentCommand implements CommandInterface
{
    protected $wrapper;
    protected $markup;

    public function __construct($markup, $wrapper)
    {
        $this->markup = $markup;
        $this->wrapper = $wrapper;
    }

    /**
     * Implements Drupal\Core\Ajax\CommandInterface:render().
     */
    public function render()
    {
        return [
            'command' => 'updateFragmentCommand',
            'wrapper' => $this->wrapper,
            'markup' => $this->markup,
        ];
    }

}