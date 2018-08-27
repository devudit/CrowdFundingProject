<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/26/2018
 * Time: 1:02 PM
 */

namespace Drupal\crowdfundingproject\Plugin\views\sort;

use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Basic sort handler for Events.
 *
 * @ViewsSort("funding-percent")
 */
class FundingPercent extends SortPluginBase {

  /**
   * Called to add the sort to a query.
   */
  public function query() {
    $this->ensureMyTable();
    $percentage = "round(( $this->tableAlias.$this->realField/node__field_to_be_pledged.field_to_be_pledged_value * 100 ),2)";
    $this->query->addOrderBy(NULL,
      $percentage
    );
  }

}