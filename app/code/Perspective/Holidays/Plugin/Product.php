<?php

namespace Perspective\Holidays\Plugin;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Perspective\Holidays\Helper\Data;
use Perspective\Holidays\Model\ResourceModel\Holidays\CollectionFactory;
use Perspective\Holidays\ViewModel\Holidays;
class Product
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;
    /**
     * @var Data
     */
    private Data $helper;
    /**
     * @var DateTime
     */
    private DateTime $date;
    /**
     * @var Holidays
     */
    protected Holidays $viewModel;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Holidays $viewModel
     * @param DateTime $date
     * @param Data $helper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Holidays $viewModel,
        DateTime $date,
        Data $helper,
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->date = $date;
        $this->viewModel = $viewModel;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return float|int|mixed
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $product = $subject;
        $discount = 0;
        if ($this->helper->getHolidayModule('enable')) {
            $customPrice = $result;
                if ($product->getData('holidays_datapatch') == 1) {
                    $collection = $this->collectionFactory->create();
                    foreach ($collection as $itemColl) {
                        if ($discount <= $itemColl->getData('discount')) {
                            $discount = $itemColl->getData('discount');
                            if ($itemColl->getData('start_holiday') <= $this->date->gmtDate()
                                && $itemColl->getData('end_holiday') >= $this->date->gmtDate()
                                && $itemColl->getData('status') == 1) {
                                $customPrice = $result * ((100 - $discount) / 100);
                            }
                        }
                    }
                    return $customPrice;
                } else {
                    return $result;
                }
        } else {
            return $result;
        }
    }
}
