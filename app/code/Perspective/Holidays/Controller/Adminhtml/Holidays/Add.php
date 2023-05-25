<?php
namespace Perspective\Holidays\Controller\Adminhtml\Holidays;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class Add extends Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Add New Holiday'));
        return $resultPage;
    }
}
