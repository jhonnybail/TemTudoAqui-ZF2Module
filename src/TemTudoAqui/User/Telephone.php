<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_telephone")
 * @ORM\HasLifecycleCallbacks
 */
class Telephone extends Object {
	
	const		TYPE_RESINDETIAL	= 1;
	const		TYPE_COMMERCIAL		= 2;
	const		TYPE_MOBILE			= 3;
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
     * Usuário do telefone.
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="telephone")
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
    
   	/**
	 * Tipo do telefone.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$type;
    
    /**
	 * Descrição do telefone.
	 * @var string
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$description;
    
    /**
	 * DDI para chamada internacional.
	 * @var integer
	 * 
     * @ORM\Column(type="integer", length=4)
     */
    protected 	$ddi;
    
    /**
	 * DDD para chamada de longa distância.
	 * @var integer
	 * 
     * @ORM\Column(type="integer", length=4)
     */
    protected 	$ddd;
    
    /**
	 * Número do telefone.
	 * @var string
	 * 
     * @ORM\Column(length=10)
     */
    protected 	$number;
    
    public function __construct(){
    	if(empty($this->user))
    		$this->user = new User;
    	parent::__construct();
    }
    
	public function __set($property, $value){
    	if($property == 'type'){
    		if($value == self::TYPE_COMMERCIAL || $value == self::TYPE_RESINDETIAL || $value == self::TYPE_MOBILE)
    			$this->type = $value;
    		else
    			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 81, 'Tipo de telefone inv�lido');
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
    	return new \TemTudoAqui\Utils\Data\String($this->ddi.$this->ddd.$this->number);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}