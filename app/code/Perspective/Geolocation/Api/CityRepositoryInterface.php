<?php

namespace Perspective\Geolocation\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CityRepositoryInterface
{

    public function save(Data\CityInterface $request);

    public function getList(SearchCriteriaInterface $searchCriteria);
}
