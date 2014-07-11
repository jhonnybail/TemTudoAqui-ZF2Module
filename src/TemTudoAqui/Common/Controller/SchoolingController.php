<?php

namespace TemTudoAqui\Common\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Schooling;

class SchoolingController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Dao\SchoolingDao");
    }

	public function saveAction(Schooling $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Schooling;
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
    
	public function deleteAction(Schooling $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Schooling;
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
    
    public function listAction(Schooling $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Schooling;
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
    
	protected function validateFields(Schooling $obj){
		
		if($obj->description != null)
			$obj->description = "%".$obj->description."%";
			
		return $obj;
		
	}
    
}
