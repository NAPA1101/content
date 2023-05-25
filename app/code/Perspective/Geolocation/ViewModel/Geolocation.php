<?php
namespace Perspective\Geolocation\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Perspective\Geolocation\Helper\ModuleStatus;
use Magento\Framework\Locale\Resolver;
use Perspective\Geolocation\Model\ResourceModel\City\CollectionFactory;

class Geolocation implements ArgumentInterface
{
    /**
     * @var Curl
     */
    public Curl $curl;
    /**
     * @var ModuleStatus
     */
    public ModuleStatus $moduleStatus;
    /**
     * @var RemoteAddress
     */
    public RemoteAddress $remoteAddress;
    /**
     * @var Repository
     */
    private Repository $repository;
    /**
     * @var Resolver
     */
    private Resolver $store;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param Repository $repository
     * @param Curl $curl
     * @param ModuleStatus $moduleStatus
     * @param RemoteAddress $remoteAddress
     * @param Resolver $store
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Repository            $repository,
        Curl                  $curl,
        ModuleStatus          $moduleStatus,
        RemoteAddress         $remoteAddress,
        Resolver              $store,
        CollectionFactory     $collectionFactory,
    )
    {
        $this->moduleStatus = $moduleStatus;
        $this->curl = $curl;
        $this->remoteAddress = $remoteAddress;
        $this->repository = $repository;
        $this->store = $store;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return string
     */
    public function getLocationImageUrl(): string
    {
        $url = $this->repository->getUrl('Perspective_Geolocation/media/location.png');
        return $url;
    }

//    public function getCityList() {
//        $cityList = $this->collectionFactory->create();
//        return count($cityList->getData());
//    }
    /**
     * @param $param
     * @return mixed
     */
    public function getIpStackApi($param)
    {
        $url = 'http://api.ipstack.com/';
        $ipUser = $this->remoteAddress->getRemoteAddress();
        if ($ipUser == '127.0.0.1') $ipUser = '37.55.144.3';
        $ipApi = $this->moduleStatus->getModuleApi('GeolocationTabApiKey');
        $this->curl->get($url . $ipUser . '?access_key=' . $ipApi);
        $responseBody = $this->curl->getBody();
        $location = json_decode($responseBody, true);
        return $location[$param];
    }
}
