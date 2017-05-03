<?php
  // Régiók
  $regions = $properties->getRegions();
?>

<form class="" role="searcher" id="searcher-form" action="/<?=SLUG_INGATLAN_LIST?>/" method="get">
<div class="searcher-header"><!--
--><ul>
    <li>
      <div class="ico">
        <i class="fa fa-search"></i>
      </div>
    </li><!--
 --><li class="title">
      <?=__('Ingatlankeresés', 'ti')?>
    </li><!--
--></ul><!--
--></div>
<div class="searcher-wrapper">
    <div class="form-items">

      <div class="inp inp-city">
        <label for="zone_multiselect_text">Régió / Város</label>
        <div class="tglwatcher-wrapper">
          <input type="text" readonly="readonly" id="zone_multiselect_text" class="form-control tglwatcher" tglwatcher="zone_multiselect" placeholder="<?=__('Összes', 'gh')?>" value="">
        </div>
        <input type="hidden" id="zone_multiselect_ids" name="zona" value="">
        <div class="multi-selector-holder" tglwatcherkey="zone_multiselect" id="zone_multiselect">
          <div class="selector-wrapper">
            <?php
              $lvl = 0;
              $zonak = array();
            ?>
            <?php foreach ($regions as $rid => $r): ?>
              <div class="lvl-<?=$lvl?> zone<?=$r->term_id?> selector-row" data-parent="<?=$r->parent?>">
                <input tglwatcherkey="zone_multiselect" htext="<?=$r->name?>" <?=(in_array($r->term_id, $zonak))?'checked="checked"':''?> class="<? if($r->count != 0): echo ' has-childs'; endif; ?>" type="checkbox" id="zone_<?=$r->term_id?>" value="<?=$r->term_id?>"> <label for="zone_<?=$r->term_id?>"><?=$r->name?> <span class="n">(<?=$r->count?>)</span></label>
              </div>
              <?php
              $children = $r->children;
              while( !empty($children) ){
                $lvl++;
                foreach ($children as $rid => $r) {
                  $children = $r->children;
                  ?>
                  <div class="lvl-<?=$lvl?> zone<?=$r->term_id?> selector-row" data-parent="<?=$r->parent?>">
                    <input tglwatcherkey="zone_multiselect" htext="<?=$r->name?>" <?=(in_array($r->term_id, $zonak))?'checked="checked"':''?> type="checkbox" id="zone_<?=$r->term_id?>" value="<?=$r->term_id?>"> <label for="zone_<?=$r->term_id?>"><?=$r->name?> <span class="n">(<?=$r->count?>)</span></label>
                  </div>
                  <?
                }

              } ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="inp inp-status">
        <label for="status_multiselect_text"><?=__('Státusz', 'gh')?></label>
        <div class="tglwatcher-wrapper">
          <input type="text" readonly="readonly" id="status_multiselect_text" class="form-control tglwatcher" tglwatcher="status_multiselect" placeholder="<?=__('Összes', 'gh')?>" value="">
        </div>
        <input type="hidden" id="status_multiselect_ids" name="st" value="<?=$form['st']?>">
        <div class="multi-selector-holder" tglwatcherkey="status_multiselect" id="status_multiselect">
          <div class="selector-wrapper">
            <?
              $selected = explode(",", $form['st']);
              $status = $properties->getSelectors( 'status' );
            ?>
            <?php if ($status): ?>
              <?php foreach ($status as $k): ?>
              <div class="selector-row">
                <input type="checkbox" <?=(in_array($k->term_id, $selected))?'checked="checked"':''?> tglwatcherkey="status_multiselect" htxt="<?=$k->name?>" id="stat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="stat_<?=$k->term_id?>"><?=$k->name?> <span class="n">(<?=$k->count?>)</span></label>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="inp inp-kategoria">
        <label for="kategoria_multiselect_text"><?=__('Kategória', 'ti')?></label>
        <div class="tglwatcher-wrapper">
          <input type="text" readonly="readonly" id="kategoria_multiselect_text" class="form-control tglwatcher" tglwatcher="kategoria_multiselect" placeholder="<?=__('Összes', 'ti')?>" value="">
        </div>
        <input type="hidden" id="kategoria_multiselect_ids" name="c" value="">
        <div class="multi-selector-holder" tglwatcherkey="kategoria_multiselect" id="kategoria_multiselect">
          <div class="selector-wrapper">
            <? $kategoria = $properties->getSelectors( 'property-types' ); ?>
            <?php if ($kategoria): ?>
              <?php foreach ($kategoria as $k): ?>
              <div class="selector-row">
                <input type="checkbox" tglwatcherkey="kategoria_multiselect" htxt="<?=$k->name?>" id="kat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="kat_<?=$k->term_id?>"><?=$k->name?> <span class="n">(<?=$k->count?>)</span></label>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="inp inp-rooms">
        <label for="searcher_rooms"><?=__('Szobák száma', 'ti')?></label>
        <div class="select-wrapper">
          <select class="form-control" name="r" id="searcher_rooms">
            <option value="0" selected="selected"><?=__('Összes', 'ti')?></option>
            <?php $c = 0; while ( $c < 10 ): $c++; ?>
            <option value="<?=$c?>"><?=sprintf(_n('%d+ szoba', '%d+ szoba', $c, 'ti'), $c)?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="inp inp-alapterulet">
        <label for="searcher_property_size"><?=__('Min. alapterület', 'ti')?></label>
        <input type="number" class="form-control" id="searcher_property_size" name="ps" min="0" placeholder="<?=__('nm', 'ti')?>" step="10" value="">
      </div>
      <div class="inp inp-price-min">
        <label for="searcher_price_min"><?=__('Minimum ár (€)', 'ti')?></label>
        <input type="text" class="form-control pricebind" id="searcher_price_min" name="pa" placeholder="<?=__('€', 'ti')?>" value="">
      </div>
      <div class="inp inp-price-max">
        <label for="searcher_price_max"><?=__('Maximum ár (€)', 'ti')?></label>
        <input type="text" class="form-control pricebind" id="searcher_price_max" name="pb" placeholder="<?=__('€', 'ti')?>" value="">
      </div>
      <div class="inp inp-submit">
        <button type="submit"><i class="fa fa-search"></i> <?=__('Keresés', 'ti')?></button>
      </div>
    </div>
</div>
<div class="searcher-footer">
  OPTIONS
</div>
</form>
<script type="text/javascript">
  (function($){
    $(window).click(function() {
      if (!$(event.target).closest('.toggler-opener').length) {
        $('.toggler-opener').removeClass('opened toggler-opener');
        $('.tglwatcher.toggled').removeClass('toggled');
      }
    });

    $('.pricebind').bind("keyup change", function() {

    });

    $('.tglwatcher').click(function(event){
      event.stopPropagation();
      event.preventDefault();
      var e = $(this);
      var target_id = e.attr('tglwatcher');
      var opened = e.hasClass('toggled');

      if(opened) {
        e.removeClass('toggled');
        $('#'+target_id).removeClass('opened toggler-opener');
      } else {
        e.addClass('toggled');
        $('#'+target_id).addClass('opened toggler-opener');
      }
    });

    $('.multi-selector-holder input[type=checkbox]').change(function()
    {
      var e = $(this);
      var checkin = $(this).is(':checked');
      var tkey = e.attr('tglwatcherkey');
      var selected = collect_checkbox(tkey, false);
      $('#'+tkey+'_ids').val(selected);
    });

  })(jQuery);

  function collect_checkbox(rkey, loader)
  {
    var arr = [];
    var str = [];
    var seln = 0;

    jQuery('#'+rkey+' input[type=checkbox]').each(function(e,i)
    {
      if(jQuery(this).is(':checked') && !jQuery(this).is(':disabled')){
        seln++;
        arr.push(jQuery(this).val());
        str.push(jQuery(this).attr('htxt'));
      }

      if(loader) {
        var e = jQuery(this);
        var has_child = jQuery(this).hasClass('has-childs');
        var checkin = jQuery(this).is(':checked');
        var lvl = e.data('lvl');
        var parent = e.data('parentid');

        var cnt_child = jQuery('#'+rkey+' .childof'+parent+' input[type=checkbox]:checked').length;

        if(cnt_child == 0) {
          jQuery('#'+rkey+' .zone'+parent+' input[type=checkbox]').prop('disabled', false);
        } else {
          jQuery('#'+rkey+' .childof'+parent).addClass('show');
          jQuery('#'+rkey+' .zone'+parent+' input[type=checkbox]').prop('checked', true).prop('disabled', true);
        }
      }
    });

    console.log(str);

    if(seln <= 3 ){
      jQuery('#'+rkey+'_text').val(str.join(", "));
    } else {
      jQuery('#'+rkey+'_text').val(seln + " <?=__('kiválasztva', 'ti')?>");
    }

    return arr.join(",");
  }
</script>
