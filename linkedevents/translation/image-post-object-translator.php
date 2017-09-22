<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-post-object-translator.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\ImagePostObjectTranslator' ) ) {
  
    class ImagePostObjectTranslator extends AbstractPostObjectTranslator {
      
      public function __construct($postObject) {
        parent::__construct($postObject);
      }
      
      /**
       * Translates image post object into LinkedEvents image
       *
       * @return \Metatavu\LinkedEvents\Model\Image LinkedEvents image
       */
      public function translatePostObject() {
        if (strpos($this->postObject->post_mime_type, 'image') === false) {
          return false;
        }

        $result = new \Metatavu\LinkedEvents\Model\Image([
          'id' => $this->getLinkedEventsId(),
          'name' => $this->getImageName(),
          'publisher' => $this->getPublisher(),
          'createdTime' => $this->getCreatedTime($this->postObject),
          'lastModifiedTime' => $this->getModifiedTime($this->postObject),
          'url' => wp_get_attachment_url($this->postObject->ID)
        ]);
        
        return $result;
      }
      
      /**
       * Returns image's name
       * 
       * @return string name object
       */
      private function getImageName() {
        return $this->postObject->post_title;
      }
      
    }
    
  }
  
  PostObjectTranslatorFactory::registerTranslator("attachment", ImagePostObjectTranslator::class);
  
?>