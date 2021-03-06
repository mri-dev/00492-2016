<?php
class NotifyManager extends PropertyFactory
{
  private $notifications = array();
  private $total_notify = 0;
  public function __construct()
  {

    // Archive requests
    $this->notifications['property']['archive_request'] = $this->checkArchiveRequest();
    $this->total_notify += $this->notifications['property']['archive_request'];

    return $this;
  }

  public function checkArchiveRequest()
  {
    global $wpdb;

    $qry = "SELECT count(arc.ID) FROM ".self::PROPERTY_ARCHIVE_DB." as arc ";

    if (isset($join)) {
      $qry .= $join;
    }

    $qry .= " WHERE accept_date IS NULL ";

    if (isset($where)) {
      $qry .= $where;
    }

    return $wpdb->get_var( $qry );
  }

  public function propertyArchiveRequests()
  {
    return $this->notifications['property']['archive_request'];
  }

  public function propertyWatched()
  {
    global $wpdb;
    $n = 0;

    $arg = array(
      'post_type' => 'listing',
      'posts_per_page' => 1,
      'post_status' => array('publish')
    );


    $ucid = ucid();

    // Visited
    $qry = "SELECT pid FROM `".self::LOG_VIEW_DB."` as t WHERE t.`ucid` = '".$ucid."' GROUP BY t.pid ORDER BY t.visited DESC;";

    $idsq = $wpdb->get_results($qry, ARRAY_A );
    $ids = array();
    foreach ($idsq as $sid) {
      if (!in_array($sid['pid'], $ids)) {
        $ids[] = $sid['pid'];
      }
    }

    if(empty($ids)) {
      $ids = array(0);
    }
    $arg['post__in'] = $ids;

    $qry = new WP_Query($arg);

    $n = $qry->found_posts;

    return $n;
  }

  public function propertyUnwatched()
  {
    global $wpdb;
    $n = 0;

    $arg = array(
      'post_type' => 'listing',
      'posts_per_page' => 1,
      'post_status' => array('publish')
    );

    $ucid = ucid();

    // Visited
    $qry = "SELECT pid FROM `".self::LOG_VIEW_DB."` as t WHERE t.`ucid` = '".$ucid."' ORDER BY t.visited DESC;";

    $idsq = $wpdb->get_results($qry, ARRAY_A );
    $ids = array();
    foreach ($idsq as $sid) {
      if (!in_array($sid['pid'], $ids)) {
        $ids[] = $sid['pid'];
      }
    }

    if ($ids) {
      $arg['post__not_in'] = $ids;
    }

    // Time click
    $t_qry = "SELECT wtime FROM `".self::LOG_WATCHTIME_DB."` as t WHERE t.`ucid` = '".$ucid."' ORDER BY t.wtime DESC LIMIT 0,1;";
    $watchtimestmp = $wpdb->get_var($t_qry);

    if ($watchtimestmp) {
      $arg['date_query']['after'] = $watchtimestmp;
    }

    $meta_qry = array();

    // PREMIUM
    if (defined('SHOW_PREMIUM_ONLY') && SHOW_PREMIUM_ONLY === true) {
      $meta_qry[] = array(
        'key' => '_listing_premium',
        'value' => '1'
      );
    } else {
      /* */
      $meta_qry[] = array(
        'relation' => 'OR',
        array(
          'key' => '_listing_premium_only',
          'compare' => 'NOT EXISTS'
        ),
        array(
          'key' => '_listing_premium_only',
          'value' => '1',
          'compare' => '!='
        )
      );
      /* */
    }

    if (!empty($meta_qry)) {
      $arg['meta_query'] = $meta_qry;
    }

    $qry = new WP_Query($arg);

    $n = $qry->found_posts;

    return $n;
  }
}
$notify = new NotifyManager();
?>
