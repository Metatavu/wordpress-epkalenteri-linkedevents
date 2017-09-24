<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\PlaceHandler' ) ) {
  
    class PlaceHandler extends AbstractHandler {
      
      private $filterApi;
      
      public function __construct() {
        parent::__construct('place');
        $this->filterApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getFilterApi();
      }
      
      public function updateResource($resource) {
        $this->filterApi->placeUpdate($resource->getId(), $resource);
      }
      
      public function createResource($postId, $resource) {
        $created = $this->filterApi->placeCreate($resource);
        $this->setLinkedEventsId($postId, $created->getId());
      }
      
    }
    
  }
  
  new PlaceHandler();
  
?>