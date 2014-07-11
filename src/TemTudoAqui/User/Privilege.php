<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_privilege")
 * @ORM\HasLifecycleCallbacks
 */
class Privilege extends Object {
		
	/**
	 * Chave primária.
	 * @var string
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
	
	/**
	 * Nome do privilégio.
	 * @var string
	 * 
     * @ORM\Column(length=50)
     */
    protected 	$name;
        
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
    	return new \TemTudoAqui\Utils\Data\String($this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}