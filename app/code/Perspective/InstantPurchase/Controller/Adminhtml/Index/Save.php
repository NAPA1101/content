<?php
namespace Perspective\InstantPurchase\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Perspective\InstantPurchase\Model\Purchase;

class Save extends Action
{

    /**
     * @var Session
     */
    protected $adminsession;
    /**
     * @var Purchase
     */
    private Purchase $purchase;

    /**
     * @param Action\Context $context
     * @param Purchase $purchase
     * @param Session $adminsession
     */
    public function __construct(
        Action\Context $context,
        Purchase $purchase,
        Session $adminsession
    ) {
        parent::__construct($context);
        $this->adminsession = $adminsession;
        $this->purchase = $purchase;
    }

    /**
     * Save Holidays record action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->purchase->load($id);
            }

            $this->purchase->setData($data);

            try {
                $this->purchase->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath('*/*/edit', ['id' => $this->purchase->getId(), '_current' => true]);
                    }
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $resultRedirect->setPath('*/*/');
//        $data = $this->getRequest()->getParams();
//        $purchase = $this->purchase->create();
//        $purchase->setData($data);
//        if ($purchase->save()) {
//            $this->messageManager->addSuccessMessage(__('You saved review'));
//        } else {
//            $this->messageManager->addErrorMessage(__('Review was not saved.'));
//        }
//        $resultRedirect = $this->resultRedirectFactory->create();
//        $resultRedirect->setPath('purchase/index/index');
//        return $resultRedirect;
    }
}
