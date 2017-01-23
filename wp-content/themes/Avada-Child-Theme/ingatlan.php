<?php
  get_header();
  $cp_page = $wp_query->query_vars['cp'];
  $xs = explode("-",$wp_query->query_vars['urlstring']);
  $ingatlan_id = end($xs);

  $properties = new Properties(array(
    'id' => $ingatlan_id,
    'post_status' => array('publish'),
  ));
  $property = $properties->getList();
  $prop = $property[0];
  if (!$prop || $prop->StatusKey() != 'publish') {
    wp_redirect('/');
  }
  $properties->logView();
  $regions = $prop->Regions();

?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_INGATLAN?>-page-view">
      <div class="page-holder">
        <div class="data-top">
          <div class="data-top-left">
            <div class="cwrapper">
              <div class="images">
                <div class="profil" id="profilimg">
                  <a data-rel="iLightbox[p<?=$prop->ID()?>]" class="fusion-lightbox" data-title="<?=$prop->Title()?>" href="<?=$prop->ProfilImg()?>"><img src="<?=$prop->ProfilImg()?>" alt=""></a>
                </div>
                <?
                  $pimgid = $prop->ProfilImgID();
                  $images = $prop->Images();
                  $imn    = $prop->imageNumbers();
                  $newimgs = array();
                  $newimgs[$pimgid] = $images[$pimgid];
                  unset($images[$pimgid]);
                  foreach ($images as $iid => $iv) {
                    $newimgs[$iid] = $iv;
                  }
                ?>
                <? foreach( $newimgs as $img ): if($img->ID == $pimgid){ continue; } ?>
                  <a href="<?=$img->guid?>" data-rel="iLightbox[p<?=$prop->ID()?>]" style="display: none;" class="fusion-lightbox" data-title="<?=$prop->Title()?>"><img src="<?=$img->guid?>" alt="<?=$prop->Title()?>" /></a>
                <? endforeach; ?>
                <? if(  $imn > 1 ): ?>
                <div class="stack">
                  <div class="stack-wrapper">
                    <div class="items image-slide">
                      <? foreach( $newimgs as $img ): ?>
                      <div class="i">
                        <img src="<?=$img->guid?>" alt="<?=$prop->Title()?>" />
                      </div>
                      <? endforeach; ?>
                    </div>
                  </div>
                </div>
                <? endif; ?>
              </div>
            </div>
          </div>
          <div class="data-top-right">
            <div class="title">
              <h1><?=$prop->Title()?></h1>
              <div class="subtitle">
                <span class="addr"><i class="fa fa-map-marker"></i> <?php $regtext = ''; foreach ($regions as $r ): $regtext .= $r->name.' / '; endforeach; $regtext = rtrim($regtext, ' / '); ?><?=$regtext?></span>

                <!-- <strong><?=$prop->multivalue_list($prop->PropertyType(true), true, '/'.SLUG_INGATLAN_LIST.'/?c=#value#')?></strong>-->
              </div>
            </div>
            <div class="properties">
              <div class="list">
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/szoba.svg" alt="<?=__('Szobák száma', 'ti')?>"></div>
                   <?=__('Szobák száma', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_room_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/telek-alapterulet.svg" alt="<?=__('Telek területe', 'ti')?>"></div>
                   <?=__('Telek területe', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_lot_size'))?sprintf(__('%d nm', 'ti'), $v):'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/alapterulet.svg" alt="<?=__('Alapterület', 'ti')?>"></div>
                   <?=__('Alapterület', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_property_size'))?sprintf(__('%d nm', 'ti'), $v):'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/szint.svg" alt="<?=__('Szintek száma', 'ti')?>"></div>
                   <?=__('Szintek száma', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_level_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/payment.svg" alt="<?=__('Megbízás típusa', 'ti')?>"></div>
                   <?=__('Megbízás típusa', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->PropertyStatus(true))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/home.svg" alt="<?=__('Ingatlan típusa', 'ti')?>"></div>
                   <?=__('Ingatlan típusa', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->PropertyType(true))?$prop->multivalue_list($v):'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Állapot', 'ti')?>"></div>
                   <?=__('Állapot', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->PropertyCondition(true))?$prop->multivalue_list($v):'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e price">
                  <?php if ($prop->isDropOff()): ?>
                    <div class="old-price">
                      <?=$prop->OriginalPrice(true)?>
                    </div>
                  <?php endif; ?>
                  <div class="current-price">
                    <?=$prop->Price(true)?> <span class="type"><?=$prop->PriceType()?></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="contact">
              <div class="title"><?=__('Érdeklődjön kollégánknál', 'ti')?></div>
              <div class="name"><strong><?=$prop->AuthorName()?></strong> <span class="tit"><?=__('Ingatlan referens', 'ti')?></span></div>
              <div class="email"><a href="mailto:<?=$prop->AuthorEmail()?>"><?=$prop->AuthorEmail()?></a></div>
              <div class="phone"><i class="fa fa-phone"></i> <?=$prop->AuthorPhone()?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="data-main-holder">
        <div class="page-holder">
          <div class="data-main">
            <div class="data-main-left">
              <div class="fusion-tabs fusion-tabs-1 classic nav-not-justified horizontal-tabs">
    						<div class="nav">
    							<ul class="nav-tabs">
    								<li class="active"><a class="tab-link" data-toggle="tab" href="#params"><h4 class="fusion-tab-heading"><i class="fa fa-gear"></i> <?=__('Ingatlan adatai', 'ti')?></h4></a></li>
                    <li><a class="tab-link" data-toggle="tab" href="#info"><h4 class="fusion-tab-heading"><i class="fa fa-file"></i> <?=__('Leírás', 'ti')?></h4></a></li>
                    <li><a class="tab-link" data-toggle="tab" href="#alaprajz"><h4 class="fusion-tab-heading"><i class="fa fa-map-o"></i> <?=__('Alaprajz', 'ti')?></h4></a></li>
                    <li><a class="tab-link" data-toggle="tab" href="#video"><h4 class="fusion-tab-heading"><i class="fa fa-video-camera"></i> <?=__('Videó', 'ti')?></h4></a></li>
    							</ul>
    						</div>
    						<div class="tab-content">
    							<div class="tab-pane active in" id="params">
                    Params
    							</div>
                  <div class="tab-pane" id="info">
                    INFO
    							</div>
                  <div class="tab-pane" id="alaprajz">
                    Alaprajz
    							</div>
                  <div class="tab-pane" id="video">
                    Videó
    							</div>
    						</div>
    					</div>
            </div>
            <div class="data-main-right">
              <div class="map-block">
                <div class="head">
                  <i class="fa fa-map-pin"></i> <?=__('Térkép', 'ti')?>
                </div>
                <?
                  $gps = $prop->GPS();
                  $gps_term_id = $regio->term_id;
                  /*ob_start();
                  include(locate_template('/templates/parts/map_place_poi.php'));
                  ob_end_flush();*/
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="history-list">
        <div class="page-holder">
          <? echo do_shortcode('[listing-list view="simple-priced" src="viewed" limit="5"]'); ?>
        </div>
      </div>
    </div>
	</div>
  <script type="text/javascript">
    (function($){
      $('.image-slide').slick({
        <?=(count($images)>5)?'centerMode: true,':''?>
        autoplay: false,
        centerPadding: '60px',
        slidesToShow: 5,
      });

      $('.image-slide .slick-slide').on('click', function(e) {
        //e.stopPropagation();
        var index = $(this).data("slick-index");
        if ($('.image-slide').slick('slickCurrentSlide') !== index) {
          $('.image-slide').slick('slickGoTo', index);
        }

        console.log(index);
      });

      $('.image-slide').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        var cs = $(slick.$slides).get(nextSlide);
        var ci = $(cs).find('img').attr('src');
        //$('#profilimg a').attr('href', ci);
        $('#profilimg img').attr('src', ci);
        console.log(ci);
      });

    })(jQuery);
  </script>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
