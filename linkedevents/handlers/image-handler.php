<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\ImageHandler' ) ) {
  
    class ImageHandler extends AbstractHandler {
      
      private $filterApi;
      
      public function __construct() {
        parent::__construct('attachment', 'save_post');
        $this->imageApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getImageApi();
        add_filter('wp_update_attachment_metadata', [$this, "onUpdateAttachmentMetaData"], 10, 2);
      }
      
      /**
       * {@inheritDoc}
       */
      protected function getPosts($perPage, $offset, $type) {
        return get_posts([
          'posts_per_page'   => $perPage,
	        'offset'           => $offset, 
          'post_type'        => $type,
          'suppress_filters' => true 
        ]);
      }

      /**
       * Fires when attachment metadata is updated
       * 
       * @param $data
       * @param $postId
       */
      public function onUpdateAttachmentMetaData($data, $postId) {
        $this->handlePostUpdate($postId);
        return $data;
      }
      
      /**
       * Updates image into Linked Events
       * 
       * @param \Metatavu\LinkedEvents\Model\Image $resource image resource
       */
      public function updateResource($postId, $resource) {
        $this->imageApi->imageUpdate($resource->getId(), $resource);
      }
      
      /**
       * Creates image into Linked Events
       * 
       * @param int $postId postId
       * @param \Metatavu\LinkedEvents\Model\Image $resource image resource
       */
      public function createResource($postId, $resource) {
        $created = $this->imageApi->imageCreate(null, [
          'url' => $resource->getUrl()
        ]);
        
        $this->setLinkedEventsId($postId, $created->getId());
      }
      
    }
    
  }
  
  new ImageHandler();
  
?>