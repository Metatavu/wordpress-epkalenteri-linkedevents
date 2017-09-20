<?php
  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  define(LINKEDEVENTS_EPKALENTERI_SETTINGS_OPTION, 'linkedevents-epkalenteri');
  define(LINKEDEVENTS_EPKALENTERI_SETTINGS_GROUP, 'linkedevents-epkalenteri');
  define(LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE, 'linkedevents-epkalenteri');
  
  if (!class_exists( 'Metatavu\LinkedEvents\Wordpress\EPKalenteri\SettingsUI' ) ) {

    class SettingsUI {

      public function __construct() {
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_menu', array($this, 'adminMenu'));
      }

      public function adminMenu() {
        add_options_page (__( "Linked Events Settings", 'linkedevents-epkalenteri' ), __( "Linked Events EP", 'linkedevents-epkalenteri' ), 'manage_options', LINKEDEVENTS_EPKALENTERI_SETTINGS_OPTION, [$this, 'settingsPage']);
      }

      public function adminInit() {
        register_setting(LINKEDEVENTS_EPKALENTERI_SETTINGS_GROUP, LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE);
        add_settings_section('api', __( "API Settings", 'linkedevents-epkalenteri' ), null, LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE);
        add_settings_section('geocoder', __( "GEOCoder Settings", 'linkedevents-epkalenteri' ), null, LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE);
        $this->addOption('api', 'url', 'api-url', __( "API URL", 'linkedevents-epkalenteri'));
        $this->addOption('api', 'text', 'api-key', __( "API Key", 'linkedevents-epkalenteri' ));
        $this->addOption('api', 'text', 'datasource', __( "Datasource", 'linkedevents-epkalenteri' ));
        $this->addOption('api', 'text', 'publisher', __( "Publisher Organization", 'linkedevents-epkalenteri' ));
        $this->addOption('geocoder', 'text', 'geocoder-provider', __( "Geocoder provider (nominatim [default], google_maps)", 'linkedevents-epkalenteri' ));
        $this->addOption('geocoder', 'text', 'geocoder-nominatim-server', __( "Nominatim Server (defaults to OpenStreetMaps)", 'linkedevents-epkalenteri' ));
        $this->addOption('geocoder', 'text', 'geocoder-google-maps-apikey', __( "Google Maps API Key", 'linkedevents-epkalenteri' ));
      }

      private function addOption($group, $type, $name, $title) {
        add_settings_field($name, $title, [$this, 'createFieldUI'], LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE, $group, [
          'name' => $name, 
          'type' => $type
        ]);
      }

      public function createFieldUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $value = Settings::getValue($name);
        echo "<input id='$name' name='" . LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE . "[$name]' size='42' type='$type' value='$value' />";
      }

      public function settingsPage() {
        if (!current_user_can('manage_options')) {
          wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        echo '<div class="wrap">';
        echo "<h2>" . __( "Linked Events", 'linkedevents-epkalenteri') . "</h2>";
        echo '<form action="options.php" method="POST">';
        settings_fields(LINKEDEVENTS_EPKALENTERI_SETTINGS_GROUP);
        do_settings_sections(LINKEDEVENTS_EPKALENTERI_SETTINGS_PAGE);
        submit_button();
        echo "</form>";
        echo "</div>";
      }
    }

  }
  
  if (is_admin()) {
    $settingsUI = new SettingsUI();
  }

?>