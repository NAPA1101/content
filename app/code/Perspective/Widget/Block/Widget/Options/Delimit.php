<?php

namespace Perspective\Widget\Block\Widget\Options;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Delimit extends Template implements BlockInterface
{
    protected $_template = "Perspective_Widget::widget/delimiter.phtml";

    public function getTabNumber()
    {
        $element = $this->getData('element');
        $id = $element->getName();
        $tabNumbers = ['First', 'Second', 'Third'];

        if ($id === 'parameters[delimiter1]') {
            $tabNumber = $tabNumbers[0];
        } elseif ($id === 'parameters[delimiter2]') {
            $tabNumber = $tabNumbers[1];
        } elseif ($id === 'parameters[delimiter3]') {
            $tabNumber = $tabNumbers[2];
        } else {
            return "Unknown";
        }

        return $tabNumber;
    }
}
