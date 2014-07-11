<?php

namespace TemTudoAqui\Common\Locality\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Locality\State,
	TemTudoAqui\Common\Locality\City;

class CityController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Locality\Dao\CityDao");
    }

    public function saveAction(City $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new City;
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
    
	public function deleteAction(City $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new City;
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
    
    public function listAction(City $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new City;
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
    
	protected function convertFields(City $obj){
		
		if(is_integer($obj->state)){
			$state			= new State;
			$state->id		= $obj->state;
			$obj->state		= $state;
		}elseif($obj->state instanceof \stdClass){
			$dao 			= $this->getServiceLocator()->get("TemTudoAqui\Common\Locality\Dao\StateDao");
			$state			= new State;
			$state->id		= $obj->state->id;
			$obj->state	= $dao->findById($state);
		}
		
		return $obj;
		
	}
    
	protected function validateFields(City $obj){
		
		if($obj->name != null)
			$obj->name 		= "%".$obj->name."%";
		if(is_integer($obj->state)){
			$state			= new State;
			$state->id		= $obj->state;
			$obj->state		= $state;
		}
		
		return $obj;
		
	}
    
}
