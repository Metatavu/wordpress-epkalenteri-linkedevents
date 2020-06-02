<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-post-object-translator.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Translation\EventPostObjectTranslator' ) ) {
  
    class EventPostObjectTranslator extends AbstractPostObjectTranslator {
      
      public function __construct($postObject) {
        parent::__construct($postObject);
      }
      
      /**
       * Translates event post object into LinkedEvents event
       *
       * @return \Metatavu\LinkedEvents\Model\Event LinkedEvents event
       */
      public function translatePostObject() {
        $superPostId = $this->getSuperPostId();
        $superEvent = $superPostId ? $this->getSuperEvent() : null;
        
        if ($superPostId && !$superEvent) {
          error_log("Skipped " . $this->getPostId() . " synchronization because super event has not yet been synchronized");
          return false;
        }
        
        $result = new \Metatavu\LinkedEvents\Model\Event([
          'id' => $this->getLinkedEventsId(),
          'location' => $this->getLocation(),
          'keywords' => $this->getKeywords(),
          'superEvent' => $superEvent,
          'superEventType' => $superEvent ? 'recurring' : null,
          'publicationStatus' => $this->getPublicationStatus(), 
          'externalLinks' => $this->getExternalLinks(),
          'offers' => $this->getOffers(),
          'subEvents' =>  $this->getSubEvents(),
          'name' => $this->getEventName(),
          'images' => $this->getImages(),
          'createdTime' => $this->getCreatedTime($this->postObject),
          'lastModifiedTime' => $this->getModifiedTime($this->postObject),
          'infoUrl' => $this->getInfoUrl(),
          'description' => $this->getEventDescription(),
          'shortDescription' => $this->getEventShortDescription(),
          'locationExtraInfo' => $this->getLocaionExtraInfo(),
          'startTime' => $this->getEventStartTime(),
          'endTime' => $this->getEventEndTime(),
          'audience' => $this->getAudience(),
          'dataSource' => $this->getDataSource(),
          'publisher' => $this->getPublisher(),
          'provider' => $this->getProvider()
        ]);
        
        return $result;
      }
      
      /**
       * Returns event's name
       * 
       * @return \Metatavu\LinkedEvents\Model\EventName name object
       */
      private function getEventName() {
        return $this->getMetaLocaleArray($this->getPostId(), 'event_name');
      }
      
      /**
       * Returns event's short description
       * 
       * @return object short description object
       */
      private function getEventShortDescription() {
        return $this->getMetaLocaleArray($this->getPostId(), 'event_short_description');
      }
      
      /**
       * Returns event's description
       * 
       * @return object description object
       */
      private function getEventDescription() {
        $description = $this->getMetaLocaleArray($this->getPostId(), 'event_description');

        if (empty($description)) {
          $description = $this->getEventShortDescription();
        }

        return $description;
      }
      
      /**
       * Returns event start time
       * 
       * @return \DateTime event start time
       */
      private function getEventStartTime() {
        return $this->parseDate($this->getPostMeta('event_start_time', true));
      }
      
      /**
       * Returns event end time
       * 
       * @return \DateTime event end time
       */
      private function getEventEndTime() {
        return $this->parseDate($this->getPostMeta('event_end_time', true));
      }
      
      /**
       * Returns event publication status
       * 
       * @return string event publication status
       */
      private function getPublicationStatus() {
        if (get_post_status($this->postObject->ID) == "publish") {
          return 'public';
        }
        
        return 'draft';
      }
      
      /**
       * Returns event's external links
       * 
       * @return \Metatavu\LinkedEvents\Model\Eventlink[] external links
       */
      private function getExternalLinks() {
        $result = [];
        
        $this->addOriginLink($result);
        $this->addExternalLink($result, 'Ilmoittautuminen', 'event_registration_info_url');
        $this->addExternalLink($result, 'Facebook', 'event_facebook_url');
        $this->addExternalLink($result, 'Twitter', 'event_twitter_url');
        $this->addExternalLink($result, 'YouTube', 'event_youtube_url');
        $this->addExternalLink($result, 'Instagram', 'event_instagram_url');
        
        return $result;
      }
      
      /**
       * Adds origin link as external link into result array if it exists
       * 
       * @param type $result result array
       */
      private function addOriginLink(&$result) {
        $link = $this->getOriginLink();
        if ($link) {
          $result[] = $link;
        }
      }
      
      /**
       * Adds external link into result array if it exists
       * 
       * @param type $result result array
       * @param type $name link name
       * @param type $key meta key
       */
      private function addExternalLink(&$result, $name, $key) {
        $link = $this->getExternalLink($name, $key);
        if ($link) {
          $result[] = $link;
        }
      }
      
      /**
       * Returns origin link (permalink into Event in Wordpress) as external link object.
       * 
       * Null is returned when link resolving fails.
       * 
       * @return \Metatavu\LinkedEvents\Model\Eventlink external link object
       */
      private function getOriginLink() {
        $link = get_post_permalink($this->postObject);
        if ($link) {
          return new \Metatavu\LinkedEvents\Model\Eventlink([
            'name' => 'EP Kalenteri',
            'link' => $link,
            'language' => "fi"
          ]);
        }
        
        return null;
      }
      
      /**
       * Returns event's external link
       * 
       * @param type $name link name
       * @param type $key meta key
       * @return \Metatavu\LinkedEvents\Model\Eventlink external link
       */
      private function getExternalLink($name, $key) {
        $link = $this->getPostMeta($key, true);
        if ($link) {
          return new \Metatavu\LinkedEvents\Model\Eventlink([
            'name' => $name,
            'link' => $link,
            'language' => "fi"
          ]);
        }
        
        return null;
      }
      
      /**
       * Gets event info url
       * 
       * @return \Metatavu\LinkedEvents\Model\EventInfoUrl info url
       */
      private function getInfoUrl() {
        $url = $this->getPostMeta('event_info_url', true);
        if ($url) {
          return new \Metatavu\LinkedEvents\Model\EventInfoUrl([
            'fi' => $url
          ]);
        }
        
        return null;
      }
      
      /**
       * Returns location extra info
       * 
       * @return object location extra info
       */
      private function getLocaionExtraInfo() {
        $info = $this->getPostMeta('event_location_extra_info', true);
        if ($info) {
          return [
            'fi' => $info
          ];
        }
        
        return null;
      }
      
      /**
       * Returns event provider
       * 
       * @return object event provider object
       */
      private function getProvider() {
        $provider = $this->getPostMeta('event_provider_name', true);
        if ($provider) {
          return [
            'fi' => $provider
          ];
        }
        
        return null;
      }
      
      /**
       * Returns event super event
       * 
       * @return \Metatavu\LinkedEvents\Model\IdRef event super event
       */
      private function getSuperEvent() {
        $postId = get_post_meta($this->postObject->ID, 'super_event', true);
        if ($postId) {
          $eventId = get_post_meta($postId, 'linkedevents-id', true);
          if ($eventId) {
            return $this->getEventRef($eventId);
          } else {
            error_log('Could not resolve super event id for post ' . $this->postObject->ID);
          }
        }
        
        return null;
      }
      
      /**
       * Returns event sub events
       * 
       * @return \Metatavu\LinkedEvents\Model\IdRef[] sub events id refs
       */
      private function getSubEvents() {
        $children = get_children([
          'numberposts' => -1,
		      'post_parent' => $this->getPostId(),
          'post_status' => 'publish',
          'post_type' => 'event'
        ]);
                
        $eventIds = [];
        
        foreach ($children as $child) {
          $eventId = get_post_meta($child->ID, 'linkedevents-id', true);
          if ($eventId) {
            $eventIds[] = $eventId;
          } else {
            error_log('Could not resolve event id for post ' . $child->ID);
          }
        }
        
        return $this->getEventRefs($eventIds);
      }
      
      /**
       * Returns event keywords
       * 
       * @return \Metatavu\LinkedEvents\Model\Keyword[] event keywords
       */
      private function getKeywords() {
        $keywords = $this->getMetaKeywords(['event_keywords', 'event_categories']);
        if ($this->getPostMeta('event_promoted', true)) {
          $isPromoted = false;
          foreach ($keywords as $keyword) {
            if ($keyword['id'] === 'eptapahtumat:afpdsmmsf8') {
              $isPromoted = true;
            }
          }
          if (!$isPromoted) {
            array_push($keywords, $this->getIdRef('eptapahtumat:afpdsmmsf8'));
          }
        } else {
          $filterArray = array();
          foreach ($keywords as $keyword) {
            if (!($keyword['id'] === 'eptapahtumat:afpdsmmsf8')) {
              array_push($filterArray, $keyword);
            }
          }
          $keywords = $filterArray;
        }
        return $keywords;
      }
      
      /**
       * Returns event audience
       * 
       * @return \Metatavu\LinkedEvents\Model\Keyword[] audience
       */
      private function getAudience() {
        return $this->getMetaKeywords(['event_audience']);
      }
      
      /**
       * Returns event location
       * 
       * @return \Metatavu\LinkedEvents\Model\IdRef location id ref
       */
      private function getLocation() {
        $postId = $this->getPostMeta('event_location', true);
        if ($postId) {
          $locationId = get_post_meta($postId, 'linkedevents-id', true);
          if ($locationId) {
            return $this->getPlaceRef($locationId);
          } else {
            error_log("Could not find locationId from event " . $this->postObject->ID);
          }
        } else {
          error_log("Place not defined for event " . $this->postObject->ID);
        }
        
        return null;
      }
      
      /**
       * Returns event images
       * 
       * @return \Metatavu\LinkedEvents\Model\IdRef[] image id refs
       */
      private function getImages() {
        $postId = $this->getPostMeta('event_image', true);
        if ($postId) {
          $imageId = get_post_meta($postId, 'linkedevents-id', true);
          if ($imageId) {
            return [ $this->getImageRef($imageId) ];
          }
        }
        
        return [];
      }
      
      /**
       * Returns event offers
       * 
       * @return \Metatavu\LinkedEvents\Model\Offer[] offers
       */
      private function getOffers() {
        $priceInfoUrl = $this->getPostMeta('event_price_info_url', true);
        $infoUrl = $priceInfoUrl ? new \Metatavu\LinkedEvents\Model\OfferInfoUrl([
          'fi' => $priceInfoUrl
        ]) : null;
        
        return [new \Metatavu\LinkedEvents\Model\Offer([
          'price' => new \Metatavu\LinkedEvents\Model\OfferPrice($this->getMetaLocaleArray($this->getPostId(), 'event_price')),
          'infoUrl' => $infoUrl,
          'description' => new \Metatavu\LinkedEvents\Model\OfferDescription($this->getMetaLocaleArray($this->getPostId(), 'event_registration')),
          'isFree' => !!$this->getPostMeta('event_is_free', true)
        ])];
      }
      
      /**
       * Returns localized post meta as associative array
       * 
       * @param string $name meta field prefix
       * @return object localized post meta as associative array
       */
      protected function getMetaLocaleArray($postId, $name) {
        $fi = $this->getPostMeta($name . "_fi", true);
        $sv = $this->getPostMeta($name . "_se", true);
        $en = $this->getPostMeta($name . "_en", true);
        
        if (!empty($fi)) {
          $result['fi'] = $fi;
        }
        
        if (!empty($sv)) {
          $result['sv'] = $sv;
        }
        
        if (!empty($en)) {
          $result['en'] = $en;
        }
        
        return $result;
      }
      
      /**
       * Returns super post id
       * 
       * @return int super post id
       */
      protected function getSuperPostId() {
        return get_post_meta($this->getPostId(), 'super_event', true);
      }
      
      /**
       * Returns post meta. 
       * 
       * If value is not defined in the post and post has a super event the 
       * value is attempted to retrieve from the super event
       * 
       * @param string $name name
       * @param boolean $single single value
       * @return string meta value
       */
      protected function getPostMeta($name, $single) {
        $result = get_post_meta($this->getPostId(), $name, $single);
        
        if (!$result) {
          $superPostId = $this->getSuperPostId();
          if ($superPostId) {
            return get_post_meta($superPostId, $name, $single);
          }
        }
        
        return $result;
      }
    }
    
  }
  
  PostObjectTranslatorFactory::registerTranslator("event", EventPostObjectTranslator::class);
  
?>