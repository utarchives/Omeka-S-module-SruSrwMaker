<?php
namespace SruSrwMaker\Entity;

use Omeka\Entity\AbstractEntity;
/**
 *
 * @Entity
 * @Table(name="srw_map",indexes={@Index(name="local_property_idx", columns={"local_property"})})
 */
class SrwMap extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(type="string", nullable=true)
     */
    protected $localProperty;
    /**
     * @Column(type="string", nullable=true)
     */
    protected $standardProperty;

    /**
     * @return mixed
     */
    public function getLocalProperty()
    {
        return $this->localProperty;
    }

    /**
     * @return mixed
     */
    public function getStandardProperty()
    {
        return $this->standardProperty;
    }

    /**
     * @param mixed $localProperty
     */
    public function setLocalProperty($localProperty)
    {
        $this->localProperty = $localProperty;
    }

    /**
     * @param mixed $standardProperty
     */
    public function setStandardProperty($standardProperty)
    {
        $this->standardProperty = $standardProperty;
    }

    public function getId()
    {
        return $this->id;
    }
}

