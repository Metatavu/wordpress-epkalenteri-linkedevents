<?php

  namespace Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  require_once( __DIR__ . '/abstract-handler.php');
  require_once( __DIR__ . '/../translation/translation.php');
  
  if (!class_exists( '\Metatavu\LinkedEvents\Wordpress\EPKalenteri\Handlers\PlaceHandler' ) ) {
  
    class PlaceHandler extends AbstractHandler {
      
      private $filterApi;
      
      public function __construct() {
        parent::__construct('place', 'acf/save_post');
        $this->filterApi = \Metatavu\LinkedEvents\Wordpress\EPKalenteri\Api::getFilterApi();
      }

      private function coordinateTransform($coordinates) {
        $lat = $coordinates[0];
        $long = $coordinates[1];
        $latlong = new \Geodetic\LatLong(
          new \Geodetic\LatLong\CoordinateValues(
            $lat,
            $long,
            \Geodetic\Angle::DEGREES,
            0.0,
            \Geodetic\Distance::METRES));
        $datum = new \Geodetic\Datum(\Geodetic\Datum::WGS84);
        $utm = $latlong->toUTM($datum);

        return [$utm->getEasting(), $utm->getNorthing()];
      }
      
      public function updateResource($postId, $resource) {
        $resource->getPosition()->setCoordinates($this->coordinateTransform($resource->getPosition()->getCoordinates()));
        $this->filterApi->placeUpdate($resource->getId(), $resource);
      }
      
      public function createResource($postId, $resource) {
        $resource->getPosition()->setCoordinates($this->coordinateTransform($resource->getPosition()->getCoordinates()));
        $created = $this->filterApi->placeCreate($resource);
        $this->setLinkedEventsId($postId, $created->getId());
      }

    }
    
  }
  
  new PlaceHandler();
  
?>