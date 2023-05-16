<?php

namespace Perspective\Geolocation\Model\ResourceModel;

class City extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('nova_poshta_city', 'entity_id');
    }
}
