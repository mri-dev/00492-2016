<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <?php $label = $item->PropertyStatus(); ?>
        <?php if ($label): ?>
          <div class="status status-<?=sanitize_title($label['text'])?>" style="<?=($label['bg'])?'background: '.$label['bg'].' !important;':''?>"><?=$label['text']?></div>
        <?php endif; ?>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isNews()):?><div class="newi"><?=__('új')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'gh')?>" /></div><? endif; ?>
        <? if($imgnum = $item->imageNumbers()):?><div class="photo trans-on"><img src="<?=IMG?>/ico-photo-white.svg" alt="<?=__('Fényképek', 'gh')?>" /> <span class="nm"><?=$imgnum?></span></div><? endif; ?>
      </div>
      <div class="image">
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="line-region">
      <?=$item->RegionName()?>
    </div>
    <div class="line-price">
      <?=$item->Price(true)?><span class="pt"><?php echo $item->PriceType(); ?></span>
    </div>
  </div>
</div>
