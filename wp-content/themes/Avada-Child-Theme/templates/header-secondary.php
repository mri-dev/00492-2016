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
                    <li class="visited-adv"><a href="/"></a><span class="num">0</span></li>
                    <li class="new-adv"><a href="/"><i class="fa fa-home"></i></a><span class="num">0</span></li>
                </ul>
            </div>
            <div class="item csh-socialling"><?php echo $content_2; ?></div>
        </div>
    </div>
</div>
