<?php

namespace TemTudoAqui\Store;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\User\System;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_rel_productattribute_value")
 * @ORM\HasLifecycleCallbacks
 */
class ProductAttributeValue extends Object {
	
	/**
	 * Chave prim�ria.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
     * Atributo.
     * @var ProductAttribute
     *
     * @ORM\ManyToOne(targetEntity="ProductAttribute", inversedBy="productAttributeValue")
     * @ORM\JoinColumn(name="idproductattribute", referencedColumnName="id")
     */
    protected 	$productAttribute;
    
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
    
    /**
	 * Define se a opção está ativa.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$active;
    
   	/**
	 * Define se o preço irá mudar.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$priceChange;
    
    /**
	 * Define se o produto possui frete grátis.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$freeShipping = false;
    
    /**
	 * Peso do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$weight;
    
    /**
	 * Comprimento do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$length;
    
    /**
	 * Altura do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$height;
    
    /**
	 * Profundidade do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$depth;
    
    /**
	 * Define se o produto n�o ter� pre�o.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$withoutPrice;
    
    /**
	 * Pre�o do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=2)
     */
    protected 	$price;
    
    /**
	 * Pre�o promocional do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=2)
     */
    protected 	$promotionalPrice;
    
    /**
	 * Estoque do produto.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$stock;
    
    /**
	 * Tipo de frete do produto.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$shipping;
    
    public function __construct(){
    	parent::__construct();
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		self::__construct();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}