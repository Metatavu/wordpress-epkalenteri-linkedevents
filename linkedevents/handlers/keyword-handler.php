<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-keyword-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\KeywordHandler' ) ) {
  
    class KeywordHandler extends AbstractKeywordHandler {
      
      public function __construct() {
        parent::__construct('keyword');
      }
      
    }
    
  }
  
  new KeywordHandler();
  
?>