<?php

namespace TemTudoAqui\Store;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_attribute")
 * @ORM\HasLifecycleCallbacks
 */
class Attribute extends Object {
	
	const		TYPE_TEXT			= 1;
	const		TYPE_TEXTAREA		= 2;
	const		TYPE_DATE			= 3;
	const		TYPE_MULTIPLECHOICE	= 4;
	
	/**
	 * Chave prim�ria.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
     * Usu�rio do atributo.
     * @var \TemTudoAqui\User\User
     *
     * @ORM\ManyToOne(targetEntity="TemTudoAqui\User\User")
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
    
    /**
	 * C�digo do atributo.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected 	$code;
    
    /**
	 * Nome do atributo.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected 	$name;
    
   	/**
	 * Tipo do attributo.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$type;
    
    /**
	 * Valor �nico.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$uniqueValue = false;
    
    /**
	 * Permite sintaxe HTML.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$allowHTML = true;
    
    /**
     * Valores para o atributo.
     * @var	array
     *
     * @ORM\OneToMany(targetEntity="AttributeValue", mappedBy="attribute", cascade={"persist"})
     */
    protected 	$attributeValue;
    
    public function __construct(){
    	if(empty($this->attributeValue))
    		$this->attributeValue = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona um valor para o atributo na lista.
     *
     * @param  \TemTudoAqui\Store\AttributeValue		$attributeValue
     * @return void
     */
    public function addAttributeValue(AttributeValue $attributeValue){
    	if($this->type == self::TYPE_MULTIPLECHOICE){
	    	$attributeValue->attribute = $this;
	    	$this->attributeValue->add($attributeValue);
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 98, 'Tipo de atributo não permite valores pré definidos.');
    }
    
	/**
     * Retorna a lista de valores do atributo.
     *
     * @return array
     */
    public function getAttributeValues(){
    	return $this->attributeValue;
    }
    
	public function __set($property, $value){
    	if($property == 'type'){
    		if($value == self::TYPE_DATE || $value == self::TYPE_MULTIPLECHOICE || $value == self::TYPE_TEXT || $value == self::TYPE_TEXTAREA)
    			$this->type = $value;
    		else
    			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 115, 'Tipo de atributo inválido');
    	}else
    		parent::__set($property, $value);
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->code.": ".$this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}