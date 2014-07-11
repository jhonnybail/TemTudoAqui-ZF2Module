<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_logger")
 * @ORM\HasLifecycleCallbacks
 */
class Logger extends Object {
		
	/**
	 * Chave primária.
	 * @var string
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;

    /**
     * Nome da Controller.
     * @var string
     *
     * @ORM\Column(length=100)
     */
    protected 	$controller;

    /**
     * Nome da Action.
     * @var string
     *
     * @ORM\Column(length=50)
     */
    protected 	$action;

    /**
     * Usuário que realizou a requisição.
     * @var \TemTudoAqui\User\UserAuth
     *
     * @ORM\ManyToOne(targetEntity="UserAuth")
     * @ORM\JoinColumn(name="iduserauth", referencedColumnName="id")
     */
    protected 	$userAuth;

    /**
     * Usuário de permissão.
     * @var \TemTudoAqui\User\UserAuth
     *
     * @ORM\ManyToOne(targetEntity="UserAuth")
     * @ORM\JoinColumn(name="iduserauthactive", referencedColumnName="id")
     */
    protected 	$userAuthActive;

    /**
     * Método usado para requisição.
     * @var string
     *
     * @ORM\Column(length=50)
     */
    protected 	$method;

    /**
     * Dados passados por POST.
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected 	$dataPost;

    /**
     * Dados passados por GET.
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected 	$dataGet;

    /**
     * Dados passados por FILE.
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected 	$dataFile;

    /**
     * Data e hora da requisição.
     * @var datetime
     *
     * @ORM\Column(type="datetime")
     */
    protected 	$dateRequest;

    /**
     * Data e hora da permissão.
     * @var datetime
     *
     * @ORM\Column(type="datetime")
     */
    protected 	$dateActive;

    /**
     * URL da requisição.
     * @var string
     *
     * @ORM\Column(length=255)
     */
    protected 	$path;

    /**
     * Recurso utilizado.
     * @var \TemTudoAqui\User\Resource
     *
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(name="idresource", referencedColumnName="id")
     */
    protected 	$resource;
        
    public function __construct(){
    	parent::__construct();
    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->controller."/".$this->action);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}