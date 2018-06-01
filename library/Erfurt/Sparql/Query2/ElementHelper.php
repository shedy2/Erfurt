<?php
/**
 * This file is part of the {@link http://erfurt-framework.org Erfurt} project.
 *
 * @copyright Copyright (c) 2012-2016, {@link http://aksw.org AKSW}
 * @license   http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * OntoWiki Sparql Query ElementHelper
 * 
 * a abstract helper class for objects that are elements of groups. i.e.: Triples but also GroupGraphPatterns
 * 
 * @package    Erfurt_Sparql_Query2
 * @author     Jonas Brekle <jonas.brekle@gmail.com>
 * @copyright  Copyright (c) 2012, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
abstract class Erfurt_Sparql_Query2_ElementHelper {
    protected $id;
    protected $element1;
    protected $element2;

    public function __construct() {
        $this->id = Erfurt_Sparql_Query2::getNextID();
    }

    //abstract public function getSparql();

    /**
     * addParent
     * when a ElementHelper-object is added to a ContainerHelper-object this method is called. lets the child know of the new parent
     * @deprecated - no action
     * @param Erfurt_Sparql_Query2_ContainerHelper $parent
     * @return Erfurt_Sparql_Query2_ElementHelper $this
     */
    public function addParent(Erfurt_Sparql_Query2_ContainerHelper $parent) {
        return $this;
    }

    public function getElement1()
    {
        return $this->element1;
    }

    public function getElement2()
    {
        return $this->element2;
    }

    /**
     * remove
     * removes this object from a query
     * @param $query 
     * @return Erfurt_Sparql_Query2_ElementHelper $this
     */
    public function remove($query) {
        //remove from this query
        foreach($query->getParentContainer($this) as $parent){
            $parent->removeElement($this);
        }

        return $this;
    }

    /**
     * removeParent
     * removes a parent
     * @deprecated - no action
     * @param Erfurt_Sparql_Query2_ContainerHelper $parent
     * @return Erfurt_Sparql_Query2_ElementHelper $this
     */
    public function removeParent(Erfurt_Sparql_Query2_ContainerHelper $parent) {
        return $this;
    }

    /**
     * getID
     * @return int the id of this object
     */
    public function getID() {
        return $this->id;
    }

    /**
     * equals
     * @param mixed $obj the object to compare with
     * @return bool true if equal, false otherwise
     */
    public function equals($obj) {
        //trivial cases
        if ($this === $obj) return true;

        if (!method_exists($obj, 'getID')) {
            return false;
        }

        if ($this->getID() == $obj->getID()) {
            return true;
        }

        if (get_class($this) !== get_class($obj)) {
                return false;
        }

        return $this->getSparql() === $obj->getSparql();
    }

    abstract public function getSparql();

    public function  __toString()
    {
        return $this->getSparql();
    }

    protected static function toArray_elementValue($value)
    {
        if ($value instanceof Erfurt_Sparql_Query2_ElementHelper) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(array(static::class, 'toArray_elementValue'), $value);
        }

        return $value;
    }

    public function toArray()
    {
        $reflect = new ReflectionClass($this);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $res = [
            '__class' => static::class,
            '__class_short' => substr(static::class, max(strrpos(static::class, '_'), strrpos(static::class, '\\')) + 1)
        ];

        foreach ($props as $prop) {
            // Пропускаем статику и id, т.к. они генерируемые
            if (!$prop->isStatic() && $prop->getName() != 'id') {
                $value = $this->{$prop->getName()};
                $res[$prop->getName()] = static::toArray_elementValue($value);
            }
        }

        return $res;
    }
}
