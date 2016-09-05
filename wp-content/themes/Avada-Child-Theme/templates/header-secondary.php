<?php
$content_1 = avada_secondary_header_content( 'header_left_content' );
$content_2 = avada_secondary_header_content( 'header_right_content' );
?>
<div class="fusion-secondary-header">
    <div class="custom-secondary-header-holder">
        <div class="top-sec-menu"><?php echo $content_1; ?></div>
        <div class="sec-header-wrap">
            <div class="item csh-notify">
                <ul id="notification">
                    <li class="visited-adv fusion-tooltip" data-toggle="tooltip" data-placement="bottom" data-original-title="Megtekintett ingatlan hirdetések"><a href="/"><span class="num">99</span></a></li>
                    <li class="new-adv fusion-tooltip" data-toggle="tooltip" data-placement="bottom" data-original-title="Új ingatlan hirdetések"><a href="/"><span class="num">150</span></a></li>
                </ul>
            </div>
            <div class="item csh-socialling"><?php echo $content_2; ?></div>
        </div>
    </div>
</div>
