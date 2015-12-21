<?php

namespace TemTudoAqui\Common\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Service;

class ServiceController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Dao\ServiceDao");
    }

	public function saveAction(Service $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Service;
    		$rs 			= parent::saveAction($obj); 
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
	public function deleteAction(Service $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Service;
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
    
    public function listAction(Service $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Service;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name  = $this->getRequest()->getQuery()->get('query');
    		$rs 			= parent::listAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
	protected function validateFields(Service $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";
			
		return $obj;
		
	}
    
}
