<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_userauth")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"tta" = "UserAuth", "wecan" = "\WeCan\User\UserAuth"})
 * @ORM\HasLifecycleCallbacks
 */
class UserAuth extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;

	/**
	 * Usuário.
	 * @var \TemTudoAqui\User\User
	 *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="permission", cascade={"persist"})
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
    
   	/**
	 * Recursos.
	 * @var \TemTudoAqui\User\Role
	 * 
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="idrole", referencedColumnName="id")
     */
    protected 	$role;

    /**
	 * Privilégios.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
     * @ORM\OneToMany(targetEntity="UserPrivilege", mappedBy="userAuth", cascade={"persist","remove"})
     */
    protected $privilege;

    /**
     * Usuário para acesso.
     * @var string
     *
     * @ORM\Column(length=30)
     */
    protected 	$username;

    /**
     * Senha para acesso.
     * @var string
     *
     * @ORM\Column(length=30)
     */
    protected 	$password;

    /**
     * Senha para confirmação.
     * @var string
     *
     * @ORM\Column(length=30)
     */
    protected 	$password2;

    /**
     * Data e Hora do registro.
     * @var date
     *
     * @ORM\Column(type="datetime")
     */
    protected 	$registrationDate;
    
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
    	$userPrivilege = new UserPrivilege;
        $userPrivilege->userAuth = $this;
        $userPrivilege->resource = $resource;
        $userPrivilege->privilege = $privilege;
        $this->privilege->add($userPrivilege);
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
    	return new \TemTudoAqui\Utils\Data\String($this->username." ({$this->user})".(!is_null($this->enterprise) ? ": ".$this->enterprise : ""));
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}