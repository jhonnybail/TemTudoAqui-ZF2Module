<?php

namespace TemTudoAqui\Common\Locality;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_locality_city")
 * @ORM\HasLifecycleCallbacks
 */
class City extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Estado em que a cidade pertence.
     * @var TemTudoAqui\Common\Locality\State
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="city")
     * @ORM\JoinColumn(name="idstate", referencedColumnName="id")
     */
    protected 	$state;
    
    /** 
     * Nome da cidade.
     * @var string
     * @ORM\Column(length=70) 
     */
    protected $name;
    
    /** 
     * DDD para ligação de telefone.
     * @var integer
     * @ORM\Column(length=4) 
     */
    protected $ddd;
    
	public function __construct(){
		if(empty($this->state))
			$this->state = new State;
		parent::__construct();
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