<?php

namespace TemTudoAqui\Common\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Metier;

class MetierController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Dao\MetierDao");
    }

	public function saveAction(Metier $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Metier;
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
    
	public function deleteAction(Metier $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Metier;
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
    
    public function listAction(Metier $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Metier;
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
    
	protected function validateFields(Metier $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";
			
		return $obj;
		
	}
    
}
