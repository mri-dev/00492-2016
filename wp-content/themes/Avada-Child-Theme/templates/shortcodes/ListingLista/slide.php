<div class="prop-item <?=($item->isSold())?'sold':''?>">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <?php $label = $item->PropertyLabel(); ?>
        <?php if ($label): ?>
          <div class="status status-<?=sanitize_title($label['text'])?>" style="<?=($label['bg'])?'background: '.$label['bg'].' !important;':''?>"><?=$label['text']?></div>
        <?php endif; ?>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'ti')?></div><? endif; ?>
        <? if($item->isNews()):?><div class="newi"><?=__('új')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'ti')?>" /></div><? endif; ?>
      </div>
      <div class="image">
        <?php if ($item->isSold()) { ?>
          <div class="sold" style="background-image:url('<?=IMG?>sold_overlay.png');"></div>
        <? } ?>
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="prim-line">
      <div class="pos">
        <div class="region"><a href="#"><img src="<?=IMG?>/ico/pinner.svg" alt=""> <?=$item->RegionName(true, 0)?></a></div>
      </div>
    </div>
    <?php $def_o = '&mdash;'; ?>
    <div class="important-options">
      <?php $o = $item->getMetaValue('_listing_property_size'); ?>
      <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
      <div class="opt">
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/alapterulet.svg"?>" alt="<?=__('nm', 'ti')?>" /></div>
          <div class="t"><?=__('nm', 'ti')?></div>
          <div class="v"><?=$o?></div>
        </div>
      </div>
      <?php $o = $item->getMetaValue('_listing_room_numbers'); ?>
      <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
      <div class="opt">
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/szoba.svg"?>" alt="<?=__('Szobák', 'ti')?>" /></div>
          <div class="t"><?=__('Szobák', 'ti')?></div>
          <div class="v c"><?=$o?></div>
        </div>
      </div>
      <?php $o = $item->getMetaValue('_listing_bathroom_numbers'); ?>
      <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
      <div class="opt">
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/furdo.svg"?>" alt="<?=__('Fürdő', 'ti')?>" /></div>
          <div class="t"><?=__('Fürdő', 'ti')?></div>
          <div class="v c"><?=$o?></div>
        </div>
      </div>
      <?php $o = $item->getMetaValue('_listing_terrace'); ?>
      <?php $o = ($o && !empty($o)) ? $o : $def_o; ?>
      <div class="opt">
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/terasz.svg"?>" alt="<?=__('Terasz', 'ti')?>" /></div>
          <div class="t"><?=__('Terasz', 'ti')?></div>
          <div class="v c"><?=$o?></div>
        </div>
      </div>
    </div>
    <div class="sec-line">
      <div class="price"><?=$item->getValuta()?><?=$item->Price(true)?><span class="pt"><?php echo $item->PriceType(); ?></span></div>
    </div>
  </div>
</div>
