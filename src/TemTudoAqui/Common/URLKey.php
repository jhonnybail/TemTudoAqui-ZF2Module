<?php

namespace TemTudoAqui\Common;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_urlkey")
 * @ORM\HasLifecycleCallbacks
 */
class URLKey extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Nome da classe do objeto registrado com essa URLKey.
     * @var string
     * @ORM\Column(length=255) 
     */
    protected $className;
    
    /** 
     * URL amigável.
     * @var string
     * @ORM\Column(length=255) 
     */
    protected $url;
    
    /**
     * Layout assossiado a essa URLKey.
     * @var TemTudoAqui\Common\Layout\Layout
     * @ORM\OneToOne(targetEntity="TemTudoAqui\Common\Layout\Layout", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="idlayout", referencedColumnName="id")
     */
    protected $layout;
    
    /** 
     * Título da página assossiado ao objeto.
     * @var string
     * @ORM\Column(length=255) 
     */
    protected $pageTitle;
    
    /** 
     * Define se o objeto será padrão.
     * @var boolean
     * @ORM\Column(type="boolean", name="layoutdefault")
     */
    protected $default;
    
    public function __construct($className){
    	$this->className 	= $className;
    	$this->default 		= false;
    	parent::__construct();
    }
	
	public function __set($property, $value){
    	
    	if($property == 'className'){
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 63, 'Não é possível definir manualmente os valores de className');
    	}else
    		parent::__set($property, $value);
    	
    }
    
	/**
     * @ORM\PostLoad
     */
	public function postLoad(){
		self::__construct($this->className, null);
	}
	
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->url);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    } 
	
}