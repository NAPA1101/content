<?php
namespace Perspective\Geolocation\Controller\Adminhtml\City;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Result\PageFactory;
use Perspective\Geolocation\Api\CityRepositoryInterface;
use Perspective\Geolocation\Api\NovaPoshtaApi2Factory;
use Perspective\Geolocation\Helper\ModuleStatus;
use Perspective\Geolocation\Model\CityFactory;

class Sync extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;
    /**
     * @var CityFactory
     */
    private $cityFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var NovaPoshtaApi2Factory
     */
    private NovaPoshtaApi2Factory $novaPoshtaApi2Factory;
    /**
     * @var ModuleStatus
     */
    private ModuleStatus $moduleStatus;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CityRepositoryInterface $cityRepository
     * @param NovaPoshtaApi2Factory $novaPoshtaApi2Factory
     * @param ModuleStatus $moduleStatus
     * @param CityFactory $cityFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context                 $context,
        PageFactory             $resultPageFactory,
        CityRepositoryInterface $cityRepository,
        NovaPoshtaApi2Factory   $novaPoshtaApi2Factory,
        ModuleStatus            $moduleStatus,
        CityFactory             $cityFactory,
        SearchCriteriaBuilder   $searchCriteriaBuilder,
        ScopeConfigInterface    $scopeConfig,
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->cityRepository = $cityRepository;
        $this->cityFactory = $cityFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->novaPoshtaApi2Factory = $novaPoshtaApi2Factory;
        $this->moduleStatus = $moduleStatus;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $pageCity = $this->_getCitiesFromServer()->request(model: "Address", method: "getSettlements");
        $pageCityObject = json_decode($pageCity);
        $totalCount = $pageCityObject->info->totalCount;
        $totalPages = ceil($totalCount / 150);
        if (property_exists($pageCityObject, 'success') && $pageCityObject->success === true) {
            for ($i = 1; $i <= $totalPages; $i++) {
                $pageCitiesFromServer = $this->_getCitiesFromServer()->request(model: "Address", method: "getSettlements", params: ["Page" => $i]);
                $pageCitiesFromServer = json_decode($pageCitiesFromServer);
                $pageCitiesFromServer = $pageCitiesFromServer->data;
                $currentCitiesRef = $this->_getCitiesIdArray();
                foreach ($pageCitiesFromServer as $key => $cityApi) {
                    $cityApiRef = $cityApi->Ref;
                    if (isset($currentCitiesRef[$cityApiRef])) {
                        continue;
                    } else {
                        $this->_addNewCity($cityApi);
                    }
                }
            }
            $this->messageManager->addSuccess(
                __('Успешно синхронизировано')
            );
        } else {
            $this->messageManager->addError(
                __('Новая почта не отвечет или отвечает не правльно')
            );
        }
        $this->_redirect('novaposhta/city/index');
    }

    /**
     * @return \Perspective\Geolocation\Api\NovaPoshtaApi2
     */
    public function _getCitiesFromServer()
    {
        $npApi = $this->moduleStatus->getModuleApi('GeolocationNpApiKey');
        $np = $this->novaPoshtaApi2Factory->create(['key' => $npApi]);
        return $np;
    }

    /**
     * @return array
     */
    private function _getCitiesIdArray()
    {
        $citiesCollection = $this->_getCitiesCollection();
        $indexesArray = [];
        foreach ($citiesCollection as $key => $city_model) {
            $indexesArray[$city_model->getCityRef()] = '';
        }
        return $indexesArray;
    }

    /**
     * @return mixed
     */
    protected function _getCitiesCollection()
    {
        return $this->cityRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * @param $cityApi
     * @return void
     */
    private function _addNewCity($cityApi)
    {
        $modelCity = $this->cityFactory->create();
        $modelCity->setCityRef($cityApi->Ref);
        $modelCity->setCityName($cityApi->DescriptionTranslit);
        $modelCity->setCityRegions($cityApi->RegionsDescriptionTranslit);
        $modelCity->setCityNameUa($cityApi->Description);
        $modelCity->setCityRegionsUa($cityApi->RegionsDescription);
        $this->cityRepository->save($modelCity);
    }
}
