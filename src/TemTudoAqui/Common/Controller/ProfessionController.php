<?php

namespace TemTudoAqui\Common\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Profession,
	TemTudoAqui\Common\Metier;

class ProfessionController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Dao\ProfessionDao");
    }

	public function saveAction(Profession $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Profession;
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
    
	public function deleteAction(Profession $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Profession;
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
    
    public function listAction(Profession $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Profession;
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
    
	protected function convertFields(Profession $obj){
		
		$dao 			= $this->getServiceLocator()->get("TemTudoAqui\Common\Dao\MetierDao");
		$metier			= new Metier;
		$metier->id		= $obj->metier instanceof \stdClass ? $obj->metier->id : (int)$obj->metier;
		$obj->metier	= $dao->findById($metier);
		
		return $obj;
		
	}
	
	protected function validateFields(Profession $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";
		if(is_integer($obj->metier)){
			$metier			= new Metier;
			$metier->id		= $obj->metier;
			$obj->metier	= $metier;
		}
			
		return $obj;
		
	}
    
}
