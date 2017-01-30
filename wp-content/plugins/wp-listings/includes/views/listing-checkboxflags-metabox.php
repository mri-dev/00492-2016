
<?php
global $post;

echo '<div style="width: 45%; float: left">';
  $pattern = '<p><input %s id="cfi%s" type="checkbox" name="wp_listings[checkbox][%s]" value="1" /> <label for="cfi%s">%s</label></p>';

  foreach ( (array) $this->property_details['checkbox'] as $label => $key ) {
    echo '<input type="hidden" name="wp_listings[checkboxes][]" value="'.$key.'">';
    $checked = '';
    $val = esc_attr( get_post_meta( $post->ID, $key, true ) );

    if($val != '' && $val == '1') {
      $checked = 'checked="checked"';
    } else {
      $checked = '';
    }
    printf( $pattern, $checked, $key, $key, $key, esc_html( $label ) );
  }
echo '</div>';
echo '<div style="width: 45%; float: left">';

foreach ( (array) $this->property_details['flags'] as $label => $key ) {
  if($key != '_listing_flag_highlight') continue;
  echo '<input type="hidden" name="wp_listings[checkboxes][]" value="'.$key.'">';
  $checked = '';
  $val = esc_attr( get_post_meta( $post->ID, $key, true ) );

  if($val != '' && $val == '1') {
    $checked = 'checked="checked"';
  } else {
    $checked = '';
  }
  printf( $pattern, $checked, $key, $key, $key, esc_html( $label ) );
}

echo '</div>';
echo '<br style="clear: both;" />';

?>
