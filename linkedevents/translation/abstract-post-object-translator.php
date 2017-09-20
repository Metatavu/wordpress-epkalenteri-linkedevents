<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/../../settings/settings.php');
  require_once( __DIR__ . '/../../vendor/autoload.php');
 
  use Geocoder\Query\GeocodeQuery;
  use Geocoder\Query\ReverseQuery;
  
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
       * Returns Linked Events id associated with the post object or null if post
       * object is not yet associated with Linked Events id
       * 
       * @return string linked events id associated with the post object
       */
      protected function getLinkedEventsId() {
        $linkedEventsId = get_post_meta($this->postObject->ID, 'linkedevents-id', true);
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
        return "$this->originIdPrefix:" . $this->postObject->ID;
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
      
    }
    
  }
  
?>