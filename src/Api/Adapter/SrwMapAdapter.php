<?php
namespace SruSrwMaker\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Api\Response;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use SruSrwMaker\Api\Representation\SrwMapRepresentation;
use SruSrwMaker\Entity\SrwMap;

class SrwMapAdapter extends AbstractEntityAdapter
{

    public function getResourceName()
    {
        return "srw_maps";
    }
    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $entity = 'omeka_root';
        if (isset($query['standard_property'])) {
            $qb->andWhere($qb->expr()->eq(
                $entity . '.standardProperty',
                $this->createNamedParameter($qb, $query['standard_property'])));
        }
    }
    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $data = $request->getContent();
        if (isset($data['local_property'])) {
            $entity->setLocalProperty($data['local_property']);
        }
        if (isset($data['standard_property'])) {
            $entity->setStandardProperty($data['standard_property']);
        }
    }

    public function getRepresentationClass()
    {
        return SrwMapRepresentation::class;
    }

    public function getEntityClass()
    {
        return SrwMap::class;
    }
    /**
     *
     * {@inheritDoc}
     * @see \Omeka\Api\Adapter\AbstractEntityAdapter::delete()
     * Delete all related items
     */
    public function delete(Request $request)
    {
        $entity = new SrwMap();
        $this->authorize($entity, Request::BATCH_DELETE);
        $connection = $this->serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
truncate table srw_map;
SQL;
        $connection->exec($sql);
        return new Response($entity);
    }
}

