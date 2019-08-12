<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  use Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\PostObjectTranslatorFactory;
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\AbstractHandler' ) ) {
  
    /**
     * Abstract base class for post object handler
     */
    class AbstractHandler {
      
      private $type;
      private $updateHook;
      
      /**
       * Constructor
       * 
       * @param string $type type of the handled post object
       */
      public function __construct($type, $updateAction) {
        $this->type = $type;
        $this->updateHook = "linkedEventsEpkalenteriCronHook" . ucfirst($type);
        $recurrence = $this->getUpdateInterval();
        
        add_filter('cron_schedules', [ $this, "cronSchedules" ]);
        
        add_action($this->updateHook, [ $this, 'onUpdateHook' ]);
        add_action($updateAction, [ $this, "onSavePost" ], 99999);
        add_action("before_delete_post", [ $this, "onBeforeDeletePost" ]);
        add_action("trashed_post", [ $this, "onTrashedPost" ], 99999);
        add_action("untrashed_post", [ $this, "onUntrashedPost" ], 99999);
        add_action('update_option_linkedevents-epkalenteri', [ $this, "onOptionsUpdated" ]);
        
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
       * Deletes a Linked Events resource
       * 
       * @param int $postId
       * @param \ArrayAccess $resource Linked Events resource
       */
      public function deleteResource($postId, $resource) {
        // TODO: Default implementation is currently null
      }

      /**
       * Function executed on scheduled times.
       */
      public function onUpdateHook() {
        $this->executeUpdateTask();
      }
      
      /**
       * Function executed after post is saved
       * 
       * @param int $postId postId
       */
      public function onSavePost($postId) {
        $this->handlePostUpdate($postId);
      }
      
      /**
       * Function executed before post is deleted
       * 
       * @param int $postId postId
       */
      public function onBeforeDeletePost($postId) {
        $this->handlePostDelete($postId);
      }

      /**
       * Function executed after post trash
       * 
       * @param int $postId postId
       */
      public function onTrashedPost($postId) {
        $this->handlePostUpdate($postId);
      }

      /**
       * Function executed after post untrash
       * 
       * @param int $postId postId
       */
      public function onUntrashedPost($postId) {
        $this->handlePostUpdate($postId);
      }

      /**
       * Function executed when plugin settings are updated
       */
      public function onOptionsUpdated() {
        $currentInterval = wp_get_schedule($this->updateHook);
        $desiredInterval = $this->getUpdateInterval();
        
        if ($currentInterval != $desiredInterval) {
          wp_clear_scheduled_hook($this->updateHook);
        }
      }
      
      /**
       * Executes an update task
       */
      private function executeUpdateTask() {
        $postObjects = $this->nextPage($this->getUpdateBatch());
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
      protected function updatePostObject($postObject) {
        $resource = PostObjectTranslatorFactory::translatePostObject($postObject);
        if ($resource) {
          $this->createUpdateResource($postObject->ID, $resource);
        } else {
          if ($resource === null) {
            error_log("Failed to translate $postObject->ID of type $postObject->post_type");
          }
        }
      }
      
      /**
       * Deletes single post object
       * 
       * @param \WP_Post $postObject
       */
      protected function deletePostObject($postObject) {
        $resource = PostObjectTranslatorFactory::translatePostObject($postObject);
        if ($resource) {
          $this->deleteResource($postObject->ID, $resource);
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
            $this->updateResource($postId, $resource);
          } catch (\Metatavu\LinkedEvents\ApiException $e) {
            $this->logApiException($e, $postId, "update");
          } catch (Error $e) {
            $this->logError($e, $postId, "update");
          }
        } else {
          try {
            $this->createResource($postId, $resource);
          } catch (\Metatavu\LinkedEvents\ApiException $e) {
            $this->logApiException($e, $postId, "create");
          } catch (Error $e) {
            $this->logError($e, $postId, "update");
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
        $results = $this->getPosts($perPage, $offset, $this->type);
        $this->setUpdateOffset(sizeof($results) < $perPage ? 0 : $offset + $perPage);
        return $results;
      }
      
      /**
       * Lists post objects
       * 
       * @param type $perPage number of results per page
       * @param type $offset offset
       * @param type $type post type
       * @return \WP_Post[] post objects
       */
      protected function getPosts($perPage, $offset, $type) {
        return get_posts([
          'posts_per_page'   => $perPage,
	        'offset'           => $offset, 
          'post_type'        => $type,
          'post_status'      => 'publish',
          'suppress_filters' => true 
        ]);
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
       * @param string $id object id
       * @param string $operation operation
       */
      protected function logApiException($e, $id, $operation) {
        error_log("$this->type ($id) $operation failed on [" . $e->getCode() . ']: ' . json_encode($e->getResponseBody()));
      }
      
      /**
       * Logs an error
       * 
       * @param \Error $e exception 
       * @param string $id object id
       * @param string $operation operation
       */
      protected function logError($e, $id, $operation) {
        error_log("$this->type ($id) $operation throw " . $e->getMessage());
      }

      /**
       * Handle post update
       * 
       * @param $postId
       */
      protected function handlePostUpdate($postId) {
        $postType = get_post_type($postId);

        if ($postType === $this->type) {
          $postObject = get_post($postId);
          $this->updatePostObject($postObject);
        }
      }

      /**
       * Handle post delete
       * 
       * @param $postId
       */
      protected function handlePostDelete($postId) {
        $postType = get_post_type($postId);

        if ($postType === $this->type) {
          $postObject = get_post($postId);
          $this->deletePostObject($postObject);
        }
      }
      
      /**
       * Returns name for the update offset setting
       * 
       * @return string name for the offset setting
       */
      private function getUpdateOffsetSetting() {
        return $this->type . '-offset';
      }
      
      /**
       * Returns update interval for the task
       * 
       * @return int offset
       */
      private function getUpdateInterval() {
        $interval = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue($this->getUpdateIntervalSetting());
        return $interval ? $interval : 'daily';
      }
      
      /**
       * Returns update interval for the task
       * 
       * @return int offset
       */
      private function getUpdateBatch() {
        $batch = intval(\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue($this->getUpdateBatchSetting()));
        return $batch ? $batch : 5;
      }
      
      /**
       * Returns update interval setting name
       * 
       * @return string update interval setting name
       */
      private function getUpdateIntervalSetting() {
        return "$this->type-update-interval"; 
      }
      
      /**
       * Returns update batch setting name
       * 
       * @return string update batch setting name
       */
      private function getUpdateBatchSetting() {
        return "$this->type-update-batch"; 
      }
    }
    
  }
  
?>