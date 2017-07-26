<div class="prop-item <?=($item->isSold())?'sold':''?>">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <?php $label = $item->PropertyLabel(); ?>
        <?php if ($label): ?>
          <div class="status status-<?=sanitize_title($label['text'])?>" style="<?=($label['bg'])?'background: '.$label['bg'].' !important;':''?>"><?=$label['text']?></div>
        <?php endif; ?>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'gh')?>" /></div><? endif; ?>
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
    <div class="sec-line">
      <div class="price"><?=$item->getValuta()?><?=$item->Price(true)?><span class="pt"><?php echo $item->PriceType(); ?></span></div>
    </div>
  </div>
</div>
