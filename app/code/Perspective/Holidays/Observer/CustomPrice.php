<?php
namespace Perspective\Holidays\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Perspective\Holidays\ViewModel\Holidays;

class CustomPrice implements ObserverInterface
{
    protected $viewModel;
    public function __construct(
        Holidays $viewModel
    )
    {
        $this->viewModel = $viewModel;
    }

    /**
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws CouldNotSaveException
     * @throws InputException
     */
    public function execute(Observer $observer): void
    {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $discount = $this->viewModel->getHolidays();
        //(optional) get the parent item, if exists
        $price = $item->getPrice();

    }
}
