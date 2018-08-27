(function ($, Drupal, drupalSettings, window) {

  "use strict";

  var cfpMap = {
    map: null,
    iconBase: '/modules/custom/CrowdFundingProject/images/icons/',
    markers: [],
    init: function () {
      cfpMap.map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: new google.maps.LatLng(52.345680256623, 4.8862566116568),
        mapTypeId: 'roadmap'
      });
      cfpMap.createMarkers();
    },
    getIcons: function () {
      return {
        cluster: {
          icon: cfpMap.iconBase + 'marker-cluster.svg',
          scaledSize: new google.maps.Size(50, 50),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(0, 0)
        },
        info: {
          icon: cfpMap.iconBase + 'map-marker.png',
          scaledSize: new google.maps.Size(50, 50),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(0, 0)
        }
      };
    },
    getFeatures: function () {
      var markersData = [];
      var locations = drupalSettings.cfpMapSettings.locations;
      locations.forEach(function (item, index, array) {
        var location = {
          position: new google.maps.LatLng(item.latitude, item.longitude),
          type: 'info',
          infoWindow: cfpMap.getInfoWindow(item)
        };
        markersData.push(location);
      });

      return markersData;
    },
    getInfoWindow: function (item) {
      /* TODO: convert ths to dynamic content and will return array */
      var contentString = '<div class="cfp-map-popup">' +
          '<span class="cfp-map-close-project"></span>' +
          '<span class="cfp-project-title small">' + item.projectTitle + '</span>' +
          '<div class="cfp-project-address">' +
          '<span class="map-address">'+item.address1+'</span>' +
          '<span class="map-address">'+item.address2+'</span>' +
          '<span class="map-locality">'+item.postalCode+' '+item.city+'</span></div>'+
          '<a class="cfp-project-link" href="' + item.projectUrl + '">Bekijk actie</a>' +
          '</div>';

      var infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      // *
      // INFOWINDOW CLOSE EVENT
      // *
      google.maps.event.addListener(cfpMap.map, 'click', function () {
        infowindow.close();
      });

      // *
      // START INFOWINDOW CUSTOMIZE.
      // The google.maps.event.addListener() event expects
      // the creation of the infowindow HTML structure 'domready'
      // and before the opening of the infowindow, defined styles are applied.
      // *
      //google.maps.event.addListener(infowindow, 'domready', function() {
      //
      //    // Reference to the DIV that wraps the bottom of infowindow
      //    var iwOuter = $('.gm-style-iw');
      //
      //    /* Since this div is in a position prior to .gm-div style-iw.
      //     * We use jQuery and create a iwBackground variable,
      //     * and took advantage of the existing reference .gm-style-iw for
      // the previous div with .prev(). */ var iwBackground = iwOuter.prev();
      // // Removes background shadow DIV
      // iwBackground.children(':nth-child(2)').css({'display' : 'none'});  //
      // Removes white background DIV
      // iwBackground.children(':nth-child(4)').css({'display' : 'none'});  //
      // Moves the infowindow 115px to the right.
      // iwOuter.parent().parent().css({left: '115px'});  // Moves the shadow
      // of the arrow 76px to the left margin.
      // iwBackground.children(':nth-child(1)').attr('style', function(i,s){
      // return s + 'left: 76px !important;'});  // Moves the arrow 76px to the
      // left margin. iwBackground.children(':nth-child(3)').attr('style',
      // function(i,s){ return s + 'left: 76px !important;'});  // Changes the
      // desired tail shadow color.
      // iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow':
      // 'rgba(72, 181, 233, 0.6) 0px 1px 6px', 'z-index' : '1'});  //
      // Reference to the div that groups the close button elements. var
      // iwCloseBtn = iwOuter.next();  // Apply the desired effect to the close
      // button iwCloseBtn.css({opacity: '1', right: '38px', top: '3px',
      // border: '7px solid #48b5e9', 'border-radius': '13px', 'box-shadow': '0
      // 0 5px #3990B9'});  // If the content of infowindow not exceed the set maximum height, then the gradient is removed. if($('.iw-content').height() < 140){ $('.iw-bottom-gradient').css({display: 'none'}); }  // The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value. iwCloseBtn.mouseout(function(){ $(this).css({opacity: '1'}); }); });

      // return infowindow
      return infowindow;
    },
    hideAllMarkers: function () {
      if(cfpMap.markers.length > 0){
        cfpMap.markers.forEach(function (marker) {
          marker.infowindow.close(cfpMap.map, marker);
        });
      }
    },
    createMarkers: function () {
      var features = cfpMap.getFeatures();
      var icons = cfpMap.getIcons();

      features.forEach(function (feature) {
        var marker = new google.maps.Marker({
          position: feature.position,
          icon: icons[feature.type].icon,
          animation: google.maps.Animation.DROP,
          map: cfpMap.map,
          infowindow: feature.infoWindow
        });
        marker.addListener('click', function () {
          cfpMap.hideAllMarkers();
          cfpMap.map.panTo(marker.getPosition());
          marker.infowindow.open(map, marker);
        });
        // push marker
        cfpMap.markers.push(marker);
      });
    }
  };

  if (typeof define === 'function' && define.amd) {
    define(cfpMap);
  }
  else {
    window.cfpMap = cfpMap;
  }

  Drupal.behaviors.cfpMaps = {
    attach: function (context, settings) {

    }
  };
}(jQuery, Drupal, drupalSettings, window));