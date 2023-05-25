<?php
namespace Perspective\InstantPurchase\Controller\Index;

use Magento\Framework\App\Action\Context;
use Perspective\InstantPurchase\Model\PurchaseFactory;
class Save extends \Magento\Framework\App\Action\Action
{

    protected $purchase;

    public function __construct(
        Context         $context,
        PurchaseFactory $purchase
    )
    {
        $this->purchase = $purchase;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $purchase = $this->purchase->create();
        $purchase->setData($data);
        if ($purchase->save()) {
            $this->messageManager->addSuccessMessage(__('You saved review'));
        } else {
            $this->messageManager->addErrorMessage(__('Review was not saved.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('purchase/index/index');
        return $resultRedirect;
    }
}

