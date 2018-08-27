(function ($, Drupal, drupalSettings, window) {

  "use strict";

  console.log(drupalSettings);

  var gMapSearch = {
    init: function () {
      $("#edit-map-search").geocomplete({
        map: ".map_canvas",
        details: "form",
        types: ["geocode", "establishment"],
        markerOptions: {
          draggable: true
        },
        mapOptions: {
          zoom: 14
        }
      });
      $("#edit-map-search").bind("geocode:dragged", function(event, latLng){
        $("#edit-map-search").geocomplete("find", latLng.lat() + "," + latLng.lng());
      });
    }
  };

  if (typeof define === 'function' && define.amd) {
    define(gMapSearch);
  }
  else {
    window.gMapSearch = gMapSearch;
  }

  /**
   * Completed drupal behaviour
   * @type {{attach: attach, detach: detach}}
   */
  Drupal.behaviors.location = {
    attach: function (context, settings) {

    },
    detach: function (context) {

    }
  }

}(jQuery, Drupal, drupalSettings, window));