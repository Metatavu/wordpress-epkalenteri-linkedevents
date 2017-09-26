<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\EventHandler' ) ) {
  
    class EventHandler extends AbstractHandler {
      
      private $eventApi;
      
      public function __construct() {
        parent::__construct('event', 'acf/save_post');
        $this->eventApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getEventApi();
      }
      
      /**
       * Updates event into the Linked Events
       * 
       * @param \Metatavu\LinkedEvents\Model\Event $resource
       */
      public function updateResource($postId, $resource) {
        $this->updateSubEvents($postId);
        $this->eventApi->eventUpdate($resource->getId(), $resource);
      }
      
      /**
       * Creates new event into the Linked Events
       * 
       * @param int post object id
       * @param \Metatavu\LinkedEvents\Model\Event $resource
       */
      public function createResource($postId, $resource) {
        $created = $this->eventApi->eventCreate($resource);
        $this->setLinkedEventsId($postId, $created->getId());
      }
      
      /**
       * Updates event's child post objects
       * 
       * @param type $postId post id
       */
      private function updateSubEvents($postId) {
        $children = get_children([
          'numberposts' => -1,
		      'post_parent' => $postId,
          'post_status' => 'publish',
          'post_type' => 'event'
        ]);
        
        foreach ($children as $child) {
          $this->updatePostObject($child);
        }
      }
      
    }
    
  }
  
  new EventHandler();
  
?>