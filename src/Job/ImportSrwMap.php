<?php
namespace SruSrwMaker\Job;

use SruSrwMaker\CsvFile;
use Omeka\Job\AbstractJob;

class ImportSrwMap extends AbstractJob
{
    protected $api;

    protected $addedCount;

    protected $logger;

    protected $hasErr = false;
    public function perform()
    {
        ini_set("auto_detect_line_endings", true);
        $this->logger = $this->getServiceLocator()->get('Omeka\Logger');
        $this->api = $this->getServiceLocator()->get('Omeka\ApiManager');
        $config = $this->getServiceLocator()->get('Config');
        $csvFile = new CsvFile($this->getServiceLocator());
        $csvFile->setTempPath($this->getArg('csvpath'));
        $csvFile->loadFromTempPath();
        $this->api->delete('srw_maps', []);
        foreach ($csvFile->fileObject as $index => $row) {
            //skip the first (header) row, and any blank ones
            if ($index == 0 || empty($row)) {
                continue;
            }
            $rdfMap = [
                'local_property' => $row[0],
                'standard_property' => $row[1],
            ];
            $this->api->create('srw_maps', $rdfMap);
        }
    }

}

