<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_email")
 * @ORM\HasLifecycleCallbacks
 */
class Email extends Object {
		
	/**
	 * Chave primária.
	 * @var string
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
	
	/**
	 * Endereço do correio eletrônico.
	 * @var string
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$email;
    
    /**
     * Usuário do e-mail.
     * @var \TemTudoAqui\User\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="email")
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
        
    public function __construct(){
    	if(empty($this->user))
    		$this->user = new User;
    	parent::__construct();
    }
    
    /**
     * Valida o e-mail.
     *
     * @param  string	$email
     * @return boolean
     */
	public static function ValidEmail($email){

		$email = new \TemTudoAqui\Utils\Data\String((string) $email);

		if(!$email->search("@"))
			return false;

		$sub = explode('@', $email);
		if(!preg_match('!.!', $sub[1]))
			return false;
		//if(!checkdnsrr($sub[1]))
			//return false;
		  
		return true;
		  
	 }
	 
	 public function __set($property, $value){
	 	if($property == 'email'){
	 		if(!self::ValidEmail($value))
	 			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 58, 'E-mail inválido');
	 		else
	 			$this->email = $value;
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
    	return new \TemTudoAqui\Utils\Data\String($this->email);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}