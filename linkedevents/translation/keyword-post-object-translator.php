<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-post-object-translator.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\KeywordPostObjectTranslator' ) ) {
  
    class KeywordPostObjectTranslator extends AbstractPostObjectTranslator {
      
      public function __construct($postObject) {
        parent::__construct($postObject);
      }
      
      /**
       * Translates keyword post object into LinkedEvents keyword
       *
       * @return \Metatavu\LinkedEvents\Model\Keyword LinkedEvents keyword
       */
      public function translatePostObject() {
        $name = new \Metatavu\LinkedEvents\Model\KeywordName([
          'fi' => $this->getPostLocalizedTitle("fi"),
          'sv' => $this->getPostLocalizedTitle("sv"),
          'en' => $this->getPostLocalizedTitle("en")
        ]);
        
        $result = new \Metatavu\LinkedEvents\Model\Keyword([
          'id' => $this->getLinkedEventsId(),
          'name' => $name,
          'originId' => $this->getOriginId(),
          'createdTime' => $this->getCreatedTime($this->postObject),
          'lastModifiedTime' => $this->getModifiedTime($this->postObject),
          'aggregate' => false,
          'dataSource' => $this->getDataSource()
        ]);

        return $result;
      }

    }
    
  }
  
  PostObjectTranslatorFactory::registerTranslator("keyword", KeywordPostObjectTranslator::class);
  PostObjectTranslatorFactory::registerTranslator("audience", KeywordPostObjectTranslator::class);
  PostObjectTranslatorFactory::registerTranslator("event_category", KeywordPostObjectTranslator::class);
  
?>