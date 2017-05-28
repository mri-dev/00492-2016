<div class="header-search">
    <form class="searchform seach-form" method="get" action="/<?php echo SLUG_INGATLAN_LIST; ?>/">
        <?php foreach ($_GET as $gk => $g): if($gk == 'src') continue; ?>
          <input type="hidden" name="<?=$gk?>" value="<?=$g?>">
        <?php endforeach; ?>
        <input id="searchform" type="text" value="<?php echo $_GET['src']; ?>" name="src" class="s" placeholder="">
        <button type="submit"><i class="fa fa-search"></i></button>
    </form>
</div>
