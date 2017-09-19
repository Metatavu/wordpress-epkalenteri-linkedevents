<?php
  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  require_once('settings-ui.php');  
  
  define(LINKEDEVENTS_SETTINGS_OPTION, 'linkedevents-epkalenteri');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings' ) ) {

    class Settings {

      /**
       * Returns setting value
       * 
       * @param string $name setting name
       * @return string setting value
       */
      public static function getValue($name) {
        $options = get_option(LINKEDEVENTS_SETTINGS_OPTION);
        if ($options) {
          return $options[$name];
        }

        return null;
      }
      
      /**
       * Sets a value for settings
       * 
       * @param string $name setting name
       * @param string $value setting value
       */
      public static function setValue($name, $value) {
        $options = get_option(LINKEDEVENTS_SETTINGS_OPTION);
        if (!$options) {
          $options = [];
        } 
        
        $options[$name] = $value;
        
        update_option(LINKEDEVENTS_SETTINGS_OPTION, $options);
      }
      
    }

  }
  

?>