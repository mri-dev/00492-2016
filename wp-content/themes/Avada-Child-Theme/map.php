<?php
  get_header();
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_MAP?>-page-view bindwindowheight">
      <div class="data-list">
        <div class="searcher">
          <?php echo do_shortcode("[listing-searcher view='map']"); ?>
        </div>
        <div class="listing" id="listing" style="padding-top: 202px;">
          <div class="listing-header">
            Ingatlanok betöltése...<i class="fa fa-spin fa-spinner"></i>
          </div>
          <div class="listing-items" id="listing-items">
            <div class="result" id="result-page-0"></div>
            <div id="load-more-page" data-page="0" style="display:none;">További <span id="pagenum">0</span> db ingatlan betöltése <i class="fa fa-arrow-circle-right"></i></div>
          </div>
        </div>
      </div>
      <div class="map-view" id="map"></div>
      <div class="item-loader" id="itempreloader">
        <div class="text">
          <i class="fa fa-spin fa-spinner"></i> Ingatlanok betöltése...
        </div>
      </div>
      <script type="text/javascript">
        var styledMapType = new google.maps.StyledMapType( [
        {
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#f3f3f3"
            }
          ]
        },
        {
          "elementType": "labels.icon",
          "stylers": [
            {
              "visibility": "on"
            }
          ]
        },
        {
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#f8941e"
            }
          ]
        },
        {
          "elementType": "labels.text.stroke",
          "stylers": [
            {
              "color": "#f5f5f5"
            }
          ]
        },
        {
          "featureType": "administrative.land_parcel",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#bdbdbd"
            }
          ]
        },
        {
          "featureType": "poi",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#e8cdb6"
            }
          ]
        },
        {
          "featureType": "poi",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#0ec5c7"
            }
          ]
        },
        {
          "featureType": "poi.park",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#c9eccc"
            }
          ]
        },
        {
          "featureType": "poi.park",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#9e9e9e"
            }
          ]
        },
        {
          "featureType": "road",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#ffffff"
            }
          ]
        },
        {
          "featureType": "road.arterial",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#757575"
            }
          ]
        },
        {
          "featureType": "road.highway",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#dadada"
            }
          ]
        },
        {
          "featureType": "road.highway",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#616161"
            }
          ]
        },
        {
          "featureType": "road.local",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#9e9e9e"
            }
          ]
        },
        {
          "featureType": "transit.line",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#e5e5e5"
            }
          ]
        },
        {
          "featureType": "transit.station",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#eeeeee"
            }
          ]
        },
        {
          "featureType": "water",
          "elementType": "geometry",
          "stylers": [
            {
              "color": "#54b4e4"
            }
          ]
        },
        {
          "featureType": "water",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#22749a"
            }
          ]
        }
      ],
      {name: '<?=__('Letisztult', 'gh')?>'});

      var mapopt = {
        center: {lat: 28.259343, lng: -16.607619},
        scrollwheel: false,
        zoom: 10,
        mapTypeControlOptions: {
          mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
        }
      };
      var markers = [];
      var map;

      map  = new google.maps.Map(document.getElementById('map'), mapopt);
      map.mapTypes.set('styled_map', styledMapType);
      map.setMapTypeId('styled_map');

      (function($){
        var getqry = {};
        var lastresult = null;
        var ir = 0;
        var currentWindow;
        var currentindexhovered = 0;

        loadList(1);

        map.addListener('click', function() {
            if (currentWindow) {
              currentWindow.close();
            }
        });

        var contentHeight = $(window).height();

        $('.bindwindowheight').css({
          height: contentHeight,
          minHeight: contentHeight
        });

        $(window).resize(function(){
          var contentHeight = $(window).height();

          if ($(window).width() > 480) {
            contentHeight -= $('.fusion-header-v3').height();
            contentHeight -= $('.fusion-page-title-bar').height();
            contentHeight -= 200;
          } else {
            contentHeight -= 43;
          }

          $('.bindwindowheight').css({
            height: contentHeight,
            minHeight: contentHeight
          });

        });

        $('#tgl-map-results').click(function(){
          var st = $(this).data('state');
          console.log(st);

          if (st == 'closed') {
            $(this).data('state', 'opened');
            $('body.map_template_page .map-view').css({
              flexBasis: '0%'
            });
            $('body.map_template_page .data-list').css({
              flexBasis: '100%'
            });
          } else {
            $(this).data('state', 'closed');
            $('body.map_template_page .map-view').css({
              flexBasis: '100%'
            });
            $('body.map_template_page .data-list').css({
              flexBasis: '0%'
            });
          }

          $('#listing').css({
            paddingTop: $('.searcher').height()
          });

        });

        function loadList(page) {
          $('.listing-header').html('<i class="fa fa-spin fa-spinner"></i> Ingatlanok betöltése...');
          $('#load-more-page').hide(0);
          getqry.page = page;
          getqry.limit = 10;
          getqry.get = '<?=json_encode($_GET)?>';
          $('#pagenum').text(getqry.limit);
          $.post('<?=get_ajax_url('maplist')?>', getqry, function(r){
            lastresult = r;
            pushItems(r.data);
            $('#itempreloader').fadeOut(400);
          }, 'json' );
        }

        function pushItems(list) {
          var datalist = '';
          $.each(list, function(i,e){
            datalist += pushItem(e);
          });

          if (getqry.page == 1) {
            var pageinfo = '<div class="page-info page'+lastresult.page.current+'">'+lastresult.data.length+' ingatlan megjelenítve:</div>';
          } else if(getqry.page == 2) {
            var pageinfo = '<div class="page-info page'+lastresult.page.current+'">További ingatlanok:</div>';
          } else {
            var pageinfo = '';
          }

          var h = '<div class="result page'+lastresult.page.current+'" id="result-page-'+lastresult.page.current+'">'+
          pageinfo+
          datalist+
          '</div>';
          var cp = getqry.page - 1;
          $(h).insertAfter('#listing-items #result-page-'+cp);
          $('.listing-header').html(lastresult.data_info.total_items+' db ingatlant találtunk');
          $('li.show-on-map.only-in-mobile .num').addClass('has').text(lastresult.data_info.total_items);
          getqry.page++;
          var next_page = lastresult.page.current + 1;

          if(next_page <= lastresult.page.max){
            $('#load-more-page').data('page', next_page).show(0);
          } else {
            $('#load-more-page').data('page', next_page).hide(0);
          }

          $('.prop-item').bind('click',function(e){
            e.preventDefault();
            e.stopPropagation();
            var ix = $(this).data('index');
            var id = $(this).data('id');
            var ww = $(window).width();

            if(ix != currentindexhovered) {
              if(ww <= 480) {
                $('body.map_template_page .map-view').css({
                  flexBasis: '100%'
                });
                $('body.map_template_page .data-list').css({
                  flexBasis: '0%'
                });

                $('#tgl-map-results').data('state', 'closed');
              }
              currentindexhovered = ix;
              var current_marker = markers[id];
              var lat = parseFloat(current_marker.position.lat());
              var lng = parseFloat(current_marker.position.lng());
              if (!isNaN(lat) && !isNaN(lng)) {
                map.setCenter(current_marker.position);
                map.setZoom(16);
                google.maps.event.trigger(current_marker, 'click');
              }
            }
          });
        }

        $('#listing').css({
          paddingTop: $('.searcher').height()
        });

        $('#load-more-page').click(function(){
          var p = $(this).data('page');
          loadList(p);
        });

        function prepareParamValue(e) {
          if(e.value == '' || e.value == null || e.value == 'null'){
            return false;
          }

          if(e.name == 'Ingatlan állapota') {
            var s = parseInt(e.value);
            e.value = '';
            for (var i = 0; i < s; i++) {
              e.value += '<i class="fa fa-star"></i>';
            }
          }
          return e;
        }

        function pushItem(i) {
          ir++;

          var marker = new google.maps.Marker({
              position: new google.maps.LatLng(i.gps.lat,i.gps.lng),
              title: i.title
          });
          marker.set("id", i.id);

          var paramcontent = '';

          if (i.params.length > 0) {
            paramcontent += '<table class="paramtable">';
            $.each(i.params, function(i,e){
              e = prepareParamValue(e);
              if(e) {
                paramcontent += '<tr><td>'+e.name+'</td><td>'+e.value+( (e.after) ? ' '+e.after : '' )+'</td></tr>';
              }
            });
            paramcontent += '</table>';
          }

          var boxContent = '<div class="map-box-content">'+
          '<div class="img"><img src="'+i.image+'" alt="'+i.title+'" /></div>'+
          '<div class="title"><h2>'+i.title+'</h2></div>'+
          '<div class="pos">'+i.region+'</div>'+
          '<div class="desc">'+i.desc+'</div>'+
          '<div class="params">'+paramcontent+'</div>'+
          '<div class="price">'+i.price_text+'</div>'+
          '<div class="read"><a href="'+i.url+'">Részletek <i class="fa fa-arrow-circle-right"></i></a></div>'+
          '<div class="clearfix"></div>'+
          '</div>';

          marker.addListener('click', function(){
            var infowindow = new google.maps.InfoWindow({
              maxWidth: 350
            });
            google.maps.event.addListener(infowindow, 'domready', function() {
               var iwOuter = $('.gm-style-iw');
               var iwBackground = iwOuter.prev();
               iwBackground.children(':nth-child(2)').css({'display' : 'none'});
               iwBackground.children(':nth-child(4)').css({'display' : 'none'});

            });
            infowindow.setContent(boxContent);
            map.setZoom(13);
            map.setCenter(marker.getPosition());
            var id = marker.get("id");
            $('.prop-item.focused').removeClass('focused');
            $('.prop-item[data-id=\''+id+'\']').addClass('focused');

            var itemtop = $('.prop-item[data-id=\''+id+'\']').offset().top;
            var holdertop = $('#listing-items').offset().top;

            $('.listing-items').animate({
              scrollTop: itemtop-holdertop
            }, 100);

            if( currentWindow ) {
              currentWindow.close();
            }
            currentWindow = infowindow;
            infowindow.open(map, marker);
          });

          markers[i.id] = marker;
          marker.setMap(map);

          return '<div class="prop-item itemindex'+ir+'" data-id="'+i.id+'" data-index="'+ir+'">'+
          '<div class="img"><img src="'+i.image+'" alt="'+i.title+'"/></div>'+
          '<div class="dt">'+
          '<div class="title"><a href="'+i.url+'" target="_blank">'+i.title+'</a></div>'+
          '<div class="info"><span class="price">'+i.price_text+'</span> <span class="pos">'+i.region+'</span></div>'+
          '<div class="desc hided '+((i.desc !='')?'hasdata':'')+'">'+i.desc+'</div>'+
          '<div class="label '+((!i.label)?'hided hasdata':'')+'">'+((i.label) ? '<span style="background:'+i.label.bg+' ">'+i.label.text+'</span>' :'')+'</div>'+
          '</div>'+
          '</div>';
        }


      })(jQuery);

      </script>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
