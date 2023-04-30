<?php
namespace Perspective\Holidays\Controller\Adminhtml\Holidays;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Perspective\Holidays\Model\Holidays;

class Save extends Action
{

    /**
     * @var Holidays
     */
    protected $Holidaysmodel;

    /**
     * @var Session
     */
    protected $adminsession;

    /**
     * @param Action\Context $context
     * @param Holidays       $Holidaysmodel
     * @param Session        $adminsession
     */
    public function __construct(
        Action\Context $context,
        Holidays $Holidaysmodel,
        Session $adminsession
    ) {
        parent::__construct($context);
        $this->holidaysmodel = $Holidaysmodel;
        $this->adminsession = $adminsession;
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
            $entity_id = $this->getRequest()->getParam('entity_id');
            if ($entity_id) {
                $this->holidaysmodel->load($entity_id);
            }

            $this->holidaysmodel->setData($data);

            try {
                $this->holidaysmodel->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->Holidaysmodel->getEntityId(), '_current' => true]);
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
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
