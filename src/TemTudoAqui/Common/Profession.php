<?php

namespace TemTudoAqui\Common;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_profession")
 * @ORM\HasLifecycleCallbacks
 */
class Profession extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Nome da profissão.
     * @var string
     * @ORM\Column(length=100) 
     */
    protected $name;
	
	/**
     * Ramo da profissão.
     * @var TemTudoAqui\Common\Metier
     *
     * @ORM\ManyToOne(targetEntity="Metier", inversedBy="profession")
     * @ORM\JoinColumn(name="idmetier", referencedColumnName="id")
     */
    protected $metier;
	
	 
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