<?php
namespace Perspective\Holidays\Controller\Adminhtml\Holidays;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Perspective\Holidays\Model\Holidays;

class Delete extends Action
{
    protected $modelHolidays;
    public function __construct(
        Context $context,
        Holidays $holidaysFactory
    ) {
        parent::__construct($context);
        $this->modelHolidays = $holidaysFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Perspective_Holidays::index_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelHolidays;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Record deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Record does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }
}
