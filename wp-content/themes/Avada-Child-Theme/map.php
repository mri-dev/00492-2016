<?php
  get_header();
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_MAP?>-page-view">
      <div class="data-list">
        <div class="searcher">
          <?php echo do_shortcode("[listing-searcher view='map']"); ?>
        </div>
        <div class="listing" id="listing" style="padding-top: 237px;">
          <div class="listing-header">
            Ingatlanok betöltése...<i class="fa fa-spin fa-spinner"></i>
          </div>
          <div class="listing-items" id="listing-items">
            <div class="result" id="result-page-0"></div>
            <div id="load-more-page" data-page="0" style="display:none;">További ingatlanok betöltése</div>
          </div>
        </div>
      </div>
      <div class="map-view" id="map"></div>
      <script type="text/javascript">
      (function($){
        var getqry = {};
        var lastresult = null;

        loadList(1);

        function loadList(page) {
          $('.listing-header').html('Ingatlanok betöltése...<i class="fa fa-spin fa-spinner"></i>');
          $('#load-more-page').hide(0);
          getqry.page = page;
          getqry.limit = 2;
          $.post('<?=get_ajax_url('maplist')?>', getqry, function(r){
            lastresult = r;
            console.log(r);
            pushItems(r.data);
          }, 'json' );
        }

        function pushItems(list) {
          var datalist = '';
          $.each(list, function(i,e){
            datalist += pushItem(e);
          });
          var h = '<div class="result page'+lastresult.page.current+'" id="result-page-'+lastresult.page.current+'">'+
          datalist+
          '</div>';
          var cp = getqry.page - 1;
          $(h).insertAfter('#listing-items #result-page-'+cp);
          $('.listing-header').html(lastresult.data_info.total_items+' db ingatlant találtunk');
          getqry.page++;
          var next_page = lastresult.page.current + 1;

          if(next_page <= lastresult.page.max){
            $('#load-more-page').data('page', next_page).show(0);
          } else {
            $('#load-more-page').data('page', next_page).hide(0);
          }
        }

        $('#load-more-page').click(function(){
          var p = $(this).data('page');
          loadList(p);
          console.log(p);
        });

        function pushItem(i) {
          return '<div class="prop-item">'+
          '<div class="img"><img src="'+i.image+'" alt="'+i.title+'"/></div>'+
          '<div class="dt">'+
          '<div class="title"><a href="'+i.url+'" target="_blank">'+i.title+'</a></div>'+
          '<div class="info"><span class="price">'+i.price_text+'</span> <span class="pos">'+i.region+'</span></div>'+
          '<div class="desc '+((i.desc == '')?'hided':'')+'">'+i.desc+'</div>'+
          '<div class="label '+((!i.label)?'hided':'')+'">'+((i.label) ? '<span style="background:'+i.label.bg+' ">'+i.label.text+'</span>' :'')+'</div>'+
          '</div>'+
          '</div>';
        }
      })(jQuery);

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

        map  = new google.maps.Map(document.getElementById('map'), mapopt);
        map.mapTypes.set('styled_map', styledMapType);
        map.setMapTypeId('styled_map');

      </script>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
