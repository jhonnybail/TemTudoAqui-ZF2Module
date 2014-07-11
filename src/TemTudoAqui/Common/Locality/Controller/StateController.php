<?php

namespace TemTudoAqui\Common\Locality\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Locality\State,
	TemTudoAqui\Common\Locality\Country;

class StateController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Locality\Dao\StateDao");
    }

	public function saveAction(State $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new State;
    		$rs 			= parent::saveAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
	public function deleteAction(State $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new State;
    		$rs 			= parent::deleteAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
    public function listAction(State $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new State;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name  = $this->getRequest()->getQuery()->get('query');
    		$rs 			= parent::listAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;
    	
    }
    
	protected function convertFields(State $obj){
		
		if(is_integer($obj->country)){
			$country		= new Country;
			$country->id	= $obj->country;
			$obj->country	= $country;
		}elseif($obj->country instanceof \stdClass){
			$dao 			= $this->getServiceLocator()->get("TemTudoAqui\Common\Locality\Dao\CountryDao");
			$country		= new Country;
			$country->id	= $obj->country->id;
			$obj->country	= $dao->findById($country);
		}
		
		return $obj;
		
	}
    
	protected function validateFields(State $obj){
		
		if($obj->name != null)
			$obj->name 		= "%".$obj->name."%";
		if(is_integer($obj->country)){
			$country		= new Country;
			$country->id	= $obj->country;
			$obj->country	= $country;
		}
		
		return $obj;
		
	}
    
}
