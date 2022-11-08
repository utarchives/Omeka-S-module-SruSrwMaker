<?php
namespace SruSrwMaker\Mvc\Controller\Plugin;

use SimpleXMLElement;

class Sru extends SrwBase
{
    public function searchRetrieveOperation ($SearchRetrieveOperationRequest) {
        $query = urldecode($SearchRetrieveOperationRequest->fromQuery('query'));
        $sortKeys = urldecode($SearchRetrieveOperationRequest->fromQuery('sortKeys'));
        $startRecord = urldecode($SearchRetrieveOperationRequest->fromQuery('startRecord'));
        $maximumRecords = urldecode($SearchRetrieveOperationRequest->fromQuery('maximumRecords'));
        $params = $this->createParameter($query, $startRecord, $maximumRecords);
        $response = $this->api->search($this->targetAdapter, $params);
        $items = $response->getContent();
        $numberOfRecords = $response->getTotalResults();
        $rootNode = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF8" standalone="yes"?><searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/"></searchRetrieveResponse>' );
        $rootNode->addChild('version', '1.1');
        $rootNode->addChild('numberOfRecords', $numberOfRecords);
        $recordsNode = $rootNode->addChild('records');
        $result =  $this->convert2Xml($items, $numberOfRecords, $recordsNode);
        return $rootNode->asXML();
    }
    protected function convert2Xml($items, $numberOfRecords, $recordsNode, $version = '1.1', $recordPacking = 'xml')
    {
        $i = 1;
        foreach ($items as $item) {
            $recordNode = $recordsNode->addChild('record');
            $recordNode->addChild('recordSchema', 'info:srw/schema/1/dc-v1.1');
            $recordNode->addChild('recordPacking', 'xml');
            $recordDataNode = $recordNode->addChild('recordData');
            $schemaNode = $recordDataNode->addChild('dc:dc', '', 'http://purl.org/dc/elements/1.1/');
            //             $schemaNode->addAttribute('xsi:schemaLocation', 'info:srw/schema/1/dc-v1.1', 'http://www.loc.gov/zing/srw/dcschema/v1.0/');
//             $schemaNode->addAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
            //             foreach($item->values() as $term => $values) {
            //                 foreach ($item->values()[$term]['values'] as $value) {
            //                     $schemaNode->addChild($term, $value->value());
            //                 }
            //             }
            $schemaNode = $this->setValue($item, $schemaNode);
            $recordNode->addChild('recordPosition', $i);
            $i++;
        }
        return $recordsNode;
    }
}

