<?php

namespace Perspective\Holidays\Plugin;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Perspective\Holidays\Helper\Data;
use Perspective\Holidays\Model\ResourceModel\Holidays\CollectionFactory;
use Perspective\Holidays\ViewModel\Holidays;
class Product
{
    private CollectionFactory $collectionFactory;
    private Data $helper;
    private DateTime $date;
    protected Holidays $viewModel;

    public function __construct(
        CollectionFactory $collectionFactory,
        Holidays $viewModel,
        DateTime $date,
        Data $helper
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->date = $date;
        $this->viewModel = $viewModel;
    }


    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $collection = $this->collectionFactory->create();
        $discount = 0;
        if ($this->helper->getHolidayModule('enable')) {
            $customPrice = $result;
            foreach ($collection as $itemColl) {
                if ($discount <= $itemColl->getData('discount')) {
                    $discount = $itemColl->getData('discount');
                }
                if ($itemColl->getData('start_holiday') <= $this->date->gmtDate()
                    && $itemColl->getData('end_holiday') >= $this->date->gmtDate()
                    && $itemColl->getData('status') == 1) {
                    $customPrice = $result * ((100 - $discount) / 100);
                }
            }
            return $customPrice;
        } else {
            return $result;
        }
    }
}
