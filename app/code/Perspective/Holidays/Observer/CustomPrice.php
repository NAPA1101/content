<?php
namespace Perspective\Holidays\Observer;
////////////////////////////////     not used //////////////////////////////////////////////////
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Perspective\Holidays\Helper\Data;
use Perspective\Holidays\Model\ResourceModel\Holidays\CollectionFactory;

class CustomPrice implements ObserverInterface
{
    protected $viewModel;

    protected $holidaysFactory;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    protected Data $helper;
    private DateTime $date;

    /**
     * @param CollectionFactory $collectionFactory
     * @param DateTime $date
     * @param Data $helper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        DateTime $date,
        Data $helper
    )
    {

        $this->helper = $helper;
        $this->date = $date;
        $this->collectionFactory = $collectionFactory;
    }
    public function execute(Observer $observer): void
    {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $collection = $this->collectionFactory->create();
        $price = $item->getPrice();
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $discount = 0;
        if ($this->helper->getHolidayModule('enable')) {
            foreach ($collection as $itemColl) {
                if ($discount <= $itemColl->getData('discount')) {
                    $discount = $itemColl->getData('discount');
                }
                if ($itemColl->getData('start_holiday') <= $this->date->gmtDate()
                    && $itemColl->getData('end_holiday') >= $this->date->gmtDate()
                    && $itemColl->getData('status') == 1)
                {
                    $customPrice = $price * ((100 - $discount) / 100);
                    $item->setCustomPrice($customPrice);
                    $item->setOriginalCustomPrice($customPrice);
                    $item->getProduct()->setIsSuperMode(true);
                    break;
                }
            }
        } else {
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}
