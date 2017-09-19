<?php
/**
 * Offer
 *
 * PHP version 5
 *
 * @category Class
 * @package  Metatavu\LinkedEvents
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Linked Events information API
 *
 * Linked Events provides categorized data on events and places using JSON-LD format.  Events can be searched by date and location. Location can be exact address or larger area such as neighbourhood or borough  JSON-LD format is streamlined using include mechanism. API users can request that certain fields are included directly into the result, instead of being hyperlinks to objects.  Several fields are multilingual. These are implemented as object with each language variant as property. In this specification each multilingual field has (fi,sv,en) property triplet as example.
 *
 * OpenAPI spec version: v1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Metatavu\LinkedEvents\Model;

use \ArrayAccess;

/**
 * Offer Class Doc Comment
 *
 * @category    Class
 * @description information record for this event. The prices are not in a structured format and the format depends on information source. An exception to this is the case of free event. These are indicated using is_free flag, which is searchable.
 * @package     Metatavu\LinkedEvents
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Offer implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'offer';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'price' => '\Metatavu\LinkedEvents\Model\OfferPrice',
        'infoUrl' => '\Metatavu\LinkedEvents\Model\OfferInfoUrl',
        'description' => '\Metatavu\LinkedEvents\Model\OfferDescription',
        'isFree' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerFormats = [
        'price' => null,
        'infoUrl' => null,
        'description' => null,
        'isFree' => null
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'price' => 'price',
        'infoUrl' => 'info_url',
        'description' => 'description',
        'isFree' => 'is_free'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'price' => 'setPrice',
        'infoUrl' => 'setInfoUrl',
        'description' => 'setDescription',
        'isFree' => 'setIsFree'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'price' => 'getPrice',
        'infoUrl' => 'getInfoUrl',
        'description' => 'getDescription',
        'isFree' => 'getIsFree'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['price'] = isset($data['price']) ? $data['price'] : null;
        $this->container['infoUrl'] = isset($data['infoUrl']) ? $data['infoUrl'] : null;
        $this->container['description'] = isset($data['description']) ? $data['description'] : null;
        $this->container['isFree'] = isset($data['isFree']) ? $data['isFree'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {

        return true;
    }


    /**
     * Gets price
     * @return \Metatavu\LinkedEvents\Model\OfferPrice
     */
    public function getPrice()
    {
        return $this->container['price'];
    }

    /**
     * Sets price
     * @param \Metatavu\LinkedEvents\Model\OfferPrice $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->container['price'] = $price;

        return $this;
    }

    /**
     * Gets infoUrl
     * @return \Metatavu\LinkedEvents\Model\OfferInfoUrl
     */
    public function getInfoUrl()
    {
        return $this->container['infoUrl'];
    }

    /**
     * Sets infoUrl
     * @param \Metatavu\LinkedEvents\Model\OfferInfoUrl $infoUrl
     * @return $this
     */
    public function setInfoUrl($infoUrl)
    {
        $this->container['infoUrl'] = $infoUrl;

        return $this;
    }

    /**
     * Gets description
     * @return \Metatavu\LinkedEvents\Model\OfferDescription
     */
    public function getDescription()
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     * @param \Metatavu\LinkedEvents\Model\OfferDescription $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets isFree
     * @return bool
     */
    public function getIsFree()
    {
        return $this->container['isFree'];
    }

    /**
     * Sets isFree
     * @param bool $isFree Whether the event is of free admission
     * @return $this
     */
    public function setIsFree($isFree)
    {
        $this->container['isFree'] = $isFree;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\Metatavu\LinkedEvents\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\Metatavu\LinkedEvents\ObjectSerializer::sanitizeForSerialization($this));
    }
}


