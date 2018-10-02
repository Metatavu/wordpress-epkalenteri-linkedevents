<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-post-object-translator.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\PlacePostObjectTranslator' ) ) {
  
    class PlacePostObjectTranslator extends AbstractPostObjectTranslator {
      
      public function __construct($postObject) {
        parent::__construct($postObject);
      }
      
      /**
       * Translates place post object into LinkedEvents place
       *
       * @return \Metatavu\LinkedEvents\Model\Place LinkedEvents place
       */
      public function translatePostObject() {
        $locationMeta = $this->getLocationMeta();
        $geocodedAddress = $locationMeta['address'] ? $this->geocodeQuery($locationMeta['address']) : null;
        
        $result = new \Metatavu\LinkedEvents\Model\Place([
          'id' => $this->getLinkedEventsId(),
          'name' => $this->getPlaceName(),
          'originId' => $this->getOriginId(),
          'createdTime' => $this->getCreatedTime($this->postObject),
          'lastModifiedTime' => $this->getModifiedTime($this->postObject),
          'position' => $this->getPosition($locationMeta, $geocodedAddress),
          'streetAddress' => $this->getStreetAddress($locationMeta['address']),
          'addressLocality' => $this->getAddressLocality($geocodedAddress),
          'addressRegion' => $this->getAddressRegion($geocodedAddress),
          'postalCode' => $geocodedAddress ? $geocodedAddress->getPostalCode() : null,
          'postOfficeBoxNum' => null,
          'addressCountry' => $this->getAddressCountry($geocodedAddress),
          'deleted' => false,
          'dataSource' => $this->getDataSource(),
          'publisher' => $this->getPublisher()
        ]);
         
        return $result;
      }
      
      /**
       * Returns place's name
       * 
       * @return \Metatavu\LinkedEvents\Model\PlaceName name object
       */
      private function getPlaceName() {
        return new \Metatavu\LinkedEvents\Model\PlaceName([
          'fi' => $this->postObject->post_title
        ]);
      }
      
      /**
       * Returns street address part from the address string
       * 
       * @param string $address address string
       * @return \Metatavu\LinkedEvents\Model\PlaceStreetAddress street address
       */
      private function getStreetAddress($address) {
        if ($address) {
          $parts = explode(",", $address);
          
          return new \Metatavu\LinkedEvents\Model\PlaceStreetAddress([
            "fi" => $parts[0]
          ]);
        }
        
        return null;
      }
      
      /**
       * Returns locality from geocoded address
       * 
       * @param \Geocoder\Model\Address $geocodedAddress geocoded address
       * @return \Metatavu\LinkedEvents\Model\PlaceAddressLocality address locality
       */
      private function getAddressLocality($geocodedAddress) {
        if ($geocodedAddress && $geocodedAddress->getLocality()) {
          return new \Metatavu\LinkedEvents\Model\PlaceAddressLocality([
            'fi' => $geocodedAddress->getLocality()
          ]);
        }

        // Geocoder puts the address locality in either the "locality"
        // or "region" field, use region if locality is not available
        return new \Metatavu\LinkedEvents\Model\PlaceAddressLocality([
          'fi' => $this->getAddressRegion($geocodedAddress)
        ]);
      }
      
      /**
       * Returns region from geocoded address
       * 
       * @param \Geocoder\Model\Address $geocodedAddress geocoded address
       * @return string address region
       */
      private function getAddressRegion($geocodedAddress) {
        if ($geocodedAddress && $geocodedAddress->getAdminLevels() && $geocodedAddress->getAdminLevels()->count() > 0) {
          $adminLevel = $geocodedAddress->getAdminLevels()->first();
          return $adminLevel->getName();
        }
        
        return null;
      }
      
      /**
       * Returns country from geocoded address
       * 
       * @param \Geocoder\Model\Address $geocodedAddress geocoded address
       * @return string country
       */
      private function getAddressCountry($geocodedAddress) {
        if ($geocodedAddress && $geocodedAddress->getCountry()) {
          return $geocodedAddress->getCountry()->getCode();
        }
        
        return "fi";
      }
      
      /**
       * Attempts to resolve place position. Primarily location meta is used 
       * but if location meta does not contain coordinates, geocoded address is used
       * 
       * @param type $locationMeta
       * @param \Geocoder\Model\Address $geocodedAddress geocoded address
       * @return \Metatavu\LinkedEvents\Model\PlacePosition
       */
      private function getPosition($locationMeta, $geocodedAddress) {
        $latitude = $locationMeta['lat'];
        $longitude = $locationMeta['lng'];
        
        if (!$latitude && !$longitude && $geocodedAddress && $geocodedAddress->getCoordinates()) {
          $latitude = $geocodedAddress->getCoordinates()->getLatitude();
          $longitude = $geocodedAddress->getCoordinates()->getLongitude();
        }
       
        if ($latitude && $longitude) {
          return new \Metatavu\LinkedEvents\Model\PlacePosition([
            'coordinates' => [floatval($latitude), floatval($longitude)],
            'type' => 'Point'
          ]);
        }
        
        return null;
      }
      
      /*
       * Returns location meta
       */
      private function getLocationMeta() {
        return get_post_meta($this->postObject->ID, 'location_map', true);
      }
      
    }
    
  }
  
  PostObjectTranslatorFactory::registerTranslator("place", PlacePostObjectTranslator::class);
  
?>