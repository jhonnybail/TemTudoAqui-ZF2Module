<?php

namespace TemTudoAqui\Common;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_metier")
 * @ORM\HasLifecycleCallbacks
 */
class Metier extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Nome do ramo.
     * @var string
     * @ORM\Column(length=100) 
     */
    protected $name;
	
	/**
	 * Atividades relacionadas ao ramo.
	 * @var array
	 * 
     * @ORM\OneToMany(targetEntity="Business", mappedBy="metier", cascade={"remove"})
     */
    protected $business;
	
	/**
	 * Profissões relacionadas ao ramo.
	 * @var array
	 * 
     * @ORM\OneToMany(targetEntity="Profession", mappedBy="metier", cascade={"remove"})
     */
    protected $profession;
	
	public function __construct(){
    	if(empty($this->business))
    		$this->business = new \Doctrine\Common\Collections\ArrayCollection();
		if(empty($this->profession))
    		$this->profession = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona uma atividade na lista.
     *
     * @param  TemTudoAqui\Common\Business	$business
     * @return void
     */
    public function addBusiness(Business $business){
    	$this->business->add($business);
    }
    
	/**
     * Retorna a lista de atividades.
     *
     * @return array
     */
    public function getBusinesses(){
    	return $this->business;
    }
	
	/**
     * Adiciona uma profissão na lista.
     *
     * @param  TemTudoAqui\Common\Profession	$profession
     * @return void
     */
    public function addProfession(Profession $profession){
    	$this->business->add($profession);
    }
    
	/**
     * Retorna a lista de profissões.
     *
     * @return array
     */
    public function getProfessions(){
    	return $this->profession;
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