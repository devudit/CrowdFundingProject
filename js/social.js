(function ($, Drupal, drupalSettings, window) {

  "use strict";

  var redirectData = {
    id: 0,
    wrapper: '#cfp_user_login_form_block_wrapper',
    redirect: '_none'
  };

  if (drupalSettings.cfpSocial.fb_app_id) {
    window.fbAsyncInit = function () {
      // FB JavaScript SDK configuration and setup
      FB.init({
        appId: drupalSettings.cfpSocial.fb_app_id, // FB App ID
        cookie: true,  // enable cookies to allow the server to access the
                       // session
        xfbml: true,  // parse social plugins on this page
        version: 'v2.8' // use graph api version 2.7
      });

      // Check whether the user already logged in
      FB.getLoginStatus(function (response) {
        if (response.status === 'connected') {
          //display user data
          //getFbUserData();
        }
      });
    };
  }

  // Facebook login with JavaScript SDK
  var facebookLogin = function () {
    window.fbAsyncInit();
    FB.login(function (response) {
      if (response.authResponse) {
        getData();
      }
      else {
        console.log('there is some error in facebook login')
      }
    }, {scope: 'email'});
  };

  var getData = function () {
    FB.api('/me', {
          locale: 'en_US',
          fields: 'id,first_name,last_name,email,link,gender,locale,picture'
        },
        function (response) {
          resolveLogin(response);
        });
  };

  var resolveLogin = function (data) {
    if (data !== undefined) {
      $.ajax({
        type: "POST",
        url: '/cfp/social/login',
        data: {
          login_data: data,
          redirect_data: JSON.stringify(redirectData)
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
        }
      });
    }
  };

  var attachLinkListeners = function () {
    $('.facebookLogin').click(function (e) {
      e.preventDefault();
      redirectData.id = $(this).attr('data-id');
      redirectData.wrapper = $(this).attr('data-wrapper');
      redirectData.redirect = $(this).attr('data-redirect');
      facebookLogin();
    });
  };

  /**
   * Completed drupal behaviour
   * @type {{attach: attach, detach: detach}}
   */
  Drupal.behaviors.social = {
    attach: function (context, settings) {

      attachLinkListeners();

    },
    detach: function (context) {
      $('.facebookLogin').unbind('click');
    }
  }

}(jQuery, Drupal, drupalSettings, window));