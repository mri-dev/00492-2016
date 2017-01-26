<?php
  global $is_ingatlan_page, $is_ingatlan_list;

  if ($is_ingatlan_page) {
      $secondary_content = ingatlan_social_share_titlebar();
  } else if ($is_ingatlan_list) {
      $secondary_content = null;
  }
?>
<div class="fusion-page-title-bar fusion-page-title-bar-<?php echo $content_type; ?> fusion-page-title-bar-<?php echo $alignment; ?>">
    <div class="fusion-page-title-row">
        <div class="fusion-page-title-wrapper">
            <div class="fusion-page-title-captions">
                <?php if ($is_ingatlan_page): ?>
                  <a href="<?php echo get_option('siteurl', true); ?>" class="back-to-home"> <i class="fa fa-caret-left"></i> Vissza a találati listára</a>
                <?php else: ?>
                  <a href="<?php echo get_option('siteurl', true); ?>" class="back-to-home"> <i class="fa fa-caret-left"></i> Vissza a főoldalra</a>
                <?php endif; ?>
                <?php if ( $title ) : ?>
                    <?php // Add entry-title for rich snippets ?>
                    <?php $entry_title_class = ( Avada()->settings->get( 'disable_date_rich_snippet_pages' ) ) ? ' class="entry-title"' : ''; ?>
                    <h1<?php echo $entry_title_class; ?>><?php echo $title; ?></h1>

                    <?php if ( $subtitle ) : ?>
                        <h3><?php echo $subtitle; ?></h3>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( 'center' == $alignment ) : // Render secondary content on center layout ?>
                    <?php if ( 'none' != fusion_get_option( 'page_title_bar_bs', 'page_title_breadcrumbs_search_bar', $post_id ) ) : ?>
                        <div class="fusion-page-title-secondary"><?php echo $secondary_content; ?></div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>

            <?php if ( 'center' != $alignment ) : // Render secondary content on left/right layout ?>
                <?php if ( 'none' != fusion_get_option( 'page_title_bar_bs', 'page_title_breadcrumbs_search_bar', $post_id ) ) : ?>
                    <div class="fusion-page-title-secondary"><?php echo $secondary_content; ?></div>
                <?php endif; ?>
            <?php endif;?>

        </div>
    </div>
</div>
