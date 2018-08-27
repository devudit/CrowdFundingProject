(function ($, Drupal) {
  Drupal.behaviors.crowdFundingProject = {
    attach: function (context, settings) {

      var cfpSettings = settings.cfpSettings;
      // project image preview
     if($('.cfp-form-elements .cfp-form-project-image .form-item-project-image .file-link').length > 0){
        var image_src = $('.cfp-form-elements .cfp-form-project-image .form-item-project-image .file-link a').attr('href');
        $('.cfp-form-elements .cfp-form-project-image .image-upload .image-upload-drag figure.image-upload-view').css('background-image', 'url(' + image_src + ')');
     } else {
       $('.cfp-form-elements .cfp-form-project-image .image-upload .image-upload-drag figure.image-upload-view').css('background-image', 'url(/' + cfpSettings.module_url + '/images/default-project.jpg)');
     }

     // user image preview
      if($('.cfp-form-user-fields .cfp-form-user-avatar .form-item-avatar .file-link').length > 0){
        var image_thumb_src = $('.cfp-form-user-fields .cfp-form-user-avatar .form-item-avatar .file-link a').attr('href');
        $('.cfp-form-user-fields .cfp-form-user-avatar .avatar-upload .image-upload-view img').attr('src',image_thumb_src);
      } else {
        $('.cfp-form-user-fields .cfp-form-user-avatar .avatar-upload .image-upload-view img').attr('src','/'+cfpSettings.module_url+'/images/default-avatar.png');
      }

      $(context).find('.package-item').once('package-item-processed').each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $(context).find('.package-item').removeClass('active');
          $(context).find('.package-item input[type="checkbox"]').removeAttr('checked');
          $(this).addClass('active');
          $(this).find('input[type="checkbox"]').attr('checked',true);
        })
      })

    },
    detach: function (context) {
      $(context).find(".package-item").unbind("click");
    }
  };
})(jQuery, Drupal);