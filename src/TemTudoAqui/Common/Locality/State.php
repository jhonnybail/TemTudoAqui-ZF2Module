<?php

namespace TemTudoAqui\Common\Locality;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_locality_state")
 * @ORM\HasLifecycleCallbacks
 */
class State extends Object {
	
	/**
	 * Chave prim�ria.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Pa�s em que o estado pertence.
     * @var \TemTudoAqui\Common\Locality\Country
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="state")
     * @ORM\JoinColumn(name="idcountry", referencedColumnName="id")
     */
    protected 	$country;
    
    /** 
     * Nome do estado.
     * @var string
     * @ORM\Column(length=70) 
     */
    protected $name;
    
    /** 
     * Sigla do estado.
     * @var string
     * @ORM\Column(length=4) 
     */
    protected $uf;
    
    /**
     * Lista de cidades do estado.
     * @var array
     *
     * @ORM\OneToMany(targetEntity="City", mappedBy="state", cascade={"persist"})
     */
    protected 	$city;
    
	public function __construct(){
    	if(empty($this->city))
			$this->city = new \Doctrine\Common\Collections\ArrayCollection();
		if(empty($this->country))
			$this->country = new Country;
    	parent::__construct();
    }
    
	/**
     * Adiciona uma cidade na lista.
     *
     * @param  \TemTudoAqui\Common\Locality\City	$city
     * @return void
     */
    public function addCity(City $city){
    	$this->state->add($city);
    }
    
	/**
     * Retorna a lista de cidades.
     *
     * @return array
     */
    public function getCities(){
    	return $this->city;
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