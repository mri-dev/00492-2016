<?php
  $ingatlan_id = (int)$wp_query->query_vars['urlstring'];

  $properties = new Properties(array(
    'id' => $ingatlan_id,
    'post_status' => array('publish'),
  ));

  $property = $properties->getList();
  $prop = $property[0];
  if (!$prop || $prop->StatusKey() != 'publish') {
    wp_redirect('/');
  }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<link href="https://fonts.googleapis.com/css?family=Didact+Gothic" rel="stylesheet">
<style media="all">
  body,html {
    height: 100%;
    width: 100%;
    padding: 0;
    font-size: 15px;
    margin: 0;
    font-family: 'Didact Gothic', sans-serif;
  }
  *{
    box-sizing: border-box;
  }
  header img,
  header .title {
    float: left;
  }
  header .title{
    padding-left: 30px;
  }
  header{
    margin-bottom: 50px;
    display: block;
  }
  header img {
    height: 80px;
  }
  header h1{
    margin: 0 0 8px 0;
    text-transform: uppercase;
    font-weight: bold;
    color: #008dd2;
  }
  header h2 {
    margin: 0;
  }
  .clr {
    clear:both;
  }

  .row {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    margin: 0 -15px;
  }

  img {
    max-width: 100%;
  }

  .row > .col {
    padding: 0 15px;
  }

  .col-img{
    flex-basis: 40%;
  }

  .col-desc{
    flex-basis: 60%;
  }
  .params table {
    width: 100%;
    border-collapse: collapse;
  }

  .params table th, .params table td {
    text-align: left;
    padding: 5px;
    width: 25%;
    border: 1px solid #f1f1f1;
    font-size: 12px;
    vertical-align: middle;
  }
  .params table th{
    background-color: #f9f9f9;
  }

  h3 {
    font-weight: bold;
    text-transform: uppercase;
    color: black;
    margin: 0 0 20px 0;
    font-size: 20px;
    background: #eaeaea;
    padding: 10px;
    line-height: 1;
  }

  .current-price{
    text-align: center;
    padding: 20px;
    font-size: 24px;
    background: #47bfd6 ;
    color: white;
    font-weight: bold;
  }

  .contact{
    text-align: center;
  }
  .contact h4{
    font-weight: bold;
    margin: 10px 0 10px 0;
    font-size: 12px;
  }

  .contact .name{
    text-transform: uppercase;
    color: black;
    font-size: 16px;
    font-weight: bold;
  }

  .contact .telefon,
  .contact .email{
    font-size: 14px;
  }

  .contact .telefon{
    font-size: 18px;
  }

  .contact .id{
    font-size: 12px;
    font-style: italic;
  }

  .excerpt{
    background: #f4f4f4;
    border: 4px solid #eaeaea;
    padding: 15px;
    margin: 0 0 30px 0;
  }

  .divider{
    margin: 15px 0;
    height: 5px;
    display: block;
  }

  .page-break{
    page-break-after: always;
  }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript">
  (function($){
      setTimeout(function() {
          window.print();
          window.close();
      }, 300);
  })(jQuery);
</script>
</head>
<body>
  <header>
    <img src="<?php echo get_option('siteurl', '/'); ?>/wp-content/uploads/2016/11/viasale-travel-logo-128x65.png" alt="<?php echo get_option('blogname', ''); ?>">
    <div class="title">
      <h1><?php echo $prop->Title(); ?></h1>
      <?php $region = $prop->RegionName(); ?>
      <h2><span class="region"><?php echo $region; ?></span></h2>
    </div>
    <div class="clr"></div>
  </header>
  <div class="content">
    <?php $shortdesc = $prop->ShortDesc(); ?>
    <?php if (!empty($shortdesc)): ?>
      <div class="excerpt">
        <?php echo $shortdesc; ?>
      </div>
    <?php endif; ?>
    <div class="row">
      <div class="col col-img">
        <div class="current-price">
          <span class="type"><?=$prop->getValuta()?></span><?=$prop->Price(true)?><span class="pt"><?php echo $prop->PriceType(); ?></span>
        </div>
        <div class="contact">
          <h4>Kapcsolatfelvétel:</h4>
          <div class="name">
            <?php echo $prop->AuthorName(); ?>
          </div>
          <div class="telefon">
            <?php echo $prop->AuthorPhone(); ?>
          </div>
          <div class="email">
            <?php echo $prop->AuthorEmail(); ?>
          </div>
          <div class="id">
            Azonosító: #<?php echo $prop->ID(); ?>
          </div>
        </div>
      </div>
      <div class="col col-desc">
        <h3>Ingatlan paraméterek</h3>
        <div class="params">
          <?php $params = $prop->Paramteres(); ?>
          <table>
            <tbody>
              <tr>
              <?php $st = 0; foreach ((array)$params as $p): $st++; ?>
                <th><?php echo $p['name']; ?></th>
                <td><?php if($p['name'] == 'Ingatlan állapota') { echo str_repeat('*', (int)$p['value']); } else { echo $p['value']. $p['after']; } ?></td>
                <?php if ($st%2 === 0): ?>
                </tr><tr>
                <?php endif; ?>
              <?php endforeach; ?>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="divider"></div>
    <div class="row">
      <div class="col col-img">
        <div class="img">
          <?php $img = $prop->ProfilImg(); ?>
          <img src="<?php echo $img; ?>" alt="">
        </div>
      </div>
      <div class="col col-desc">
        <h3>Ingatlan leírása</h3>
        <div class="desc">
          <?php echo $prop->Description(); ?>
        </div>
      </div>
    </div>
    <div class="page-break"></div>
    <div class="">
      <?php
        if($hotel_desc){
          echo '<h1>Szálloda leírása</h1>';
          foreach ($hotel_desc as $did => $de):
            $des = $de['description'];
            ?>
            <h3><?php echo $de['name']; ?></h3>
            <p><?php echo $des; ?></p>
            <?php
          endforeach;
        }
      ?>
    </div>
  </div>
</body>
</html>
