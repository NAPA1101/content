<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Perspective\Geolocation\Api\Data;

interface CityInterface
{
    public function setCityRef($ref);
    public function setCityName($city_name);
    public function setCityRegions($city_regions);
    public function setCityNameUa($city_name_ua);
    public function setCityRegionsUa($city_regions_ua);
}
