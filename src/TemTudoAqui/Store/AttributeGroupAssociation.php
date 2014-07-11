<?php

namespace TemTudoAqui\Store;

use TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_attributegroupassociation")
 * @ORM\HasLifecycleCallbacks
 */
class AttributeGroupAssociation extends Object {
    
    /**
     * Atributo.
     * @var \TemTudoAqui\Store\Attribute
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Attribute")
     * @ORM\JoinColumn(name="idattribute", referencedColumnName="id")
     */
    protected 	$attribute;
    
    /**
     * Grupo.
     * @var \TemTudoAqui\Store\AttributeGroup
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="AttributeGroup")
     * @ORM\JoinColumn(name="idattributegroup", referencedColumnName="id")
     */
    protected 	$attributeGroup;
    
    /**
	 * Define se o atributo � obrigat�rio.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$required = false;
    
    /**
	 * Define se o atributo � v�sivel no Front-End do sistema.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$visibleFrontEnd = true;
    
    /**
	 * Define se o atributo � edit�vel no Front-End do sistema.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$editFrontEnd = true;
    
    public function __construct(){
    	parent::__construct();
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->attribute);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}