<?php

namespace TemTudoAqui\Common\Locality;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_locality_country")
 * @ORM\HasLifecycleCallbacks
 */
class Country extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Nome do país.
     * @var string
     * @ORM\Column(length=70) 
     */
    protected $name;
    
    /** 
     * DDI para ligação de telefone.
     * @var integer
     * @ORM\Column(length=4) 
     */
    protected $ddi;
    
    /**
     * Lista de estados o pais.
     * @var array
     *
     * @ORM\OneToMany(targetEntity="State", mappedBy="country", cascade={"persist"})
     */
    protected 	$state;
    
    public function __construct(){
    	if(empty($this->state))
    		$this->state = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona um estado na lista.
     *
     * @param  TemTudoAqui\Common\Locality\State	$state
     * @return void
     */
    public function addState(State $state){
    	$this->state->add($state);
    }
    
	/**
     * Retorna a lista de estados.
     *
     * @return array
     */
    public function getStates(){
    	return $this->state;
    }
    
	/**
     * @ORM\PostLoad
     */
	public function postLoad(){
		self::__construct();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }
	
}