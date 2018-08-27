<?php

namespace Drupal\crowdfundingproject;


interface ModalServicesInterface {

  /**
   * Build modal wrapper using header , body and footer
   * all parameter will be string or you can pass html
   * in them
   *
   * @param null $header
   * @param null $body
   * @param null $footer
   *
   * @return string
   */
  public function buildModalWrapper($header = NULL, $body = NULL, $footer = NULL, $showCloseButton = TRUE);

  /**
   * Prefix wrapper for form
   * all parameters are optional
   *
   * @param string $title
   * @param string $subtitle
   * @param string $id
   * @param string $class
   * @param string $entrancesAnimation
   * @param string $exitAnimation
   *
   * @return string
   */
  public function getFormModalPrefix( $title = '',
                                     $subtitle = '',
                                     $id = '',
                                     $class = '',
                                     $entrancesAnimation = 'fadeInUp',
                                     $exitAnimation = 'fadeOutDown',
                                     $showCloseButton = true
  );

  /**
   * Return suffix wrapper
   * @return string
   */
  public function getFormModalSuffix();
}