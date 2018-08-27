(function ($, Drupal, drupalSettings, window) {

  "use strict";

  function inlineCommentAjax(action, data_id, parent_id, comment) {
    $.ajax({
      type: "POST",
      url: '/cfp/inline-comment/' + action,
      data: {
        data_id: data_id,
        parent_id: parent_id,
        comment: comment
      },
      success: function (data) {
        var ajaxObject = Drupal.ajax({
          url: "",
          base: false,
          element: false,
          progress: false
        });
        // Then, simulate an AJAX response having arrived, and let the Ajax
        // system handle it.
        ajaxObject.success(data, "success");
      },
      dataType: 'json'
    });
  }

  /**
   * comment events behaviour
   * @type {{attach: attach, detach: detach}}
   */
  Drupal.behaviors.comment = {
    attach: function (context, settings) {
      /* comment forms */
      $('.cpf-comment-form-body #edit-comment').focus(function (e) {
        e.preventDefault();
        $(this).parents('.cpf-comment-form-body').addClass('focussed');
        $(this).parents('.cpf-comment-form-body').next().addClass('focussed');
      }).blur(function (e) {
        e.preventDefault();
        $(this).parents('.cpf-comment-form-body').removeClass('focussed');
        $(this).parents('.cpf-comment-form-body').next().removeClass('focussed');
      });
      $('.comment-footer .comment-input').focus(function (e) {
        e.preventDefault();
        $(this).parents('.comment-footer').addClass('focussed');
      });
      $(document).click(function (e) {
        var $ele = $(e.target);
        var $isAction = $ele.hasClass('.comment-form-actions');
        var $parents = $ele.parents('.comment-footer').length;
        if (!$isAction && !$parents) {
          $('.comment-footer, .comment-footer .comment-input').removeClass('focussed');
        }
      });

      // Add photo and video
      $('.action-add-photo').click(function (e) {
        e.preventDefault();
        if ($('.comment-image-wrapper').hasClass('is-active')) {
          $('.comment-image-wrapper').removeClass('is-active');
        }
        else {
          $('.comment-image-wrapper').addClass('is-active');
        }
      });
      $('.action-add-video').click(function (e) {
        e.preventDefault();
        if ($('.form-item-comment-video').hasClass('is-active')) {
          $('.form-item-comment-video').removeClass('is-active');
        }
        else {
          $('.form-item-comment-video').addClass('is-active');
        }
      });

      // load single line comment form
      $(context).find('.action-load-comment').once('action-load-comment-processed').each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          if ($(this).parents('.m-wallpost').hasClass('is-active')) {
            $(this).parents('.m-wallpost').removeClass('is-active')
          }
          else {
            $(this).parents('.m-wallpost').addClass('is-active')
          }
        });
      });
      $(context).find('.action-inline-comment').once('action-inline-comment-processed').each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          var dataId = $(this).attr('data-id');
          var dataCid = $(this).attr('data-cid');
          var comment = $(this).parents('.m-comment-form').find('textarea.comment-input').val();
          inlineCommentAjax('save', dataId, dataCid, comment)
        });
      });
      $(context).find('.action-delete').once('action-delete-processed').each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          var dataId = 0;
          var dataCid = $(this).attr('data-cid');
          var comment = '';
          inlineCommentAjax('delete', dataId, dataCid, comment)
        })
      });

      $(context).find('.show-more-less').once('show-more-less-processed').each(function () {
        $(this).click(function (e) {
          if ($(this).parents('.wallpost-body').hasClass('is-show-more')) {
            $(this).parents('.wallpost-body').removeClass('is-show-more');
            $(this).text('Toon meer');
          }
          else {
            $(this).parents('.wallpost-body').addClass('is-show-more');
            $(this).text('Toon minder');
          }
        })
      });

      //add image to background
      if ($('.comment-image-wrapper .form-item-comment-image .comment-image-file span.file-link').length > 0) {
        $('.comment-image-wrapper').addClass('is-active').addClass('hide-plus');
        var image_src = $('.comment-image-wrapper .form-item-comment-image .comment-image-file span.file-link a').attr('href');
        $('.comment-image-wrapper').css('background-image', 'url(' + image_src + ')');
      }

      // Drupal ajax prototype
      Drupal.AjaxCommands.prototype.cfpMessageCommand = function (ajax, response, status) {
        if ($('.' + response.wrapper).length > 0) {
          $('.' + response.wrapper).html(response.message);
          $('.' + response.wrapper).addClass(response.type);
        }
      };
      Drupal.AjaxCommands.prototype.deleteCommentCommand = function (ajax, response, status) {
        if (response.status) {
          $(response.wrapper).fadeOut(300, function () {
            $(this).remove();
          });
        }
      };
      Drupal.AjaxCommands.prototype.updateInlineCommentCommand = function (ajax, response, status) {
        if (response.status) {
          if ($(response.wrapper + ' ul.comments').length > 0) {
            $(response.wrapper + ' ul.comments').prepend(response.html);
            $(response.wrapper + ' textarea').val('');
          } else {
            $(response.wrapper).prepend('<ul class="comments">'+response.html+"</ul>");
            $(response.wrapper + ' textarea').val('');
          }
        }
      };
      Drupal.AjaxCommands.prototype.updateCommentCommand = function (ajax, response, status) {
        if (response.status) {
          $(response.wrapper).after(response.html);
        }
      }
    },
    detach: function (context) {
      $('.comment-footer #edit-comment').unbind('focus').unbind('blur');
      $('.cpf-comment-form-body #edit-comment').unbind('focus').unbind('blur');
      $('.m-comment-form').unbind('focusOut').unbind('blur');
      $('.action-add-photo').unbind('click');
      $('.action-add-video').unbind('click');
      $(context).find('.action-load-comment').unbind('click');
      $(context).find('.action-inline-comment').unbind('click');
      $(context).find('.show-more-less').unbind('click');
      $(context).find('.action-delete').unbind('click');
    }
  }

}(jQuery, Drupal, drupalSettings, window));