<div class="pslider i<?=$i?>" >
  <div class="image" style="background-image: url('<?=$item->SliderImage()?>');"></div>
 <div class="inside-wrapper">
   <div class="info-wrapper">
     <div class="infobox">
       <div class="features">
         <?php $label = $item->PropertyStatus(); ?>
         <?php $def_o = '&mdash;'; ?>
         <?php if ($label): ?>
           <div class="status status-<?=sanitize_title($label['text'])?>" style="<?=($label['bg'])?'background: '.$label['bg'].' !important;':''?>"><?=$label['text']?></div>
         <?php endif; ?>
         <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'ti')?></div><? endif; ?>
         <? if($item->isNews() || true):?><div class="newi"><?=__('új')?></div><? endif; ?>
         <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'ti')?>" /></div><? endif; ?>
       </div>
       <div class="region"><img src="<?=IMG?>/ico/pinner_white.svg" alt=""> <?=$item->RegionName()?></div>
       <div class="important-options">
         <?php $o = $item->getMetaValue('_listing_property_size'); ?>
         <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
         <div class="opt">
           <div class="d">
             <div class="ico"><img src="<?=IMG."/ico/alapterulet_white.svg"?>" alt="<?=__('nm', 'ti')?>" /></div>
             <div class="t"><?=__('nm', 'ti')?></div>
             <div class="v"><?=$o?></div>
           </div>
         </div>
         <?php $o = $item->getMetaValue('_listing_bedrooms'); ?>
         <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
         <div class="opt">
           <div class="d">
             <div class="ico"><img src="<?=IMG."/ico/halo_white.svg"?>" alt="<?=__('Háló', 'ti')?>" /></div>
             <div class="t"><?=__('Háló', 'ti')?></div>
             <div class="v c"><?=$o?></div>
           </div>
         </div>
         <?php $o = $item->getMetaValue('_listing_level_numbers'); ?>
         <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
         <div class="opt">
           <div class="d">
             <div class="ico"><img src="<?=IMG."/ico/szint_white.svg"?>" alt="<?=__('Emelet', 'ti')?>" /></div>
             <div class="t"><?=__('Emelet', 'ti')?></div>
             <div class="v c"><?=$o?></div>
           </div>
         </div>
         <?php $o = $item->getMetaValue('_listing_bathroom_numbers'); ?>
         <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
         <div class="opt">
           <div class="d">
             <div class="ico"><img src="<?=IMG."/ico/furdo_white.svg"?>" alt="<?=__('Fürdő', 'ti')?>" /></div>
             <div class="t"><?=__('Fürdő', 'ti')?></div>
             <div class="v c"><?=$o?></div>
           </div>
         </div>
         <?php $o = $item->getMetaValue('_listing_room_numbers'); ?>
         <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
         <div class="opt">
           <div class="d">
             <div class="ico"><img src="<?=IMG."/ico/szoba_white.svg"?>" alt="<?=__('Szobák', 'ti')?>" /></div>
             <div class="t"><?=__('Szobák', 'ti')?></div>
             <div class="v c"><?=$o?></div>
           </div>
         </div>
         <?php $o = $item->getMetaValue('_listing_garage'); ?>
         <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
         <div class="opt">
           <div class="d">
             <div class="ico"><img src="<?=IMG."/ico/garazs_white.svg"?>" alt="<?=__('Garázs', 'ti')?>" /></div>
             <div class="t"><?=__('Garázs', 'ti')?></div>
             <div class="v c"><?=$o?></div>
           </div>
         </div>
       </div>
       <div class="prices">
         <a class="url" href="<?=$item->URL()?>"><i class="fa fa-arrow-circle-right"></i> </a>
         <div class="price">
          <?php if ($item->isDropOff()): ?>
            <span class="oldar"><?=$item->OriginalPrice(true)?></span>
          <?php endif; ?>
           <?=$item->getValuta()?><?=$item->Price(true)?><span class="pt"><?php echo $item->PriceType(); ?></span></div>
       </div>
     </div>
   </div>
 </div>
</div>
