<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_role")
 * @ORM\HasLifecycleCallbacks
 */
class Role extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
	 * Tipo de permissão.
	 * @var integer
	 * 
     * @ORM\Column(length=50)
     */
    protected 	$name;
    
    /**
	 * Privilégios.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\OneToMany(targetEntity="RolePrivilege", mappedBy="role", cascade={"persist","remove"})
     */
    protected $privilege;
    
    public function __construct(){
    	if(empty($this->privilege))
    		$this->privilege = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona um privilégio na lista.
     *
     * @param  \TemTudoAqui\User\Resource	$resource
     * @param  \TemTudoAqui\User\Privilege	$privilege
     * @return void
     */
    public function addPrivilege(Resource $resource, Privilege $privilege){
    	$rolePrivilege = new RolePrivilege;
        $rolePrivilege->role = $this;
        $rolePrivilege->resource = $resource;
        $rolePrivilege->privilege = $privilege;
        $this->privilege->add($rolePrivilege);
    }
    
	/**
     * Retorna a lista de privilegios.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrivilegies(){
    	return $this->privilege;
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->user.": ".$this->enterprise);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}