<?php

namespace Drupal\crowdfundingproject\Controller;

interface CfpAjaxCallbackInterface {

  /**
   * Provides the ajax callback response for module
   */
  public function ajaxCallback($type, $id);

  public function ajaxLoginCallback($type, $id, $slug, $anim_in, $anim_out);

  public function ajaxFragmentCallback($type);
}
