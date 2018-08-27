(function ($, Drupal, drupalSettings, window) {

  "use strict";

  var projectMap = {
    map: null,
    iconBase: '/modules/custom/CrowdFundingProject/images/icons/',
    markers: [],
    init: function () {
      projectMap.map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 18,
        center: new google.maps.LatLng(projectMap.getLocations().latitude, projectMap.getLocations().longitude),
        styles: projectMap.getStyle(),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false,
        panControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        overviewMapControl: false,
        rotateControl: false,
        disableDefaultUI: true,
        gestureHandling: 'none',
        zoomControl: false
      });
      projectMap.createMarker();
    },
    getLocations: function () {
      var location = drupalSettings.cfpMapSettings.locations;
      return {
        latitude: location.latitude ? location.latitude : 52.345680256623,
        longitude: location.longitude ? location.longitude : 4.8862566116568,
      }
    },
    createMarker: function () {
      //var marker = new google.maps.Marker({
      //    position: new
      // google.maps.LatLng(projectMap.getLocations().latitude,
      // projectMap.getLocations().longitude), animation:
      // google.maps.Animation.DROP, map: projectMap.map, });
      function HTMLMarker(lat, lng) {
        this.lat = lat;
        this.lng = lng;
        this.pos = new google.maps.LatLng(lat, lng);
      }

      HTMLMarker.prototype = new google.maps.OverlayView();
      HTMLMarker.prototype.onRemove = function () {
      }

      //init your html element here
      HTMLMarker.prototype.onAdd = function () {
        this.div = document.createElement('DIV');
        this.div.className = "map-label";
        this.div.style.position = 'absolute';
        var panes = this.getPanes();
        panes.overlayImage.appendChild(this.div);
      };

      HTMLMarker.prototype.draw = function () {
        var overlayProjection = this.getProjection();
        var position = overlayProjection.fromLatLngToDivPixel(this.pos);
        var panes = this.getPanes();
        this.div.style.left = position.x + 'px';
        this.div.style.top = position.y + 'px';
      };

      HTMLMarker.prototype.getPosition = function () {
        return this.pos;
      };

      //to use it
      var htmlMarker = [];
      htmlMarker[0] = new HTMLMarker(projectMap.getLocations.latitude, projectMap.getLocations.longitude);
      htmlMarker[0].setMap(projectMap.map);
    },
    getStyle: function () {
      return [
        {
          "featureType": "administrative",
          "elementType": "all",
          "stylers": [
            {
              "hue": "#000000"
            },
            {
              "lightness": -100
            },
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "landscape",
          "elementType": "geometry",
          "stylers": [
            {
              "hue": "#dddddd"
            },
            {
              "saturation": -100
            },
            {
              "lightness": -3
            },
            {
              "visibility": "on"
            }
          ]
        },
        {
          "featureType": "landscape",
          "elementType": "labels",
          "stylers": [
            {
              "hue": "#000000"
            },
            {
              "saturation": -100
            },
            {
              "lightness": -100
            },
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "poi",
          "elementType": "all",
          "stylers": [
            {
              "hue": "#000000"
            },
            {
              "saturation": -100
            },
            {
              "lightness": -100
            },
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "road",
          "elementType": "geometry",
          "stylers": [
            {
              "hue": "#bbbbbb"
            },
            {
              "saturation": -100
            },
            {
              "lightness": 26
            },
            {
              "visibility": "on"
            }
          ]
        },
        {
          "featureType": "road",
          "elementType": "labels",
          "stylers": [
            {
              "hue": "#ffffff"
            },
            {
              "saturation": -100
            },
            {
              "lightness": 100
            },
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "road.local",
          "elementType": "all",
          "stylers": [
            {
              "hue": "#ffffff"
            },
            {
              "saturation": -100
            },
            {
              "lightness": 100
            },
            {
              "visibility": "on"
            }
          ]
        },
        {
          "featureType": "transit",
          "elementType": "labels",
          "stylers": [
            {
              "hue": "#000000"
            },
            {
              "lightness": -100
            },
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "water",
          "elementType": "geometry",
          "stylers": [
            {
              "hue": "#ffffff"
            },
            {
              "saturation": -100
            },
            {
              "lightness": 100
            },
            {
              "visibility": "on"
            }
          ]
        },
        {
          "featureType": "water",
          "elementType": "labels",
          "stylers": [
            {
              "hue": "#000000"
            },
            {
              "saturation": -100
            },
            {
              "lightness": -100
            },
            {
              "visibility": "off"
            }
          ]
        }
      ];
    }
  };

  if (typeof define === 'function' && define.amd) {
    define(projectMap);
  }
  else {
    window.projectMap = projectMap;
  }

  console.log(drupalSettings);
  /**
   * Completed drupal behaviour
   * @type {{attach: attach, detach: detach}}
   */
  Drupal.behaviors.completed = {
    attach: function (context, settings) {

      if (!settings.cfpMapSettings.projectCompleted || settings.cfpMapSettings.projectCompleted === "0") {
        $(context).find('ul.quicktabs-tabs').once('quicktabs-tabs-processed').each(function () {
          $(this).find('li:last-child').hide();
        });
      } else{
        $('a[href="/quicktabs/nojs/project_single_page_tabs/3"]').trigger('click');
      }

      if ($('[data-image]').length > 0) {
        $('[data-image]').each(function () {
          $(this).css('background-image', 'url(' + $(this).attr('data-image') + ')');
        });
      }
      // click listiners
      $(context).find(".action-facebook").once("facebook-link-listener").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $('.st_facebook_large > span').trigger('click');
        });
      });
      $(context).find(".action-twitter").once("twitter-link-listener").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $('.st_twitter_large > span').trigger('click');
        });
      });
      $(context).find(".action-linkedin").once("linkedin-link-listener").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $('.st_linkedin_large > span').trigger('click');
        });
      });
      $(context).find(".switch-tab-supporter").once("supporter-tab-link-listener").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $('a[href="/quicktabs/nojs/project_single_page_tabs/2"]').trigger('click');
        });
      });
    },
    detach: function (context) {
      $(context).find(".action-facebook").unbind("click");
      $(context).find(".action-twitter").unbind("click");
      $(context).find(".action-linkedin").unbind("click");
      $(context).find(".switch-tab-supporter").unbind("click");
    }
  }

}(jQuery, Drupal, drupalSettings, window));
