<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_useraddress")
 * @ORM\HasLifecycleCallbacks
 */
class UserAddress extends Object {
	
	const		TYPE_RESINDETIAL	= 1;
	const		TYPE_COMMERCIAL		= 2;
	
	/**
	 * Usuário referente ao endereço.
	 * @var \TemTudoAqui\User\User
	 * 
     * @ORM\Id @ORM\ManyToOne(targetEntity="User", inversedBy="address")
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
    
    /**
	 * Endereço referente ao usuário.
	 * @var \TemTudoAqui\Common\Locality\Address
	 * 
     * @ORM\Id @ORM\ManyToOne(targetEntity="TemTudoAqui\Common\Locality\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="idaddress", referencedColumnName="id")
     */
    protected 	$address;
    
    /**
	 * Tipo de endereço.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$type;
    
    public function __construct(){
    	if(empty($this->user))
    		$this->user = new User;
    	if(empty($this->address))
    		$this->address = new \TemTudoAqui\Common\Locality\Address;
    	parent::__construct();
    }
    
    public function __set($property, $value){
    	if($property == 'type'){
    		if($value == self::TYPE_COMMERCIAL || $value == self::TYPE_RESINDETIAL)
    			$this->type = $value;
    		else
    			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 51, 'Tipo de endereço inválido');
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
    	return new \TemTudoAqui\Utils\Data\String($this->user.": ".$this->address);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}