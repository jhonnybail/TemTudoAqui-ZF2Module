<?php

namespace TemTudoAqui\Store;

use TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_attributevalue")
 * @ORM\HasLifecycleCallbacks
 */
class AttributeValue extends Object {
	
	/**
	 * Chave prim�ria.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
     * Atributo.
     * @var Attribute
     *
     * @ORM\ManyToOne(targetEntity="Attribute", inversedBy="attributeValue")
     * @ORM\JoinColumn(name="idattribute", referencedColumnName="id")
     */
    protected 	$attribute;
    
    /**
	 * Nome do valor.
	 * @var string
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$name;
    
    /**
	 * Valor.
	 * @var string
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$value;
    
   	/**
	 * Descrição.
	 * @var integer
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$description;
    
    public function __construct(){
    	if(empty($this->attribute))
    		$this->attribute = new Attribute;
    	parent::__construct();
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->value);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}