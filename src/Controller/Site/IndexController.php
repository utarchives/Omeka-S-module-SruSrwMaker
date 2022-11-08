<?php
namespace SruSrwMaker\Controller\Site;

use Laminas\Mvc\Controller\AbstractActionController;
use SoapClient;
use SoapServer;
use Omeka\Module\Manager;
use SruSrwMaker\Mvc\Controller\Plugin\SearchRetrieveRequestType;
use SruSrwMaker\Mvc\Controller\Plugin\Srw;
use SruSrwMaker\Mvc\Controller\Plugin\Sru;
use SruSrwMaker\Mvc\Controller\Plugin\SearchRetrieveResponseType;
use SruSrwMaker\Mvc\Controller\Plugin\RecordType;


class IndexController extends AbstractActionController
{
    protected $hasDependencyModule = 0;
    public function __construct($omekaModules)
    {
        $resourceTree = $omekaModules->getModule('ResourceTree');
        $specialCharacterSearch = $omekaModules->getModule('SpecialCharacterSearch');
        if ((($resourceTree) &&
            (Manager::STATE_NOT_INSTALLED != $resourceTree->getState() &&
                Manager::STATE_NOT_ACTIVE != $resourceTree->getState())) &&
            ($specialCharacterSearch &&
                (Manager::STATE_NOT_INSTALLED != $specialCharacterSearch->getState() &&
                    Manager::STATE_NOT_ACTIVE != $specialCharacterSearch->getState()))) {
            $this->hasDependencyModule = 2;
        } else if ((!$resourceTree ||
            (Manager::STATE_NOT_INSTALLED == $resourceTree->getState() ||
                Manager::STATE_NOT_ACTIVE == $resourceTree->getState())) &&
            ($specialCharacterSearch &&
                (Manager::STATE_NOT_INSTALLED != $specialCharacterSearch->getState() &&
                    Manager::STATE_NOT_ACTIVE !== $specialCharacterSearch->getState()))) {
            $this->hasDependencyModule = 1;
        }
    }
    public function browseSruAction()
    {
        $sru = new Sru($this->api(), $this->hasDependencyModule);
        $result = $sru->searchRetrieveOperation($this->params());
        header('Content-type: application/xml; charset=UTF-8');
        echo $result;
        exit;
    }
    public function browseSrwAction()
    {
        $file = OMEKA_PATH . '/files/content.txt';
        $fp = fopen($file, 'w');
        fwrite($fp, $this->getRequest());
        fclose($fp);
        $wsdl = OMEKA_PATH . '/modules/SruSrwMaker/data/wsdl/srw.wsdl';
        $url = 'http://';
        if (isset($_SERVER['HTTPS'])) {
            $url = 'https://';
        }
        $option['classmap'] = ['SearchRetrieveRequestType' => SearchRetrieveRequestType::class,
            'SearchRetrieveResponseType' => SearchRetrieveResponseType::class,
            'recordType' => RecordType::class
        ];
        $option['uri'] =  $url . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $server = new SoapServer($wsdl, $option);
        $server->setClass(Srw::class);
        $server->setObject(new Srw($this->api(), $this->hasDependencyModule));
        $server->handle();
        exit;
    }
    public function browseWsdlAction()
    {
        $wsdl = OMEKA_PATH . '/modules/SruSrwMaker/data/wsdl/srw.wsdl';
        $output = file_get_contents($wsdl);
        $url = 'http://';
        if (isset($_SERVER['HTTPS'])) {
            $url = 'https://';
        }
        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $serverUrl = str_replace('wsdlsrw', 'srw', $url);
        $output = str_replace('@@@@@@@@@@@', $serverUrl, $output);
        // Emit the XML:
        header('Content-type: application/xml; charset=UTF-8');
        echo $output;
        exit;

    }
//     public function browseClientAction()
//     {
//         echo '<pre>';
//             $url = 'http://';
//             if (isset($_SERVER['HTTPS'])) {
//                 $url = 'https://';
//             }
//             $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// //             $serverUrl = str_replace('srwclient', 'srw', $url);
//             $option['classmap'] = ['SearchRetrieveRequestType' => SearchRetrieveRequestType::class,
//                 'SearchRetrieveResponseType' => SearchRetrieveResponseType::class,
//                 'recordType' => RecordType::class
//             ];
//             $option['location'] = str_replace('srwclient', 'srw', $url);
//             $option['uri'] = str_replace('srwclient', 'wsdlsrw', $url);
// //             $option['location'] = 'http://10.91.11.224/omeka/s/u-t-archive/srw';
// //             $option['uri'] = 'http://10.91.11.224/omeka/s/u-t-archive/wsdlsrw';
//             $option['trace'] = 1;
//             $client = new SoapClient(null,
//                 $option
//                 );
//             $request = new SearchRetrieveRequestType();
//             $request->version = '1.1';
//             $request->query = 'title="明治"';
//             $request->startRecord = 1;
//             $request->maximumRecords = 10;
//             $request->recordPacking = 'xml';
//             $request->resultSetTTL = 300;
//             $result = $client->searchRetrieve($request);
//             var_dump($result->records->record[0]->recordData);
//             var_dump($client->__getLastResponse());
//             exit;

//     }
}

