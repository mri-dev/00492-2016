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
  $eladva = $prop->isSold();

?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_INGATLAN?>-page-view">
      <div class="page-holder">
        <div class="data-top">
          <div class="data-top-left <?=($eladva)?'sold':''?>">
            <div class="features">
              <?php $label = $prop->PropertyLabel(); ?>
              <?php if ($label): ?>
                <div class="status status-<?=sanitize_title($label['text'])?>" style="<?=($label['bg'])?'background: '.$label['bg'].' !important;':''?>"><?=$label['text']?></div>
              <?php endif; ?>
              <? if($prop->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'ti')?></div><? endif; ?>
              <? if($prop->isNews()):?><div class="newi"><?=__('új')?></div><? endif; ?>
              <? if($prop->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'ti')?>" /></div><? endif; ?>
            </div>
            <div class="cwrapper">
              <div class="images">
                <div class="profil" id="profilimg">
                  <?php if ($eladva) { ?>
                    <div class="sold" style="background-image:url('<?=IMG?>sold_overlay.png');"></div>
                  <? } ?>
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
                      <div class="i orientation-<?=$img->metadata['orientation']?>">
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
                <span class="addr"><i class="fa fa-map-marker"></i> <?php echo $prop->RegionName(true, 0); ?></span>

                <!-- <strong><?=$prop->multivalue_list($prop->PropertyType(true), true, '/'.SLUG_INGATLAN_LIST.'/?c=#value#')?></strong>-->
              </div>
            </div>
            <div class="properties">
              <div class="list">
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/szoba.svg" alt="<?=__('Szobaszám', 'ti')?>"></div>
                   <?=__('Szobák száma', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_room_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/halo.svg" alt="<?=__('Hálók', 'ti')?>"></div>
                   <?=__('Hálók', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_bedrooms'))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/furdo.svg" alt="<?=__('Fürdők', 'ti')?>"></div>
                   <?=__('Fürdők', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_bathroom_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/garazs.svg" alt="<?=__('Garázs', 'ti')?>"></div>
                   <?=__('Garázs', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_garage'))?$v:'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e e2x smaller toleft">
                  <?php
                    $cond = $prop->PropertyCondition();
                  ?>
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Állapot', 'ti')?>"> <?=__('Állapot', 'ti')?></div>
                 </div><!--
              --><div class="v">
                  <?php $cond_text = ''; foreach ((array)$cond as $co){ $cond_text .= '<a href="/'.SLUG_INGATLAN_LIST.'/?co='.$co->term_id.'">'.$co->name.'</a>, '; } $cond_text = rtrim($cond_text, ', ');  ?>
                  <?php if(empty($cond_text)){ echo '<span class="na">'.__('nincs megadva', 'ti').'</span>';  }else{ echo $cond_text; } ?>
                  </div>
                </div>
                <div class="e nm">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/alapterulet.svg" alt="<?=__('Alapterület', 'ti')?>"></div>
                   <?=__('Alapterület', 'ti')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_property_size'))?sprintf(__('%d nm', 'ti'), $v):'<span class="na">'.__('nincs megadva', 'ti').'</span>'?></div>
                </div>
                <div class="e price">
                  <?php if ($prop->isDropOff()): ?>
                    <div class="old-price">
                      <?=$prop->OriginalPrice(true)?>
                    </div>
                  <?php endif; ?>
                  <div class="current-price">
                    <span class="type"><?=$prop->getValuta()?></span><?=$prop->Price(true)?><span class="pt"><?php echo $prop->PriceType(); ?></span>
                  </div>
                </div><!--
            --></div>
            </div>
            <div class="contact">
              <div class="title"><?=__('Érdeklődjön kollégánknál', 'ti')?></div>
              <div class="name"><strong><?=$prop->AuthorName()?></strong> <span class="tit"><?=__('Ingatlan referens', 'ti')?></span></div>
              <div class="email"><a href="mailto:<?=$prop->AuthorEmail()?>"><?=$prop->AuthorEmail()?></a></div>
              <div class="clearfix"></div>
              <div class="phone"><i class="fa fa-phone"></i> <?=$prop->AuthorPhone()?></div>
              <div class="mail"><a href="mailto:<?=$prop->AuthorEmail()?>"><i class="fa fa-envelope-o"></i> <?=__('Üzenet küldése', 'ti')?></a></div>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>
      </div>
      <?php
      $layout = $prop->Layouts();
      $video = $prop->Videos();
      ?>
      <div class="data-main-holder">
        <div class="page-holder">
          <div class="data-main">
            <div class="data-main-left">
              <div class="fusion-tabs fusion-tabs-1 classic nav-not-justified horizontal-tabs">
    						<div class="nav">
    							<ul class="nav-tabs">
    								<li class="active"><a class="tab-link" data-toggle="tab" href="#params"><h4 class="fusion-tab-heading"><i class="fa fa-gear"></i> <?=__('Ingatlan adatai', 'ti')?></h4></a></li>
                    <li><a class="tab-link" data-toggle="tab" href="#info"><h4 class="fusion-tab-heading"><i class="fa fa-file"></i> <?=__('Leírás', 'ti')?></h4></a></li>
                    <?php if ($layout): ?>
                    <li><a class="tab-link" data-toggle="tab" href="#alaprajz"><h4 class="fusion-tab-heading"><i class="fa fa-map-o"></i> <?=__('Alaprajz', 'ti')?></h4></a></li>
                    <?php endif; ?>
                    <?php if ($video): ?>
                    <li><a class="tab-link" data-toggle="tab" href="#video"><h4 class="fusion-tab-heading"><i class="fa fa-video-camera"></i> <?=__('Videó', 'ti')?></h4></a></li>
                    <?php endif; ?>
    							</ul>
    						</div>
    						<div class="tab-content">
    							<div class="tab-pane active in" id="params">
                    <div class="parameters">
                      <div class="line">
                      <?
                        $params = $prop->Paramteres();
                        $pi = 0;
                        foreach( $params as $param): $pi++; ?>
                        <div class="param">
                          <div class="text">
                            <?=$param['name']?>
                          </div>
                          <div class="value">
                            <?php $v = $param['value']; ?>
                            <?php if (!empty($v)): ?>
                              <?php if ($param['name'] == 'Ingatlan állapota' || $param['name'] == 'Épület állapota kívül'): ?>
                                <?php $star = (int)$v; ?>
                                <?php if (is_numeric($star)): ?>
                                  <?php echo str_repeat('<i class="fa fa-star"></i>', $star); ?>
                                <?php endif; ?>
                              <?php else: ?>
                                <?php echo $v; ?>
                                <?php if ($param['after']): ?>
                                  <?php echo $param['after']; ?>
                                <?php endif; ?>
                              <?php endif; ?>
                            <?php else: ?>
                              <div class="na">
                                <?php echo __('N/A', 'ti'); ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                        <?php if ($pi%2 == 0): ?>
                          </div>
                          <div class="line">
                        <?php endif; ?>
                      <? endforeach;?>
                      </div>
                    </div>
    							</div>
                  <div class="tab-pane" id="info">
                    <div class="pad">
                      <? echo $prop->Description(); ?>
                    </div>
    							</div>
                  <div class="tab-pane" id="alaprajz">
                    <div class="pad">
                    <?php echo $prop->Layouts(); ?>
                    </div>
    							</div>
                  <div class="tab-pane" id="video">
                    <div class="pad">
                      <?
                        $video = \YoutubeHelper::ember( $video );

                        echo $video;
                      ?>
                    </div>
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
                  $regio = end($regions);
                  $gps_term_id = $regio->term_id;

                  ob_start();
                  include(locate_template('/templates/parts/map_place_poi.php'));
                  ob_end_flush();
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="recomended-list">
        <div class="page-holder">
          <?php $nearb = end($prop->RegionName(false, 0, true)); ?>
          <? echo do_shortcode('[listing-list view="standard" src="recomended" limit="4" loc="'.$nearb->term_id.'" city="'.$nearb->name.'" excid="'.$prop->ID().'"]'); ?>
        </div>
      </div>
      <div class="divider"></div>
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
        autoplay: false,
        slidesToShow: 5,
      });

      $('.image-slide .slick-slide').on('click', function(e) {
        //e.stopPropagation();
        var index = $(this).data("slick-index");

        if ($('.image-slide').slick('slickCurrentSlide') !== index) {
          $('.image-slide').slick('slickGoTo', index);
        }

      });

      $('.image-slide').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        var cs = $(slick.$slides).get(nextSlide);
        console.log(cs);
        var ci = $(cs).find('img').attr('src');
        $('#profilimg a').attr('href', ci);
        $('#profilimg img').attr('src', ci);
      });

    })(jQuery);
  </script>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
