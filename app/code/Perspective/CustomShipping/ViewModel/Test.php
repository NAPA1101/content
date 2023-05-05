<?php
namespace Perspective\CustomShipping\ViewModel;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\CustomShipping\Helper\Data;
class Test implements ArgumentInterface
{
    private CollectionFactory $collectionFactory;
    private Data $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        CollectionFactory $collectionFactory,
    ) {
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
    }

    public function getTest() {
        return explode(',', $this->helper->getGeneralConfig('multiselect_field'));
    }

    public function getCustomerDOB() {
        $collections = $this->collectionFactory->create();
        $collections->addAttributeToSelect('dob', date('Y-m-d'));
    }
}
