<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/../../settings/settings.php');
  require_once( __DIR__ . '/../../vendor/autoload.php');
 
  use Geocoder\Query\GeocodeQuery;
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\AbstractPostObjectTranslator' ) ) {
  
    /**
     * Abstract base class for post object translators
     */
    class AbstractPostObjectTranslator {
      
      /**
       * @var string originId's prefix
       */
      private $originIdPrefix = 'ep';
      
      /**
       * @var string linkedEventsId
       */
      private $linkedEventsId;
      
      /**
       * @var \WP_Post postObject
       */
      protected $postObject;
      
      /**
       * Creates new translator
       * 
       * @param \WP_Post $postObject
       */
      public function __construct($postObject) {
        $this->linkedEventsId = null;
        $this->postObject = $postObject;
      }
      
      /**
       * Returns current post id
       * 
       * @return int current post id
       */
      protected function getPostId() {
        return $this->postObject->ID; 
      }
      
      /**
       * Returns post meta
       *
       * @param string $name name
       * @param boolean $single single value
       * @return string meta value
       */
      protected function getPostMeta($name, $single) {
        return get_post_meta($this->getPostId(), $name, $single);
      }
      
      /**
       * Returns Linked Events id associated with the post object or null if post
       * object is not yet associated with Linked Events id
       * 
       * @return string linked events id associated with the post object
       */
      protected function getLinkedEventsId() {
        $linkedEventsId = get_post_meta($this->getPostId(), 'linkedevents-id', true);
        if ($linkedEventsId) {
          return $linkedEventsId;
        }
        
        return null;
      }
      
      /**
       * Returns origin id for the post object
       * 
       * @return string origin id for the post object
       */
      protected function getOriginId() {
        return "$this->originIdPrefix:" . $this->getPostId();
      }
      
      /**
       * Returns creation time from post object
       * 
       * @return \DateTime post object creation time
       */
      protected function getCreatedTime() {
        return new \DateTime($this->postObject->post_date_gmt);
      }
      
      /**
       * Returns modification time from post object
       * 
       * @return \DateTime post object creation time
       */
      protected function getModifiedTime() {
        return new \DateTime($this->postObject->post_modified_gmt);
      }
      
      /**
       * Parses date
       * 
       * @param string $string
       * @return \DateTime date
       */
      protected function parseDate($string) {
        $result = new \DateTime($string, new \DateTimeZone('Europe/Helsinki'));
        return $result; 
      }
      
      /**
       * Returns data source
       * 
       * @return string data source
       */
      protected function getDataSource() {
        return \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue('datasource');
      }
      
      /**
       * Returns publisher
       * 
       * @return string publisher
       */
      protected function getPublisher() {
        return \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue('publisher');
      }
      
      /**
       * Returns IdRef array for event ids
       * 
       * @param type $eventIds event ids
       * @return \Metatavu\LinkedEvents\Model\IdRef[] event IdRefs
       */
      protected function getEventRefs($eventIds) {
        $result = [];
        
        foreach ($eventIds as $eventId) {
          $result[] = $this->getEventRef($eventId);  
        }
        
        return $result;
      }
      
      /**
       * Returns reference into the event
       * 
       * @param string $eventId event id
       * @return \Metatavu\LinkedEvents\Model\IdRef reference into the event
       */
      protected function getEventRef($eventId) {
        return $this->getIdRef($this->getApiUrl() . "/event/$eventId/");
      }
      
      /**
       * Returns IdRefs for specified keyword sets
       * 
       * @param string[] $keys meta keys
       * @return \Metatavu\LinkedEvents\Model\IdRef[] keyword IdRefs
       */
      protected function getMetaKeywords($keys) {
        return $this->getKeywordRefs($this->getMetaKeywordIds($keys));
      }
      
      /**
       * Returns keywordIds for specified keyword set
       * 
       * @param string[] $keys meta keys
       * @return string[] keyword ids
       */
      private function getMetaKeywordIds($keys) {
        $result = [];
        $postIds = [];
        
        foreach ($keys as $key) {
          $postIds = array_merge($postIds, $this->getMetaKeywordPostIds($key));
        }
        
        foreach (array_filter(array_unique($postIds)) as $postId) {
          $keywordId = get_post_meta($postId, 'linkedevents-id', true);
          if ($keywordId) {
            $result[] = $keywordId;
          } else {
            error_log("Keyword id not found from the post " . $postId);
          }
        }
        
        return $result;
      }
      
      private function getMetaKeywordPostIds($key) {
       $ids = $this->getPostMeta($key, false);
       
        if ($ids && is_array($ids) && sizeof($ids) > 0) {
          if (is_array($ids[0])) {
            return $ids[0];
          } else {
            return $ids;
          }
        }
        
        return $ids;
      }
      
      /**
       * Returns IdRef array for keyword ids
       * 
       * @param type $keywordIds keyword ids
       * @return \Metatavu\LinkedEvents\Model\IdRef[] keyword IdRefs
       */
      protected function getKeywordRefs($keywordIds) {
        $result = [];
        
        foreach ($keywordIds as $keywordId) {
          $result[] = $this->getKeywordRef($keywordId);  
        }
        
        return $result;
      }
      
      /**
       * Returns reference into the keyword
       * 
       * @param string $keywordId keyword id
       * @return \Metatavu\LinkedEvents\Model\IdRef reference into the keyword
       */
      protected function getKeywordRef($keywordId) {
        return $this->getIdRef($this->getApiUrl() . "/keyword/$keywordId/");
      }
      
      /**
       * Returns reference into the location
       * 
       * @param string $locationId location id
       * @return \Metatavu\LinkedEvents\Model\IdRef reference into the location
       */
      protected function getPlaceRef($locationId) {
        return $this->getIdRef($this->getApiUrl() . "/place/$locationId/");
      }
      
      /**
       * Returns reference into the image
       * 
       * @param string $id image id
       * @return \Metatavu\LinkedEvents\Model\IdRef reference into the image
       */
      protected function getImageRef($id) {
        return $this->getIdRef($this->getApiUrl() . "/image/$id/");
      }
      
      /**
       * Returns IdRef object for id
       * 
       * @param string $id id
       * @return \Metatavu\LinkedEvents\Model\IdRef IdRef
       */
      protected function getIdRef($id) {
        $idRef = new \Metatavu\LinkedEvents\Model\IdRef();
        $idRef->setId($id);
        return $idRef;
      }
      
      /**
       * Extracts id from IdRef
       * 
       * @param \Metatavu\LinkedEvents\Model\IdRef $idRef
       * @return string id
       */
      protected function extractIdRefId($idRef) {
        if (isset($idRef)) {
          $id = rtrim($idRef->getId(), '/');
          $parts = explode("/", $id);
          return $parts[count($parts) - 1];
        }
        
        return null;
      }
      
      /**
       * Geocodes street address
       * 
       * @param string $address street address
       * @return \Geocoder\Model\Address geocoded address
       */
      protected function geocodeQuery($address) {
        $adapter = new \Http\Adapter\Guzzle6\Client();
        $provider = $this->getGeocoderProvider($adapter);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'fi');
        $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
        return $result->isEmpty() ? null : $result->first();
      }
      
      /**
       * Returns selected geocoder provider
       * 
       * @return \Geocoder\Provider provider
       */
      private function getGeocoderProvider($adapter) {
        // Geocoder\Exception\InvalidServerResponse
        $provider = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue('geocoder-provider');
          
        if ($provider === "google_maps") {
          $googleMapsApiKey = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue('geocoder-google-maps-apikey');
          return new \Geocoder\Provider\GoogleMaps\GoogleMaps($adapter, null, $googleMapsApiKey);
        }
        
        $nominatimServer = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue('geocoder-nominatim-server');
        if (!$nominatimServer) {
          $nominatimServer = 'http://nominatim.openstreetmap.org/search';
        }
        
        return new \Geocoder\Provider\Nominatim\Nominatim($adapter, $nominatimServer);
      }
      
      /**
       * Returns API URL
       * 
       * @return string API URL
       */
      private function getApiUrl() {
        return \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Settings\Settings::getValue("api-url");
      }
      
    }
    
  }
  
?>