<?php
namespace Perspective\Holidays\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Perspective\Holidays\Model\ResourceModel\Holidays\CollectionFactory;
use Perspective\Holidays\Helper\Data;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;

class Holidays implements ArgumentInterface
{

    /**
     * @var Product
     */
    protected $product;
    /**
     * @var DateTime
     */
    private DateTime $date;
    /**
     * @var Data
     */
    private Data $helper;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;


    /**
     * @param DateTime $date
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param Data $helper
     * @param Product $product
     */
    public function __construct(
        DateTime $date,
        CollectionFactory $collectionFactory,
        Registry $registry,
        Data $helper,
        Product $product,

    ) {
        $this->date = $date;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        if ($this->helper->getHolidayModule('enable')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getMaxDiscount(): int
    {
        $collection = $this->collectionFactory->create();
        $discount = 0;
        foreach ($collection as $item) {
            if ($discount <= $item->getData('discount')) {
                $discount = $item->getData('discount');
            }
        }
        return $discount;
    }

    /**
     * @param $massNumber
     * @return mixed|null
     */
    public function getHolidays($massNumber): mixed
    {
        $mass = [];
        $collection = $this->collectionFactory->create();
        if ($this->helper->getHolidayModule('enable')) {
            foreach ($collection as $itemColl) {
                    if ($itemColl->getData('start_holiday') <= $this->date->gmtDate()
                        && $itemColl->getData('end_holiday') >= $this->date->gmtDate()
                        && $itemColl->getData('status') == 1 && $this->getMaxDiscount() == $itemColl->getData('discount')) {
                        $mass[] = $itemColl->getData('holiday_name');
                        $mass[] = $itemColl->getData('holiday_text');
                        $mass[] = $itemColl->getData('discount');
                        return $mass[$massNumber];
                    }
                }
            }
        return null;
    }
}
