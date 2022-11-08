<?php
namespace SruSrwMaker\Mvc\Controller\Plugin;

use SimpleXMLElement;

class Srw extends SrwBase
{
    protected $version;
    protected $numberOfRecords;

//     public function searchRetrieve($version, $query, $startRecord, $maximumRecords, $recordPacking, $resultSetTTL)
    public function searchRetrieve($request)
    {
        $query = $request->query;
        $startRecord = $request->startRecord;
        $maximumRecords = $request->maximumRecords;
        $version = $request->version;
        $recordPacking = $request->recordPacking;
        $params = $this->createParameter($query, $startRecord, $maximumRecords);
        $response = $this->api->search($this->targetAdapter, $params);
        $items = $response->getContent();
        $numberOfRecords = $response->getTotalResults();
        $recordsNode = new SimpleXMLElement('<records></records>');
        $records =  $this->convert2ReturnValue($items, $numberOfRecords, $recordsNode, $version, $recordPacking);
        $response = new SearchRetrieveResponseType();
        $response->records = $records;
        $response->version = $version;
        $response->numberOfRecords = $numberOfRecords;
        $response->resultSetId = date('ymdHis');
        $response->resultSetIdleTime = 100;
        return $response;
    }

    protected function convert2ReturnValue($items, $recordPacking = 'xml')
    {
        $i = 0;
        $records = [];
        foreach ($items as $item) {
            $recordType = new RecordType();
//             $records['record'][$i]['recordSchema'] = 'info:srw/schema/1/dc-v1.1';
//             $records['record'][$i]['recordPacking'] = 'xml';
            $schemaNode = new SimpleXMLElement('<?xml version="1.0" encoding="UTF8" standalone="yes"?><dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/"></dc:dc>');
            $schemaNode = $this->setValue($item, $schemaNode);
            $dataPart = $schemaNode->asXml();
            $dataPart = str_replace('<?xml version="1.0" encoding="UTF8" standalone="yes"?>', '', $dataPart);
            $recordType->recordSchema = 'info:srw/schema/1/dc-v1.1';
            $recordType->recordPacking = 'xml';
            $recordType->recordData = $dataPart;
//             $records['record'][$i]['recordData'] = $dataPart;
            $records['record'][$i] = $recordType;
            $i++;
        }
        return $records;
    }

}


