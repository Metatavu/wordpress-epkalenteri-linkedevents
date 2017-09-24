<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\EventHandler' ) ) {
  
    class EventHandler extends AbstractHandler {
      
      private $eventApi;
      
      public function __construct() {
        parent::__construct('event');
        $this->eventApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getEventApi();
      }
      
      public function updateResource($resource) {
        $this->eventApi->eventUpdate($resource->getId(), $resource);
      }
      
      public function createResource($postId, $resource) {
        $created = $this->eventApi->eventCreate($resource);
        $this->setLinkedEventsId($postId, $created->getId());
      }
      
    }
    
  }
  
  new EventHandler();
  
?>