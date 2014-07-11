<?php

namespace TemTudoAqui\Common;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_business")
 * @ORM\HasLifecycleCallbacks
 */
class Business extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Nome da atividade.
     * @var string
     * @ORM\Column(length=100) 
     */
    protected $name;
	
	/**
     * Ramo da atividade.
     * @var \TemTudoAqui\Common\Metier
     *
     * @ORM\ManyToOne(targetEntity="Metier", inversedBy="business")
     * @ORM\JoinColumn(name="idmetier", referencedColumnName="id")
     */
    protected 	$metier;
	
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