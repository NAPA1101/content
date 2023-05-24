<?php

namespace Perspective\InstantPurchase\Controller\Index;

use Magento\Framework\App\Action\Action;
class Index extends Action
{
    /**
     * [execute description]
     * @return [type] [description]
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
