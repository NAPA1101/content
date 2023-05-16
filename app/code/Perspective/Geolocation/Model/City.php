<?php

namespace Perspective\Geolocation\Model;

use Perspective\Geolocation\Api\Data\CityInterface;

class City extends \Magento\Framework\Model\AbstractExtensibleModel implements CityInterface
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Perspective\Geolocation\Model\ResourceModel\City');
    }

    public function getCustomAttributesCodes()
    {
        return array('ref', 'city_name', 'city_regions', 'city_name_ua', 'city_regions_ua');
    }

    public function setCityRef($ref)
    {
        return $this->setData('ref', $ref);
    }

    public function getCityRef()
    {
        return $this->_getData('ref');
    }
    public function setCityName($city_name)
    {
        return $this->setData('city_name', $city_name);
    }

    public function getCityName()
    {
        return $this->_getData('city_name');
    }

    public function setCityRegions($city_regions)
    {
        return $this->setData('city_regions', $city_regions);
    }

    public function getCityRegions()
    {
        return $this->_getData('city_regions');
    }

    public function setCityNameUa($city_name_ua)
    {
        return $this->setData('city_name_ua', $city_name_ua);
    }

    public function getCityNameUa()
    {
        return $this->_getData('city_name_ua');
    }

    public function setCityRegionsUa($city_regions_ua)
    {
        return $this->setData('city_regions_ua', $city_regions_ua);
    }

    public function getCityRegionsUa()
    {
        return $this->_getData('city_regions_ua');
    }
}
