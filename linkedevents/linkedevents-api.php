<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri;
  
  require_once( __DIR__ . '/../vendor/autoload.php');
  require_once( __DIR__ . '/../settings/settings.php');
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api' ) ) {
    
     class Api {
      
      /**
       * Constructs EventApi instance
       * 
       * @return \Metatavu\LinkedEvents\Client\EventApi event api
       */
      public static function getEventApi() {
        $client = self::getClient();
        return new \Metatavu\LinkedEvents\Client\EventApi($client);
      }
      
      /**
       * Constructs FilterApi instance
       * 
       * @return \Metatavu\LinkedEvents\Client\FilterApi filter api
       */
      public static function getFilterApi() {
        $client = self::getClient();
        return new \Metatavu\LinkedEvents\Client\FilterApi($client);
      }
      
      /**
       * Constructs ImageApi instance
       * 
       * @return \Metatavu\LinkedEvents\Client\ImageApi image api
       */
      public static function getImageApi() {
        $client = self::getClient();
        return new \Metatavu\LinkedEvents\Client\ImageApi($client);
      }
      
      /**
       * Constructs LanguageApi instance
       * 
       * @return \Metatavu\LinkedEvents\Client\LanguageApi language api
       */
      public static function getLanguageApi() {
        $client = self::getClient();
        return new \Metatavu\LinkedEvents\Client\LanguageApi($client);
      }
      
      /**
       * Constructs SearchApi instance
       * 
       * @return \Metatavu\LinkedEvents\Client\SearchApi search api
       */
      public static function getSearchApi() {
        $client = self::getClient();
        return new \Metatavu\LinkedEvents\Client\SearchApi($client);
      }
      
      /**
       * Creates initialize ApiClient
       * 
       * @return \Metatavu\LinkedEvents\ApiClient api client
       */
      private function getClient() {
        return new \Metatavu\LinkedEvents\ApiClient(self::getConfiguration());
      }
      
      /**
       * Creates configuration
       * 
       * @return \Metatavu\LinkedEvents\Configuration configuration
       */
      private function getConfiguration() {
       $result = \Metatavu\LinkedEvents\Configuration::getDefaultConfiguration();
       $result->setHost(\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue("api-url"));
       $result->addDefaultHeader('apikey', \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue("api-key"));
       return $result;
      }
      
    }
  }

?>