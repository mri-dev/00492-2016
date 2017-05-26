<?php
global $wp_query;
$xs = explode("-",$wp_query->query_vars['urlstring']);
$ingatlan_id = end($xs);

$properties = new Properties(array(
  'id' => $ingatlan_id,
  'post_status' => array('publish'),
));
$property = $properties->getList();
$prop = $property[0];

?>
<div class="page-title-shares">
  <div class="socials">
    <div class="fb">
      <a href="javascript:void(0);" onclick="window.open('https://www.facebook.com/dialog/share?app_id=<?=FB_APP_ID?>&amp;display=popup&amp;href=<?=get_option('siteurl', '').$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>&amp;redirect_uri=<?=get_option('siteurl', '')?>/close.html','','width=800, height=240')"><i class="fa fa-facebook"></i></a>
    </div>
    <div class="gplus">
      <a  href="https://plus.google.com/share?url=<?=get_option('siteurl', '').$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>" onclick="javascript:window.open('https://plus.google.com/share?url=<?=get_option('siteurl', '').$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>', '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" ><i class="fa fa-google-plus"></i></a>
    </div>
  </div>
  <div class="extras">
    <div class="default">
      <a href="mailto:?subject=<?php echo $prop->Title(); ?> ingatlanhirdetés, itt: <?php echo $prop->RegionName(); ?>&body=Találtam egy nagyon kedvező ingatlant. Tekintsd meg Te is: <?php echo $prop->Title(); ?> (<?php echo $prop->RegionName(); ?>) - <?php echo $prop->URL(); ?>"><i class="fa fa-envelope-o"></i></a>
    </div>
    <div class="default">
      <a href="javascript:void(0);" onclick="PrintElem(<?=$ingatlan_id?>);"><i class="fa fa-print"></i></a>
    </div>
  </div>
</div>
<script type="text/javascript">
  function PrintElem(tid) {
  var url = '/ingatlan/print/'+tid;

  var printw = window.open(url, 'PRINT', 'height=820,width=900');
  printw.focus();

  setTimeout(function() {
      //printw.print();
      //printw.close();
  }, 1000);

  return true;
}

</script>
