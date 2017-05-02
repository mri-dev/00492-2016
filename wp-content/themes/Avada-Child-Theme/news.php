<?php
  get_header();

?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_WATCHED?>-page-view">
	</div>
  <script type="text/javascript">
  </script>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
