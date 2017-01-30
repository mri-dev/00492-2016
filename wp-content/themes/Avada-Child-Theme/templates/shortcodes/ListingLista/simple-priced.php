<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <div class="status status-<?=$item->PropertyStatus()?>"><?=$item->PropertyStatus(true)?></div>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'gh')?>" /></div><? endif; ?>
      </div>
      <div class="image">
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="sec-line">
      <div class="price"><?=$item->getValuta()?><?=$item->Price(true)?><span class="pt"><?php echo $item->PriceType(); ?></span></div>
    </div>
  </div>
</div>
