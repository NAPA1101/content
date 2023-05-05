<?php
namespace Perspective\CustomShipping\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\CustomShipping\Helper\Data;
class Test implements ArgumentInterface
{

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper,

    ) {

        $this->helper = $helper;
    }

    public function getTest() {
        return explode(',', $this->helper->getGeneralConfig('multiselect_field'));
    }
}
