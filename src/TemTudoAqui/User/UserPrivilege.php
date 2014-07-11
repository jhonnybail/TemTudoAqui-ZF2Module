<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_userprivilege")
 * @ORM\HasLifecycleCallbacks
 */
class UserPrivilege extends Object {
	
	/**
	 * Usuário.
	 * @var \TemTudoAqui\User\UserAuth
     * 
     * @ORM\Id @ORM\ManyToOne(targetEntity="UserAuth", inversedBy="privilege")
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$userAuth;
    
    /**
	 * Recurso.
	 * @var \TemTudoAqui\User\Resource
	 * 
     * @ORM\Id @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(name="idresource", referencedColumnName="id")
     */
    protected 	$resource;
    
    /**
	 * Privilégio.
	 * @var \TemTudoAqui\User\Privilege
	 * 
     * @ORM\Id @ORM\ManyToOne(targetEntity="Privilege")
     * @ORM\JoinColumn(name="idprivilege", referencedColumnName="id")
     */
    protected 	$privilege;
    
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
    	return new \TemTudoAqui\Utils\Data\String($this->resource.": ".$this->privilege);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}