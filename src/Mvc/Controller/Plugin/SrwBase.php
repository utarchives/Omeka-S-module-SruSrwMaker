<?php
namespace SruSrwMaker\Mvc\Controller\Plugin;

class SrwBase
{
    protected $api;
    protected $targetAdapter = 'items';
    public function __construct($api, $hasDependencyModule) {
        $this->api = $api;
        if ($hasDependencyModule == 1) {
            $this->targetAdapter = 'search_value_items';
        } else if ($hasDependencyModule == 2) {
            $this->targetAdapter = 'search_items';
        }
    }
    protected $termKey = [];
    protected $joinerKey = [];
    protected $compareKey = [];
    protected function createParameter($query, $startRecord, $maximumRecords)
    {
        $sortParam = '';
        $searchParam = $query;
        $queryCue = explode('sortBy', $query);
        if(count($queryCue) > 1) {
            $searchParam = $queryCue[0];
            $sortParam = $queryCue[1];
        }
        $this->termKey['cql.anywhere'] = 'search';
        $this->termKey['title'] = $this->getLocalProperty('title');
        $this->termKey['creator'] = $this->getLocalProperty('creator');
        $this->termKey['publisher'] = $this->getLocalProperty('publisher');
        $this->termKey['date'] = $this->getLocalProperty('date');
        $this->compareKey['equal'] = '=';
        $this->compareKey['equal2'] = ' = ';
        $this->compareKey['all'] = ' all ';
        $this->compareKey['any'] = ' any ';
        $this->joinerKey['and'] = 'and';
        $this->joinerKey['or'] = 'or';
        $this->joinerKey['not'] = 'not';
        //         var_dump($query);
        //         var_dump(urldecode('resource_class_id=&property%5B0%5D%5Bjoiner%5D=and&property%5B0%5D%5Bproperty%5D=&property%5B0%5D%5Btype%5D=in&property%5B0%5D%5Btext%5D=慶応&property%5B1%5D%5Bjoiner%5D=and&property%5B1%5D%5Bproperty%5D=1&property%5B1%5D%5Btype%5D=in&property%5B1%5D%5Btext%5D=三&site_id=&item_set_id%5B%5D=&submit=Search'));
        $params = [];
        $index = 0;
        $value = '';
        $inJob = false;
        $creatingParam = false;
        $checkCompareKey = false;
        $compareKey = '';
        $key = null;
        $type = 'in';
        $joiner = 'and';
        for ($i = 0; $i < strlen($searchParam); $i++) {
            $char = substr($searchParam, $i, 1);
            $value .= $char;
            if (!$inJob) {
                if ($index > 0) {
                    $joinerKey = $this->getJoinerKey($value);
                    if ($joinerKey) {
                        if (strcmp($joinerKey, 'not') == 0) {
                            $joiner = 'and';
                            $type = 'neq';
                        } else {
                            $joiner = $joinerKey;
                            $type = 'in';
                        }
                        $value = '';
                    }
                }
                $termKey = $this->getTermKey(trim($value));
                if (!$termKey) {
                    continue;
                }
            }
            $inJob = true;
            if (!$creatingParam) {
                if (strcmp('search', $termKey) != 0) {
                    $params['property'][$index]['property'] = $this->getPropertyId($termKey);
                } else {
                    $params['property'][$index]['property'] = null;
                }
                $creatingParam = true;
                $value = '';
            }
            if (!$checkCompareKey) {
                $compareKey = $this->getCompareKey($value);
                if ($compareKey) {
                    $checkCompareKey = true;
                    $value = '';
                } else {
                    continue;
                }
            }
            // if last char is space...
            if (strcmp($compareKey, $this->compareKey['equal']) == 0) {
                if (strcmp(substr(trim($value), 0, 1), '"') ==  0) {
                    $checkValue = trim($value);
                    if (strcmp(substr($checkValue, strlen($checkValue) - 1, 1), '"') == 0 && strlen($checkValue) > 1) {
                        $params['property'][$index]['type'] = $type;
                        $params['property'][$index]['joiner'] = $joiner;
                        $params['property'][$index]['text'] = str_replace('"', '', $checkValue);
                        $value = '';
                        $inJob = false;
                        $creatingParam = false;
                        $checkCompareKey = false;
                        $index++;
                    } else {
                        continue;
                    }
                } else if (strcmp(substr($value, strlen($value) - 1), ' ') == 0 || $i == strlen($searchParam) -1) {
                    $params['property'][$index]['type'] = $type;
                    $params['property'][$index]['joiner'] = $joiner;
                    $params['property'][$index]['text'] = str_replace('"', '', $value);
                    $value = '';
                    $inJob = false;
                    $creatingParam = false;
                    $checkCompareKey = false;
                    $index++;
                }
            } else {
                $checkValue = trim($value);
                if (strcmp(substr($checkValue, 0, 1), '"') ==  0 &&
                    strcmp(substr($checkValue, strlen($checkValue) - 1, 1), '"') == 0 && strlen($checkValue) > 1) {
                    $checkValue = str_replace('"', '', $checkValue);
//                     $checkValue = str_replace('　', ' ', $checkValue);
                    $singleValues = explode(' ', $checkValue);
                    $property = $params['property'][$index]['property'];
                    foreach ($singleValues as $singleValue) {
                        $params['property'][$index]['property'] = $property;
                        $params['property'][$index]['type'] = $type;
                        $params['property'][$index]['joiner'] = $joiner;
                        $params['property'][$index]['text'] = $singleValue;
                        $index++;
                    }
                    $value = '';
                    $inJob = false;
                    $creatingParam = false;
                    $checkCompareKey = false;
                }
            }

        }
        if (!empty($sortParam)) {
            $sort = explode('/', $sortParam);
            $term = $this->getTermKey($sort[0]);
            $params['sort_by'] = strtolower($sort[0]) != 'date' ? $term : 'dcterms:date';
            if (strcmp(trim(strtolower($sort[0])), 'sort.descending') == 0) {
                $params['sort_order'] = 'desc';
            } else {
                $params['sort_order'] = 'asc';
            }
        } else {
            $params['sort_by'] = $this->termKey['title'];
            $params['sort_order'] = 'asc';
        }
        if ($startRecord) {
            $params['offset'] = $startRecord - 1;
        }
        if ($maximumRecords) {
            $params['limit'] = $maximumRecords;
        }
        /*
        if (!$this->identity) {
            $params['is_public'] = 1;
        }
        */
        return $params;
    }
    protected function getTermKey($value)
    {
        foreach($this->termKey as $key => $term) {
            if (strcmp(trim(strtolower($value)), $key) == 0) {
                return $term;
            }
        }
        return null;
    }
    protected function getCompareKey($value)
    {
        foreach($this->compareKey as $key => $term) {
            if (strcmp(strtolower($value), $term) == 0) {
                return $term;
            }
        }
        return null;
    }
    protected function getJoinerKey($value)
    {
        foreach($this->joinerKey as $key => $term) {
            if (strcmp(strtolower(trim($value)), $key) == 0) {
                return $term;
            }
        }
        return null;
    }
    protected function getLocalProperty($standardProperty)
    {
        $searchValue = 'dcterms:' . $standardProperty;
        $response = $this->api->searchOne('srw_maps', ['standard_property' => $searchValue]);
        if($response->getTotalResults() > 0) {
            $map = $response->getContent();
            return $map->localProperty();
        }
        return null;
    }
    protected function getPropertyId($targetProperty)
    {
        $term = explode(":", $targetProperty);
        $response = $this->api->searchOne('properties',
            ['vocabulary_prefix' => $term[0],
                'local_name' => $term[1]]);
            return $response->getContent()->id();
    }

    protected function setValue($item, $schemaNode)
    {
        $response = $this->api->search('srw_maps', ['sort_order' => 'asc', 'sort_by' => 'id']);
        $maps = $response->getContent();
        foreach ($maps as $map) {
            if (isset($item->values()[$map->localProperty()])) {
                foreach ($item->values()[$map->localProperty()]['values'] as $value) {
                    $term = str_replace('dcterms', 'dc', $map->standardProperty());
                    if (strcmp($value->type() , 'uri') == 0) {
                        $schemaNode->addChild($term, htmlspecialchars($value->uri(), ENT_QUOTES, 'UTF-8'));
                    } else if (strcmp($value->type(), 'literal') == 0) {
                        $schemaNode->addChild($term, htmlspecialchars($value->value(), ENT_QUOTES, 'UTF-8'));
                    }
                }
            }
        }
        return  $schemaNode;
    }
}

