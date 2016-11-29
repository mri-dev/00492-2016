<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <div class="status status-<?=$item->PropertyStatus()?>"><?=$item->PropertyStatus(true)?></div>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isNews()):?><div class="newi"><?=__('új')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'gh')?>" /></div><? endif; ?>
      </div>
      <div class="image">
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="prim-line">
      <div class="pos">
        <div class="region"><a href="#"><i class="fa fa-pin"></i> <?=$item->RegionName()?></a></div>
      </div>
    </div>
    <div class="important-options">
      <div class="opt">
        <div class="head"><?=__('Telek alapterület', 'gh')?></div>
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/telek-alapterulet.svg"?>" alt="<?=__('Telek alapterület', 'gh')?>" /></div>
          <?=sprintf(__('%s nm', 'gh'), $item->getMetaValue('_listing_lot_size'))?>
        </div>
      </div>
      <div class="opt">
        <div class="head"><?=__('Alapterület', 'gh')?></div>
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/alapterulet.svg"?>" alt="<?=__('Alapterület', 'gh')?>" /></div>
          <?=sprintf(__('%s nm', 'gh'), $item->getMetaValue('_listing_property_size'))?>
        </div>
      </div>
      <div class="opt">
        <div class="head"><?=__('Szobák száma', 'gh')?></div>
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/szoba.svg"?>" alt="<?=__('Szobák száma', 'gh')?>" /></div>
          <?=sprintf(_n('%s szoba', '%s szoba', $item->getMetaValue('_listing_room_numbers'), 'gh'), $item->getMetaValue('_listing_room_numbers'))?>
        </div>
      </div>
      <div class="opt">
        <div class="head"><?=__('Szintek száma', 'gh')?></div>
        <div class="d">
          <div class="ico"><img src="<?=IMG."/ico/szint.svg"?>" alt="<?=__('Szintek száma', 'gh')?>" /></div>
          <?=sprintf(_n('%s szint', '%s szint', $item->getMetaValue('_listing_level_numbers'), 'gh'), $item->getMetaValue('_listing_level_numbers'))?>
        </div>
      </div>
    </div>
    <div class="sec-line">
      <div class="price"><?=$item->Price(true)?></div>
    </div>
  </div>
</div>
