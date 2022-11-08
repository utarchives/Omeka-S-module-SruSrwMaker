<?php
namespace SruSrwMaker\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class SrwMapRepresentation extends AbstractEntityRepresentation
{

    public function localProperty()
    {
        return $this->resource->getLocalProperty();
    }
    public function standardProperty()
    {
        return $this->resource->getStandardProperty();
    }
    public function getJsonLdType()
    {
        return 'o:SrwMap';
    }

    public function getJsonLd()
    {
        return [
            'o:id' => $this->id,
            'o:local_property' => $this->originalCharacter(),
            'o:standard_property' => $this->searchCharacter(),
        ];
    }
}

