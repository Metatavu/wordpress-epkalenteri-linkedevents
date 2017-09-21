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
        // Sync images
        
        $superEvent = $this->getSuperEvent();
        
        $result = new \Metatavu\LinkedEvents\Model\Event([
          'id' => $this->getLinkedEventsId(),
          'location' => $this->getLocation(),
          'keywords' => $this->getKeywords(),
          'superEvent' => $superEvent,
          'superEventType' => $superEvent ? 'RECURRING' : null,
          // 'eventStatus' => $this->getEventStatus(),
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
        return new \Metatavu\LinkedEvents\Model\EventName([
          'fi' => $this.getPostMeta('event_name_fi', true),
          'sv' => $this.getPostMeta('event_name_se', true),
          'en' => $this.getPostMeta('event_name_en', true)
        ]);
      }
      
      /**
       * Returns event's short description
       * 
       * @return object short description object
       */
      private function getEventShortDescription() {
        return [
          'fi' => $this.getPostMeta('event_short_description_fi', true),
          'sv' => $this.getPostMeta('event_short_description_se', true),
          'en' => $this.getPostMeta('event_short_description_en', true)
        ];
      }
      
      /**
       * Returns event's sdescription
       * 
       * @return object description object
       */
      private function getEventDescription() {
        return [
          'fi' => $this.getPostMeta('event_description_fi', true),
          'sv' => $this.getPostMeta('event_description_se', true),
          'en' => $this.getPostMeta('event_description_en', true)
        ];
      }
      
      /**
       * Returns event start time
       * 
       * @return \DateTime event start time
       */
      private function getEventStartTime() {
        return new \DateTime($this->getPostMeta('event_start_time', true));
      }
      
      /**
       * Returns event end time
       * 
       * @return \DateTime event end time
       */
      private function getEventEndTime() {
        return new \DateTime($this->getPostMeta('event_end_time', true));
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
        
        $this->addExternalLink($result, 'Ilmoittautuminen', 'event_registration_info_url');
        $this->addExternalLink($result, 'Facebook', 'event_facebook_url');
        $this->addExternalLink($result, 'Twitter', 'event_twitter_url');
        $this->addExternalLink($result, 'YouTube', 'event_youtube_url');
        $this->addExternalLink($result, 'Instagram', 'event_instagram_url');
        
        return $result;
      }
      
      /**
       * Adds external link into result array if it existss
       * 
       * @param type $result result array
       * @param type $name link name
       * @param type $key meta key
       */
      private function addExternalLink($result, $name, $key) {
        $link = getExternalLink($name, $key);
        if ($link) {
          $result[] = $link;
        }
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
            'language' => null
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
        $postId = $this->getPostMeta('super_event', true);
        if ($postId) {
          $eventId = $this->getPostMeta('linkedevents-id', false);
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
		      'post_parent' => $this->postObject->ID,
          'post_status' => 'publish',
          'post_type' => 'event', 
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
        return $this->getMetaKeywords(['event_keywords', 'event_categories']);
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
          $locationId = $this->getPostMeta('linkedevents-id', false);
          if ($locationId) {
            return $this->getPlaceRef($locationId);
          }
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
          $locationId = $this->getPostMeta('linkedevents-id', false);
          if ($locationId) {
            return [ $this->getImageRef($locationId) ];
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
        return [new \Metatavu\LinkedEvents\Model\Offer([
          'price' => new \Metatavu\LinkedEvents\Model\OfferPrice([
            'fi' => this.getPostMeta('event_price_fi', true),
            'sv' => this.getPostMeta('event_price_se', true),
            'en' => this.getPostMeta('event_price_en', true)
          ]),
          'infoUrl' => new \Metatavu\LinkedEvents\Model\OfferInfoUrl([
            'fi' => this.getPostMeta('event_price_info_url', true)
          ]),
          'description' => new \Metatavu\LinkedEvents\Model\OfferDescription([
            'fi' => this.getPostMeta('event_registration_fi', true),
            'sv' => this.getPostMeta('event_registration_se', true),
            'en' => this.getPostMeta('event_registration_en', true)
          ]),
          'isFree' => !!this.getPostMeta('event_is_free', true) 
        ])];
      }
      
      
    }
    
  }
  
  PostObjectTranslatorFactory::registerTranslator("event", EventPostObjectTranslator::class);
  
?>