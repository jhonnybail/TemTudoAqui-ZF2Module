<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\UserAuth,
	TemTudoAqui\User\Logger,
	TemTudoAqui\Utils\Data\Number;

class LoggerController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\LoggerDao");
    }
    
	public function deleteAction(Logger $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Logger;
    		$rs 			= parent::deleteAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
    public function listAction(Logger $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		        = new Logger;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->controller    = $this->getRequest()->getQuery()->get('query');
    		$rs 			        = parent::listAction($obj);
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;
    	
    }
    
	protected function validateFields(Logger $obj){
		
		if($obj->userAuth instanceof Number){
			$userAuth			= new UserAuth;
			$userAuth->id		= $obj->userAuth->getValue();
			$obj->userAuth		= $userAuth;
		}elseif(is_integer($obj->userAuth)){
			$userAuth			= new UserAuth;
			$userAuth->id		= $obj->userAuth;
			$obj->userAuth		= $userAuth;
		}

        if($obj->userAuthActive instanceof Number){
            $userAuthActive			= new UserAuth;
            $userAuthActive->id		= $obj->userAuthActive->getValue();
            $obj->userAuthActive	= $userAuthActive;
        }elseif(is_integer($obj->userAuthActive)){
            $userAuthActive			= new UserAuth;
            $userAuthActive->id		= $obj->userAuthActive;
            $obj->userAuthActive	= $userAuthActive;
        }

        if($obj->dateRequest != null){
            $d = DateTime::createFromFormat("d/m/Y", $obj->dateRequest);
            $obj->dateRequest = $d;
        }

        if($obj->dateActive != null){
            $d = DateTime::createFromFormat("d/m/Y", $obj->dateActive);
            $obj->dateActive = $d;
        }

        if($obj->resource instanceof Number){
            $resource		= new Resource;
            $resource->id	= $obj->resource->getValue();
            $obj->resource	= $resource;
        }elseif(is_integer($obj->resource)){
            $resource		= new Resource;
            $resource->id	= $obj->resource;
            $obj->resource	= $resource;
        }
		
		return $obj;
		
	}
    
}
