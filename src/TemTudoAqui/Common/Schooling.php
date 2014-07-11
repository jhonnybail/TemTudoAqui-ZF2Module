<?php

namespace TemTudoAqui\Common;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_schooling")
 * @ORM\HasLifecycleCallbacks
 */
class Schooling extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Descrição da escolariedade.
     * @var string
     * @ORM\Column(length=100) 
     */
    protected $description;
	
	 /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->description);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}