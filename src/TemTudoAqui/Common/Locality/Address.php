<?php

namespace TemTudoAqui\Common\Locality;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_locality_address")
 * @ORM\HasLifecycleCallbacks
 */
class Address extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Logradouro do endereço.
     * @var string
     * @ORM\Column(length=100)
     */
    protected 	$street;
    
    /** 
     * Número do endereço.
     * @var string
     * @ORM\Column(length=30) 
     */
    protected $number;
    
    /** 
     * Complemento para localização do endereço.
     * @var string
     * @ORM\Column(length=100) 
     */
    protected $complement;  
    
    /**
     * Bairro do endereço.
     * @var string
     * @ORM\Column(length=100)
     *
     */
    protected 	$neighborhood;
    
    /**
     * Cidade do endereço.
     * @var TemTudoAqui\Common\Locality\City
	 *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="idcity", referencedColumnName="id")
     */
    protected 	$city;
    
    /**
     * Estado do endereço.
     * @var TemTudoAqui\Common\Locality\State
	 *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="idstate", referencedColumnName="id")
     */
    protected 	$state;
    
    /**
     * Country do endereço.
     * @var TemTudoAqui\Common\Locality\Country
	 *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="idcountry", referencedColumnName="id")
     */
    protected 	$country;
    
    /** 
     * CEP do endereço.
     * @var int
     * @ORM\Column(length=10) 
     */
    protected $zipCode;
    
	public function __construct(){
		if(empty($this->city))
			$this->city = new City;
		if(empty($this->state))
			$this->state = new State;
		if(empty($this->country))
			$this->country = new Country;
    	parent::__construct();
    }
    
	/**
     * @ORM\PostLoad
     */
	public function postLoad(){
		self::__construct();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->street.", ".$this->number." - ".$this->neighborhood." - ".$this->city." - ".$this->state." - ".$this->country." - ".$this->zipCode);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}