<?php

namespace Drupal\crowdfundingproject;

class ModalServices implements ModalServicesInterface
{

    /**
     * {@inheritdoc}
     */
    public function buildModalWrapper($header = null, $body = null, $footer = null, $showCloseButton = true)
    {
        $wrapper = '<div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">';
            $wrapper .= '<span class="modal-arrow-back" style="display: none;"></span>';
            $wrapper .= $showCloseButton ? '<a type="button" class="close" data-dismiss="modal" aria-hidden="true">×</a>' : '';
            $wrapper .= $header ? $header : '';
            $wrapper .= '</div>';
            $wrapper .= $body ? '<div class="modal-body">'.$body.'</div>' : '';
            $wrapper .= $footer ? '<div class="modal-footer">'.$footer.'</div>' : '';
        $wrapper .= '</div>
                  </div>';
        return $wrapper;

    }

  /**
   * {@inheritdoc}
   */
    public function getFormModalPrefix($title = '',
                                       $subTitle = '',
                                       $id = '',
                                       $class = '',
                                       $entrancesAnimation = 'fadeInUp',
                                       $exitAnimation = 'fadeOutDown',
                                       $showCloseButton = true
    ){

      // Header
      $header = '<h4 class="modal-form-title">'.$title.'</h4>';
      $header .= '<p>'.$subTitle.'</p>';
      // Footer
      $footer = '';

      $responseHtml = '<div
                        id="'.$id.'"
                        class="modal fade '.$class.'"
                        data-easein="'.$entrancesAnimation.'"
                        data-easeout="'.$exitAnimation.'"
                        role="dialog"
                        aria-hidden="true">
                        ';

      $responseHtml .= '<div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">';
      $responseHtml .= '<span class="modal-arrow-back" style="display: none;"></span>';
      $responseHtml .= $showCloseButton ? '<a type="button" class="close" data-dismiss="modal" aria-hidden="true">×</a>' : '';
      $responseHtml .= $header ? $header : '';
      $responseHtml .= '</div>';
      $responseHtml .= '<div class="modal-body">';


      return $responseHtml;

    }

  /**
   * {@inheritdoc}
   */
  public function getFormModalSuffix(){
    return '</div></div></div></div>';
  }

}