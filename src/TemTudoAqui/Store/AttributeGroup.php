<?php

namespace TemTudoAqui\Store;

use TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_attributegroup")
 * @ORM\HasLifecycleCallbacks
 */
class AttributeGroup extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
     * Usu�rio do grupo.
     * @var \TemTudoAqui\User\User
     *
     * @ORM\ManyToOne(targetEntity="TemTudoAqui\User\User")
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
    
    /**
	 * Nome do grupo.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected 	$name;
    
    /**
     * Atributos do grupo.
     * @var	array
     *
     * @ORM\OneToMany(targetEntity="AttributeGroupAssociation", mappedBy="attributeGroup", cascade={"persist"})
     */
    protected 	$attribute;
    
    public function __construct(){
    	if(empty($this->attribute))
    		$this->attribute = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona um atributo na lista.
     *
     * @param  \TemTudoAqui\Store\Attribute		$attribute
     * @param  boolean							$required
     * @param  boolean							$visibleFrontEnd
     * @param  boolean							editFrontEnd
     * @return \TemTudoAqui\Store\AttributeGroupAssociation
     */
    public function addAttribute(Attribute $attribute, $required = false, $visibleFrontEnd = false, $editFrontEnd = false){
    	if(!empty($this->id)){
	    	$attr = new AttributeGroupAssociation;
	    	$attr->attribute = $attribute;
	    	$attr->attributeGroup = $this;
	    	$attr->required = $required;
	    	$attr->visibleFrontEnd = $visibleFrontEnd;
	    	$attr->editFrontEnd = $editFrontEnd;
	    	$this->attribute->add($attr);
	    	return $attr;
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 64, 'Não é permitido adicionar um produto antes de salvar o Grupo de atributos.');
    }
    
	/**
     * Retorna a lista de atributos.
     *
     * @return array
     */
    public function getAttributes(){
    	return $this->attribute;
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}