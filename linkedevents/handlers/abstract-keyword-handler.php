<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\AbstractKeywordHandler' ) ) {
  
    class AbstractKeywordHandler extends AbstractHandler {
      
      private $filterApi;
      
      public function __construct($type) {
        parent::__construct($type);
        $this->filterApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getFilterApi();
      }
      
      public function updateResource($postId, $resource) {
        $this->filterApi->keywordUpdate($resource->getId(), $resource);
      }
      
      public function createResource($postId, $resource) {
        $created = $this->filterApi->keywordCreate($resource);
        $this->setLinkedEventsId($postId, $created->getId());
      }
      
    }
    
  }
  
?>