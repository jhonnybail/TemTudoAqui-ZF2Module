<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_resource")
 * @ORM\HasLifecycleCallbacks
 */
class Resource extends Object {
		
	/**
	 * Chave primária.
	 * @var string
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
	
	/**
	 * tipo do privilégio.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected 	$type;
	
	/**
	 * Nome do recurso.
	 * @var string
	 * 
     * @ORM\Column(length=50)
     */
    protected 	$name;
    
    /**
     * Lista de privilégios.
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Privilege")
     * @ORM\JoinTable(name="tta_rel_resource_privilege",
     *      joinColumns={@ORM\JoinColumn(name="idresource", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idprivilege", referencedColumnName="id")}
     *      )
     */
    protected 	$privilege;
        
    public function __construct(){
    	parent::__construct();
    }
    
	/**
     * Adiciona um privilégio no recurso.
     *
     * @param  \TemTudoAqui\User\Privilege	$privilege
     * @return void
     */
    public function addPrivilege(\TemTudoAqui\User\Privilege $privilege){
    	$this->privilege->add($privilege);
    }
    
	/**
     * Retorna a lista de privilégios.
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
    	return new \TemTudoAqui\Utils\Data\String($this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}