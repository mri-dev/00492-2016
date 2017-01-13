<?php

class PropertyFactory
{
  const PROPERTY_TAXONOMY_META_PREFIX = '_listing_';
  const LOG_CHANGE_DB = 'listing_change_history';
  const LOG_VIEW_DB = 'listing_views';
  const PRICE_TYPE_FIX_INDEX = 0;

  public $property_taxonomies_id = array('property-types', 'property-condition', 'property-heating', 'status', 'locations');
  public $property_status_colors = array(
    'publish' => '#c6e8c6',
    'draft'   => '#e2e2e2',
    'pending' => '#ffd383',
    'future' => '#fff8a8',
    'archived' => '#ff9797',
  );

  public $price_types = array(
    'fix' => 0,
    'per_nm' => 1,
    'per_ha' => 2,
    'per_month' => 3,
   );

  public function getValuta()
  {
    return '€';
  }

  public function __construct()
  {
    return $this;
  }

  public function StatusText( $status = null )
  {
    switch ( $status ) {
      case 'publish':
        return __( 'Közzétéve (aktív)', 'ti');
      break;
      case 'pending':
        return __( 'Függőben', 'ti');
      break;
      case 'draft':
          return __( 'Vázlat', 'ti');
      break;
      case 'archived':
        return __( 'Archivált', 'ti');
      break;
      case 'future':
        return __( 'Időzített', 'ti');
      break;
      default:
        return $status;
      break;
    }
  }

  public function i18n_pricetype_values( $index )
  {
    $texts = array(
      0 => __('Fix ár', 'ti'),
      1 => sprintf(__('%s / nm', 'ti'), $this->getValuta()),
      2 => sprintf(__('%s / Ha', 'ti'), $this->getValuta()),
      3 => sprintf(__('%s / hó', 'ti'), $this->getValuta()),
    );
  }

  public static function i18n_taxonomy_values( $key )
  {
    $texts = array(
      'elado' => __('Eladó', 'ti'),
      'kiado' => __('Kiadó', 'ti'),
      'berbeado' => __('Bérbeadó', 'ti'),

      'lakas' => __('Lakás', 'ti'),
      'haz' => __('Ház', 'ti'),
      'nyaralo' => __('Nyaraló', 'ti'),
      'telek' => __('Telek', 'ti'),
      'ipartelep' => __('Ipartelep', 'ti'),
      'kereskedelmi' => __('Kereskedelmi ingatlan', 'ti'),
      'mezogazdasagi' => __('Mezőgazdasági terület', 'ti'),
      'garazs' => __('Garázs', 'ti'),
      'csaladi_haz' => __('Családi ház', 'ti'),
      'panel' => __('Panel', 'ti'),
      'sorhaz' => __('Sorház', 'ti'),
      'tegla' => __('Tégla', 'ti'),

      'uj' => __('Új', 'ti'),
      'felkesz' => __('Félkész', 'ti'),
      'azonnal-koltozheto' => __('Azonnal költözhető', 'ti'),
      'hasznalt' => __('Használt', 'ti'),
      'felujitando' => __('Felújítandó', 'ti'),
      'felujitott' => __('Felújított', 'ti'),
      'lakhatatlan' => __('Lakhatatlan', 'ti'),
      'lakhato' => __('Lakható', 'ti'),
      'tehermentes' => __('Tehermentes', 'ti'),

      'gaz-cirko' => __('Gáz / Cirkó', 'ti'),
      'elektromos' => __('Elektromos', 'ti'),
      'gaz-konvektor' => __('Gáz / Konvektor', 'ti'),
      'gaz-napkollektor' => __('Gáz + Napkollektor', 'ti'),
      'gazkazan' => __('Gázkazán', 'ti'),
      'geotermikus' => __('Geotermikus', 'ti'),
      'hazkozponti' => __('Gáz / Cirkó', 'ti'),
      'tavfutes' => __('Távfűtés', 'ti'),
      'tavfutes-egyedi-meressel' => __('Távfűtés egyedi mérssel', 'ti'),
    );

    $t = $texts[$key];

    if (empty($t)) {
      return $key;
    }

    return $t;
  }
}

?>
