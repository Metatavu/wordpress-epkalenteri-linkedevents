<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\EventHandler' ) ) {
  
    class EventHandler extends AbstractHandler {
      
      public function __construct() {
        parent::__construct(5, '5s', 'event');
      }
      
      public function executeUpdateTask() {
        
      } 
      
    }
    
  }
  
  // new EventHandler();
  
?>