<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  use Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\PostObjectTranslatorFactory;
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\AbstractHandler' ) ) {
  
    /**
     * Abstract base class for post object handler
     */
    class AbstractHandler {
      
      private $perPage;
      private $type;
      private $updateHook;
      
      /**
       * Constructor
       * 
       * @param string $recurrence recurrence interval
       * @param string $type type of the handled post object
       */
      public function __construct($perPage, $recurrence, $type) {
        $this->perPage = $perPage;
        $this->type = $type;
        $this->updateHook = "linkedEventsEpkalenteriCronHook-$type";
        
        add_action($this->updateHook, [ $this, "onUpdateHook" ]);
        add_action('edit_post', [ $this, "onEditPost" ]);
        add_filter('cron_schedules', [ $this, "cronSchedules" ]);
        
        if (!wp_next_scheduled($this->updateHook)) {
          wp_schedule_event(time(), $recurrence, $this->updateHook);
        }
      }
      
      public function cronSchedules( $schedules ) {
        $schedules['5s'] = array(
          'interval' => 5,
          'display'  => esc_html__( 'Every Five Seconds' ),
        );
 
        return $schedules;
      }
      
      /**
       * Function executed on scheduled times.
       */
      public function onUpdateHook() {
        $this->executeUpdateTask();
      }
      
      /**
       * Function executed when a post is edited
       * 
       * @param int $postId postId
       */
      public function onEditPost($postId) {
        $postType = get_post_type($postId);
        if ($postType === $this->type) {
          $postObject = get_post($postId);
          $this->updatePostObject($postObject);
        }
      }
      
      /**
       * Executes an update task
       */
      private function executeUpdateTask() {
        $postObjects = $this->nextPage($this->perPage);
        $resources = PostObjectTranslatorFactory::translatePostObjects($postObjects);
        
        foreach ($resources as $postId => $resource) {
          $this->createUpdateResource($postId, $resource); 
        }
      }
      
      /**
       * Updates single post object
       * 
       * @param \WP_Post $postObject
       */
      private function updatePostObject($postObject) {
        $resource = PostObjectTranslatorFactory::translatePostObject($postObject);
        if ($resource) {
          $this->createUpdateResource($postObject->ID, $resource);
        } else {
          error_log("Failed to translate $postObject->ID of type $postObject->post_type");
        }
      }
      
      /**
       * Creates or updates a Linked Events resource
       * 
       * @param int $postId
       * @param \ArrayAccess $resource Linked Events resource
       */
      private function createUpdateResource($postId, $resource) {
        if ($resource->getId()) {
          try {
            $this->updateResource($resource);
          } catch (\Metatavu\LinkedEvents\ApiException $e) {
            $this->logApiException($e);
          }
        } else {
          try {
            $this->createResource($postId, $resource);
          } catch (\Metatavu\LinkedEvents\ApiException $e) {
            $this->logApiException($e);
          }
        }
      }
      
      /**
       * Returns offset for the update task
       * 
       * @return int offset
       */
      protected function getUpdateOffset() {
        $value = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue($this->getUpdateOffsetSetting());
        if (!$value) {
          return 0;
        }

        return intval($value);
      }
      
      /**
       * Sets the offset for the update task
       * 
       * @param int $offset new offset
       */
      protected function setUpdateOffset($offset) {
        \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::setValue($this->getUpdateOffsetSetting(), strval($offset));
      }
      
      /**
       * Returns next page of specified post type
       * 
       * @param int $perPage results per page
       * @return [object] type array of results
       */
      protected function nextPage($perPage) {
        $offset = $this->getUpdateOffset();
        
        $results = get_posts([
          'posts_per_page'   => $perPage,
	        'offset'           => $offset, 
          'post_type'        => $this->type,
          'post_status'      => 'publish',
          'suppress_filters' => true 
        ]);
        
        $this->setUpdateOffset(sizeof($results) < $perPage ? 0 : $offset + $perPage);
        
        return $results;
      }
      
      /**
       * Updates Linked Events id associated with post
       * 
       * @param int $postId postId
       * @param string $linkedEventsId linkedEventsId
       */
      protected function setLinkedEventsId($postId, $linkedEventsId) {
        update_post_meta($postId, 'linkedevents-id', $linkedEventsId);
      }
      
      /**
       * Logs an API error
       * 
       * @param \Metatavu\LinkedEvents\ApiException $e exception 
       */
      protected function logApiException($e) {
        error_log("Keyword create failed on [" . $e->getCode() . ']: ' . json_encode($e->getResponseBody()));
      }
      
      /**
       * Returns name for the update offset setting
       * 
       * @return string name for the offset setting
       */
      private function getUpdateOffsetSetting() {
        return $this->type . '-offset';
      }
      
    }
    
  }
  
?>