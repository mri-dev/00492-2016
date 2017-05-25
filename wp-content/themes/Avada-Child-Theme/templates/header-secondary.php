<?php
global $notify;
$content_1 = avada_secondary_header_content( 'header_left_content' );
$content_2 = avada_secondary_header_content( 'header_right_content' );
?>
<div class="fusion-secondary-header">
    <div class="custom-secondary-header-holder">
        <div class="top-sec-menu"><?php echo $content_1; ?></div>
        <div class="sec-header-wrap">
            <div class="item csh-notify">
                <ul id="notification">
                    <li class="show-on-map only-in-mobile" data-toggle="tooltip" data-placement="bottom" data-original-title="Ingatlanok listája"><a href="javascript:void(0);" id="tgl-map-results" data-state="closed"><i class="fa fa-list"></i><span class="num">0</span></a></li>
                  <? $watched = $notify->propertyWatched(); ?>
                    <li class="visited-adv fusion-tooltip" data-toggle="tooltip" data-placement="bottom" data-original-title="Megtekintett ingatlan hirdetések"><a href="/megtekintett"><span class="num <?=($watched > 0)?'has':''?>"><?php echo $watched; ?></span></a></li>
                    <? $unwatched = $notify->propertyUnwatched(); ?>
                    <li class="new-adv fusion-tooltip" data-toggle="tooltip" data-placement="bottom" data-original-title="Új ingatlan hirdetések"><a href="/news"><span class="num <?=($unwatched > 0)?'has':''?>"><?php echo $unwatched; ?></span></a></li>
                </ul>
            </div>
            <div class="item csh-socialling"><?php echo $content_2; ?></div>
        </div>
    </div>
</div>
