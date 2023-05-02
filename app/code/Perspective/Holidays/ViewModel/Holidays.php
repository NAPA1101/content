<?php
namespace Perspective\Holidays\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Perspective\Holidays\Model\ResourceModel\Holidays\CollectionFactory;
use Perspective\Holidays\Helper\Data;
use Magento\Framework\Registry;

class Holidays implements ArgumentInterface
{
    protected $holidaysFactory;

    protected $product;
    private DateTime $date;
    private Registry $registry;
    private Data $helper;
    private CollectionFactory $collectionFactory;

    /**
     * @param DateTime $date
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param Data $helper
     */
    public function __construct(
        DateTime $date,
        CollectionFactory $collectionFactory,
        Registry $registry,
        Data $helper,
    ) {
        $this->date = $date;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getPrice()
    {
        return $this->getProduct()->getFinalPrice();
    }
    public function getHolidays()
    {
        $collection = $this->collectionFactory->create();
        $discount = 0;
        if ($this->helper->getHolidayModule('enable')) {
            foreach ($collection as $item) {
                if ($item->getData('start_holiday') <= $this->date->gmtDate()
                    && $item->getData('end_holiday') >= $this->date->gmtDate()
                    && $item->getData('status') == 1) {
                    echo "Name: " . $item->getData('holiday_name') . "<br>" .
                        " Text: " . $item->getData('holiday_text') .
                        " Discount: " . $item->getData('discount');

                }
            }
        } else {
            return null;
        }
    }
}
