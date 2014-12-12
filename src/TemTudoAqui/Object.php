<?php

/**
 * TemTudoAqui (http://library.temtudoaqui.info/)
 *
 * @package     TemTudoAqui
 * @link        http://github.com/jhonnybail/tta-library para o repositório de origem
 * @copyright   Copyright (c) 2014 Tem Tudo Aqui. (http://www.temtudoaqui.info)
 * @license     http://license.temtudoaqui.info
 */
namespace TemTudoAqui;

use Doctrine\ORM\Mapping as ORM,
    TemTudoAqui\Events\ObjectEventManager,
    TemTudoAqui\Events\IObjectEventManager;

/**
 * Classe genérica para os objetos do sistema.
 */
class Object extends Generic implements IObjectEventManager
{

    use ObjectEventManager;
	
	/**
     * Constructor
     */
	public function __construct(){
		parent::__construct();
	}

    /**
     * Converte o objeto para uma array.
     *
     * @return  array
     */
	public function toArray() {	
		
		$array = get_object_vars($this);
		unset($array['reflectionClass']);
		
		foreach($array as $key => $value){

            if(!preg_match("!password!", $key) || true){

                if($value instanceof \TemTudoAqui\Utils\Data\File){
                    if(!empty($value->urlRequest))
                        $array[$key] = str_replace(System::GetVariable("DOCUMENT_ROOT"), System::GetVariable("protocol")."://".System::GetVariable("HTTP_HOST"), (string) $value->urlRequest->url);
                    else
                        $array[$key] = "";
                }elseif($value instanceof \TemTudoAqui\Object){
                    $array[$key] = $value->toArray();
                }elseif(is_object($value)){
                    if($value instanceof \Zend\EventManager\EventManager){
                        unset($array[$key]);
                    }elseif($value instanceof \Doctrine\ORM\PersistentCollection){
                        unset($array[$key]);
                    }elseif($value instanceof \Doctrine\Common\Collections\ArrayCollection){
                        unset($array[$key]);
                    }elseif($value instanceof \DateTime)
                        $array[$key] = $value->format("Y-m-d H:i:s");
                    elseif($value instanceof \TemTudoAqui\Utils\Data\DateTime)
                        $array[$key] = (string)$value->get("Y-m-d H:i:s");
                    elseif($value instanceof \TemTudoAqui\Utils\Data\File)
                        $array[$key] = (string)$value->urlRequest->url;
                    else
                        $array[$key] = (string)$value;
                }elseif(is_array($value))
                    $array[$key] = $value;
                else
                    $array[$key] = (string)$value;

            }else{
                $array[$key] = "";
            }
						
		}
		
		return $array;
		
	}

    /**
     * @ORM\PostLoad
     */
    public function postLoad(){
        @static::__construct();
    }
	
}