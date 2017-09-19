<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\KeywordHandler' ) ) {
  
    class KeywordHandler extends AbstractHandler {
      
      private $filterApi;
      
      public function __construct() {
        parent::__construct(1, '5s', 'keyword');
        $this->filterApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getFilterApi();
      }
      
      public function updateResource($resource) {
        $this->filterApi->keywordUpdate($resource->getId(), $resource);
      }
      
      public function createResource($postId, $resource) {
        $created = $this->filterApi->keywordCreate($resource);
        $this->setLinkedEventsId($postId, $created->getId());
      }
      
    }
    
  }
  
  new KeywordHandler();
  
?>