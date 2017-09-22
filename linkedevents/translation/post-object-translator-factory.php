<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\PostObjectTranslatorFactory' ) ) {
  
    /**
     * Factory class for post object translators
     */
    class PostObjectTranslatorFactory {
      
      /**
       * @var [string]
       */
      private static $classes = [];
      
      /**
       * Registers new translator
       * 
       * @param string $postType type of post the translator translates
       * @param string $class classes for the translator
       */
      public static function registerTranslator($postType, $class) {
        self::$classes[$postType] = $class;
      }
      
      /**
       * Creates new translator
       * 
       * @param \WP_Post $postObject post object
       * @return AbstractPostObjectTranslator translator
       */
      public static function createTranslator($postObject) {
        return new self::$classes[$postObject->post_type]($postObject);
      }
      
      /**
       * Translates post object into Linked Events resource
       * 
       * @param \WP_Post $postObject post object
       */
      public static function translatePostObject($postObject) {
        $translator = self::createTranslator($postObject);
        if ($translator) {
          return $translator->translatePostObject($postObject);
        } else {
          error_log("Could not create translator for " . $postObject->post_type);
        }
        
        return null;
      }
      
      /**
       * Translates post objects into Linked Events resources
       * 
       * @param [\WP_Post] $postObjects post objects
       * @return array
       */
      public static function translatePostObjects($postObjects) {
        $result = [];
        
        foreach ($postObjects as $postObject) {
          $resource = self::translatePostObject($postObject);
          if ($resource) {
            $result[$postObject->ID] = $resource;
          } else {
            if ($resource === null) {
              error_log("Failed to translate $postObject->ID of type $postObject->post_type");
            }
          }
        }
        
        return $result;
      }
      
    }
    
  }
  
?>