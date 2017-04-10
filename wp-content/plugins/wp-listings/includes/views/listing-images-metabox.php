<?php
  global $post;
  $images = array();

  $tempimages = get_attached_media('image' ,$post->ID);
  $slide_img_id = (int)get_post_meta($post->ID, '_listing_slide_img_id', true);

  foreach ((array)$tempimages as $aid => $img) {
    if($slide_img_id != 0 && $slide_img_id == $aid) continue;
    $images[$aid] = $img;
  }
  unset($temimages);
?>
<label for="property_images"><?=__('Új képek feltöltése', 'ti')?></label><br>
<input type="file" multiple="multiple" name="property_images[]" id="property_images" value="" class="form-control">
<br><br>
<div class="image-set">
  <?php foreach ($images as $aid => $img):
    $selected = ($aid == get_post_thumbnail_id($post->ID)) ? true : false;
  ?>
  <div class="image">
    <div class="iwrapper">
      <a href="<?=$img->guid?>"><img src="<?=$img->guid?>" alt=""></a>
      <? if( !$selected ): ?>
      <div class="delete_selector">
        <input type="checkbox" id="delete_img_<?=$aid?>" name="wp_listings[extra][deleting_imgs][<?=$aid?>]" value="1"><label for="delete_img_<?=$aid?>"><?=__('töröl', 'gh')?></label>
      </div>
      <? endif; ?>
      <div class="profil_selector">
        <input type="radio" id="profil_img_<?=$aid?>" <?=($selected)?'checked="checked"':''?> name="wp_listings[extra][feature_img_id]" value="<?=$aid?>"><label for="profil_img_<?=$aid?>"><?=__('főkép', 'gh')?></label>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php
  $slide_img_id = (int)get_post_meta($post->ID, '_listing_slide_img_id', true);
?>
<h3><?php echo __('Ingatlan slide képe', 'ti'); ?></h3>
<input type="file" name="slide_img" id="slide_img" value="" class="form-control">
<div class="slide-img">
  <?php if ($slide_img_id == 0): ?>
    <?php echo __('Nincs slide kép feltöltve. A profilkép az alapértelmezett.','ti'); ?>
  <?php else: ?>
    <?php $slide = wp_get_attachment_url($slide_img_id); ?>
    <img src="<?php echo $slide; ?>" alt="">
  <?php endif; ?>
</div>

<style>
  .slide-img img {
    max-width: 100%;
  }
  .image-set{
    position: relative;
    display: flex;
    flex-wrap: wrap;
    margin: 0 0;
  }

  .image-set .image {
    flex-basis: 24%;
    padding: 2px;
    height: 120px;
    max-height: 120px;
  }
  .image-set .image img {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 120%;
    max-width: 120%;
    transform: translate(-50%, -50%);
  }
  .image-set .image .iwrapper {
    height: 100%;
     max-height: 100%;
     overflow: hidden;
     position: relative;
  }
  .image-set .image .iwrapper .profil_selector {
    position: absolute;
    right: 0;
    bottom: 0;
    color: white;
  }
  .image-set .image .iwrapper .profil_selector input[type=radio] {
    display: none;
  }
  .image-set .image .iwrapper .profil_selector input[type=radio] + label {
    cursor: pointer;
    background: #65686b;
    color: white;
    font-size: 0.95em;
    line-height: 24px;
    margin: 0;
    padding: 4px 10px 4px 23px;
    -webkit-border-radius: 8px 0 0 0;
    -moz-border-radius: 8px 0 0 0;
    border-radius: 8px 0 0 0;
  }
  .image-set .image .iwrapper .profil_selector input[type=radio] + label:before {
    content: "\f1db";
    font-family: 'FontAwesome';
    color: white;
    position: absolute;
    left: 8px;
  }
  .image-set .image .iwrapper .profil_selector input[type=radio]:checked + label {
    background: #2a9a53;
  }
  .image-set .image .iwrapper .profil_selector input[type=radio]:checked + label:before {
    content: "\f05d";
  }
  .image-set .image .iwrapper .delete_selector {
    position: absolute;
    left: 0;
    bottom: 0;
    color: white;
  }
  .image-set .image .iwrapper .delete_selector input[type=checkbox] {
    display: none !important;
  }
  .image-set .image .iwrapper .delete_selector input[type=checkbox] + label {
    cursor: pointer;
    background: #a95153;
    color: white;
    line-height: 24px;
    font-size: 0.95em;
    margin: 0;
    padding: 4px 23px 4px 10px;
    -webkit-border-radius: 0 8px 0 0;
    -moz-border-radius: 0 8px 0 0;
    border-radius: 0 8px 0 0;
  }
  .image-set .image .iwrapper .delete_selector input[type=checkbox] + label:before {
    display: none;
  }
  .image-set .image .iwrapper .delete_selector input[type=checkbox] + label:after {
    content: "\f096";
    font-family: 'FontAwesome';
    color: white;
    position: absolute;
    right: 8px;
    margin-top: 2px;
  }
  .image-set .image .iwrapper .delete_selector input[type=checkbox]:checked + label {
    background: #e31f24;
  }
  .image-set .image .iwrapper .delete_selector input[type=checkbox]:checked + label:after {
    content: "\f046";
  }
</style>
