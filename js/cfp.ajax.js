(function ($, Drupal, drupalSettings, window) {

  "use strict";

  var initializedModal, cpfModal;

  /**
   * Initialize modal wrapper
   * Must call on top of any behaviour
   */
  function initialize() {
    if (!initializedModal) {
      initializedModal = true;
      cpfModal = $("#cfpModalsWrapper");
    }
  }

  /**
   * Load modal data
   * By ajax request
   * @param type string|array
   * @param id integer
   */
  function loadModal(type, id) {
    if (Array.isArray(type)) {
      type.forEach(function (item, index, itemArr) {
        runAjax(item.type, item.id);
      });
    }
    else {
      runAjax(type, id);
    }
  }

  function loadLoginModal(type, id, redirect_url, anim_in, anim_out) {
    type = (type == undefined) ? 'login' : type;
    id = (id == undefined) ? 0 : id;
    redirect_url = (redirect_url == undefined) ? '_none' : redirect_url;
    anim_in = (anim_in == undefined) ? 'fadeInUp' : anim_in;
    anim_out = (anim_out == undefined) ? 'fadeOutDown' : anim_out;

    $.ajax({
      url: "/cfp/login/ajax/" + type + "/" + id + "/" + redirect_url + '/' + anim_in + '/' + anim_out,
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
      }
    });
  }

  /**
   * Run modal ajax
   * Hits drupal ajax endpoint
   * @param type string
   * @param id integer
   */
  function runAjax(type, id) {
    // TODO: convert this to drupal ajax
    if (id == undefined) {
      id = 0;
    }

    $.ajax({
      url: "/cfp/ajax/" + type + "/" + id,
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
      }
    });
  }

  /**
   * Support for multi step modal window
   * @param direction
   * @param step
   */
  function moveTo(direction, step) {
    // TODO: convert this to drupal ajax
    if (direction === undefined) {
      direction = 'next';
    }
    if (step === undefined || step === 0) {
      step = 1;
    }
    $.ajax({
      url: "/cfp/step/" + direction + "/" + step,
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
      }
    });
  }

  /**
   * Website click listeners
   * This will attach click listeners to elements
   * You will also need to detach it for garbage collection.
   * @param context
   */
  function clickListeners(context) {
    $(context).find(".showProfile").once("profile-link-listener").each(function () {
      $(this).click(function (e) {
        e.preventDefault();
        loadModal("profile", $(this).attr('data-id'));
      });
    });
    $(context).find(".loadDonateForm").once("donate-link-listener").each(function () {
      $(this).click(function (e) {
        e.preventDefault();
        var donationModal = cpfModal.find('.cfpModalContentWrapper #donate-form');
        if (donationModal.length > 0) {
          presentModal('donate-form');
        }
        else {
          loadModal('donate', $(this).attr('data-id'));
        }
      });
    });
    $(".cfpLoginForm").once("cfp-login-link-listener").each(function () {
      $(this).click(function (e) {
        e.preventDefault();
        var loginModal = cpfModal.find('.cfpModalContentWrapper #logn-form');
        if (loginModal.length > 0) {
          presentModal('logn-form');
        }
        else {
          var id = $(this).attr('data-id');
          var redirect_url = $(this).attr('data-redirect');
          var anim_in = $(this).attr('data-animin');
          var anim_out = $(this).attr('data-animout');
          var type = $(this).attr('data-type');
          if(drupalSettings.user.uid > 0){
            window.location = $(this).attr('href');
          } else {
            loadLoginModal(type, id, redirect_url, anim_in, anim_out);
          }
        }
      });
    });
  }

  /**
   * Present modal
   * Initialize and present modal
   *
   * @param id
   */
  function presentModal(id) {
    if (id) {
      $('#' + id).modal({show: false});
      $('#' + id).modal('show');
    }
  }

  /**
   * Insert modal (ajax response) to modal content wrapper
   * This will vanish all previous modals from wrapper
   * @param html
   * @param id
   */
  function insertModalHtml(html, id, exitAnimation) {
    // remove old modal
    var oldModal = cpfModal.find('.cfpModalContentWrapper .modal');
    if (oldModal.length > 0) {
      if(exitAnimation){
        oldModal.attr('data-easeout',exitAnimation);
      }
      oldModal.modal({show: false});
      oldModal.modal('hide');
      // ugly fix
      $('body').removeClass('modal-open');
      $('.modal-backdrop').remove();
      // end ugly fix
      setTimeout(function () {
        cpfModal.find('.cfpModalContentWrapper').html(html);
        Drupal.attachBehaviors($(html));
        presentModal(id);
      }, 250);
    }
    else {
      cpfModal.find('.cfpModalContentWrapper').html(html);
      Drupal.attachBehaviors($(html));
      presentModal(id);
    }
  }
  
  function destinationRedirect(closePopup, destination) {
    if(closePopup){
      hideAllActiveModal()
    }
    if(destination.length) {
      window.location = destination;
    } else {
      window.location.reload();
    }
  }
  
  function destinationReload(closePopup) {
    if(closePopup){
      hideAllActiveModal();
    }
    window.location.reload();
  }

  function hideAllActiveModal(){
    $('.modal').modal('hide');
  }

  /**
   * Drupal Ajax behaviours and ajax prototypes
   * @type {{attach: attach, detach: detach}}
   */
  Drupal.behaviors.cfpAjax = {
    attach: function (context, settings) {
      if (window.XMLHttpRequest) {
        initialize();
        clickListeners(context);
      }

      /* Ajax Prototypes */
      Drupal.AjaxCommands.prototype.loadHtmlCommand = function (ajax, response, status) {
        insertModalHtml(response.html, response.htmlId);
      };
      Drupal.AjaxCommands.prototype.loadFormCommand = function (ajax, response, status) {
        insertModalHtml(response.html, response.htmlId, response.exitAnimation);
      };
      Drupal.AjaxCommands.prototype.runAjaxCommand = function (ajax, response, status) {
        if(response.type === 'register' || response.type === 'login'){
          loadLoginModal(response.type, response.id, 'Modal:PaymentForm', 'fadeInRight', 'fadeOutLeft');
        } else {
          loadModal(response.type, response.id);
        }
      };
      Drupal.AjaxCommands.prototype.stepCommand = function (ajax, response, status) {
        var currentStep = response.stepId;
        if (response.direction === 'next') {

        }
        else if (response.direction === 'prev') {

        }
      };
      Drupal.AjaxCommands.prototype.destinationRedirectCommand = function (ajax, response, status) {
        destinationRedirect(response.closePopup, response.destination);
      };
      Drupal.AjaxCommands.prototype.refreshPageCommand = function (ajax, response, status) {
        destinationReload(response.closePopup);
      };
      Drupal.AjaxCommands.prototype.updateFragmentCommand = function (ajax, response, status) {
        $(context).find("#"+response.wrapper).html(response.markup);
      };
    },
    detach: function (context) {
      $(context).find(".showProfile").unbind("click");
      $(context).find(".loadDonateForm").unbind("click");
      $(".cfpLoginForm").unbind("click");
    }
  };

  /**
   * Bootstrap css animation behaviour
   * @type {{attach: attach}}
   */
  Drupal.behaviors.cssVelocity = {
    attach: function (context, settings) {
      $(".modal").each(function (index) {
        $(this).on('show.bs.modal', function (e) {
          var open = $(this).attr('data-easein');
          $('.modal .modal-dialog').attr('class', 'modal-dialog  ' + open + '  animated');
        });
        $(this).on('hide.bs.modal', function (e) {
          var close = $(this).attr('data-easeout');
          $('.modal .modal-dialog').attr('class', 'modal-dialog  ' + close + '  animated');
        });
      });
    }
  };

  // @depricated animation behaviors
  //Drupal.behaviors.velocity = {
  //  attach: function (context, settings) {
  //    $(".modal").each(function(index) {
  //      $(this).on('show.bs.modal', function(e) {
  //        var open = $(this).attr('data-easein');
  //        if (open == 'shake') {
  //          $('.modal-dialog').velocity('callout.' + open);
  //        } else if (open == 'pulse') {
  //          $('.modal-dialog').velocity('callout.' + open);
  //        } else if (open == 'tada') {
  //          $('.modal-dialog').velocity('callout.' + open);
  //        } else if (open == 'flash') {
  //          $('.modal-dialog').velocity('callout.' + open);
  //        } else if (open == 'bounce') {
  //          $('.modal-dialog').velocity('callout.' + open);
  //        } else if (open == 'swing') {
  //          $('.modal-dialog').velocity('callout.' + open);
  //        } else {
  //          $('.modal-dialog').velocity('transition.' + open);
  //        }
  //      });
  //      $(this).on('hide.bs.modal', function(e) {
  //        var close = $(this).attr('data-easeout');
  //        if (close == 'shake') {
  //          $('.modal-dialog').velocity('callout.' + close);
  //        } else if (close == 'pulse') {
  //          $('.modal-dialog').velocity('callout.' + close);
  //        } else if (close == 'tada') {
  //          $('.modal-dialog').velocity('callout.' + close);
  //        } else if (close == 'flash') {
  //          $('.modal-dialog').velocity('callout.' + close);
  //        } else if (close == 'bounce') {
  //          $('.modal-dialog').velocity('callout.' + close);
  //        } else if (close == 'swing') {
  //          $('.modal-dialog').velocity('callout.' + close);
  //        } else {
  //          $('.modal-dialog').velocity('transition.' + close);
  //        }
  //      });
  //    });
  //  }
  //};

  /**
   * Dynamic click events behaviours
   * @type {{attach: attach, detach: detach}}
   */
  Drupal.behaviors.clickElements = {
    attach: function (context, settings) {
      /* donation label click*/
      $('.donation-select li a').click(function (e) {
        e.preventDefault();
        $('.donation-amount-field').val($(this).attr('data-price'));
      });
      $('#edit-amount').keyup(function (e) {
        e.preventDefault();
        var number = $(this).val();

        var cl =  $(this).parents('.donation-amount').attr("class").split(" ");
        var newcl =[];
        for(var i=0;i<cl.length;i++){
          var r = cl[i].search(/number+/);
          if(r)newcl[newcl.length] = cl[i];
        }
        $(this).parents('.donation-amount').removeClass().addClass(newcl.join(" "));

        if(number.toString().length > 2 &&
            number.toString().length <= 6
        ){
          $(this).parents('.donation-amount').addClass('number-medium');
        } else if(number.toString().length > 6 &&
            number.toString().length <= 9
        ){
          $(this).parents('.donation-amount').addClass('number-large');
        } else if(number.toString().length > 9){
          $(this).parents('.donation-amount').addClass('number-x-large');
        } else {
          $(this).parents('.donation-amount').addClass('number-x-small');
        }
      });

      /* modal back arrow */
      $('.modal-arrow-back').click(function (e) {
        e.preventDefault();
        //var step =
        // $(this).parents().parents('.modal').attr('class').match(/\d+/);
        // moveTo('prev',step);
      })

    },
    detach: function (context) {
      $('#donation-select li a').unbind('click');
      $('.modal-arrow-back').unbind('click');
      $('#edit-amount').unbind('keyup');
    }
  }

}(jQuery, Drupal, drupalSettings, window));