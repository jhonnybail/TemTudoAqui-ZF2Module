<?php

namespace TemTudoAqui\Common\Locality\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Locality\Country;

class CountryController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Locality\Dao\CountryDao");
    }

	public function saveAction(Country $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Country;
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
    
	public function deleteAction(Country $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Country;
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
    
    public function listAction(Country $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Country;
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
    
	protected function validateFields(Country $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";
			
		return $obj;
		
	}
    
}
