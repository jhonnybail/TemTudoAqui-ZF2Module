<?php

namespace TemTudoAqui\Store;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_productattribute")
 * @ORM\HasLifecycleCallbacks
 */
class ProductAttribute extends Object {
    
    /**
     * Atributo.
     * @var \TemTudoAqui\Store\Attribute
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Attribute")
     * @ORM\JoinColumn(name="idattribute", referencedColumnName="id")
     */
    protected 	$attribute;
    
    /**
     * Produto.
     * @var \TemTudoAqui\Store\Product
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="idproduct", referencedColumnName="id")
     */
    protected 	$product;
    
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
    
    /**
     * Valores para o atributo.
     * @var	array
     *
     * @ORM\OneToMany(targetEntity="ProductAttributeValue", mappedBy="productAttribute", cascade={"persist"})
     */
    protected 	$productAttributeValue;
    
    public function __construct(){
    	if(empty($this->productAttributeValue))
    		$this->productAttributeValue = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona um valor para o atributo na lista.
     *
     * @param  \TemTudoAqui\Store\ProductAttributeValue		$productAttributeValue
     * @return void
     */
    public function addAttributeValue(ProductAttributeValue $productAttributeValue){
    	if($this->attribute->type == self::TYPE_MULTIPLECHOICE){
	    	$productAttributeValue->productAttribute = $this;
	    	$this->productAttributeValue->add($productAttributeValue);
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 77, 'Tipo de atributo não permite valores pré definidos.');
    }
    
	/**
     * Retorna a lista de valores do atributo.
     *
     * @return array
     */
    public function getProductAttributeValues(){
    	return $this->productAttributeValue;
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