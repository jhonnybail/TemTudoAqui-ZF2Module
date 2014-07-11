<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\Privilege;

class PrivilegeController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\PrivilegeDao");
    }

	public function saveAction(Privilege $obj = null)
    {
    	try{

    		if(is_null($obj))
			    $obj 		= new Privilege;
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
    
	public function deleteAction(Privilege $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Privilege;
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
    
    public function listAction(Privilege $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Privilege;
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
    
}
