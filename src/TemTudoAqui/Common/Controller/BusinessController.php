<?php

namespace TemTudoAqui\Common\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Business,
	TemTudoAqui\Common\Metier,
	TemTudoAqui\User\User,
	WeCan\Enterprise,
	TemTudoAqui\Utils\Data\ArrayObject;

class BusinessController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Dao\BusinessDao");
    }

	public function saveAction(Business $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Business;
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
    
	public function deleteAction(Business $obj = null)
    {
    	try{
    		$obj 			= new Business;
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
    
    public function listAction(Business $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj        = new Business;
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
	
	public function listbyuserAction()
    {
    	try{
    		
    		$get 				= $this->getRequest()->getQuery();
    		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\UserDao");
    		
    		$obj 				= new User;
    		$obj->id			= $get->get('user');
    		$obj 				= $dao->findById($obj);
    		$temp				= new ArrayObject;
    	
    		if($obj != null)
	    		foreach($obj->getBusinesses() as $bus) 			
	    			$temp->append($bus->toArray());
	    	
    		$rs = parent::listByArray($temp);
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}

        return $this->encodeOutput($rs);
    	
    }
	
	public function listbyenterpriseAction()
    {
    	try{
    		
    		$get 				= $this->getRequest()->getQuery();
    		$dao 				= $this->getServiceLocator()->get("WeCan\Dao\EnterpriseDao");
    		
    		$obj 				= new Enterprise;
    		$obj->id			= $get->get('enterprise');
    		$obj 				= $dao->findById($obj);
    		$temp				= new ArrayObject;
		
    		if($obj != null)
	    		foreach($obj->getBusinesses() as $bus) 			
	    			$temp->append($bus->toArray());
	    	
    		$rs = parent::listByArray($temp);
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}

        return $this->encodeOutput($rs);
    	
    }
    
	protected function convertFields(Business $obj){
		
		$dao 			= $this->getServiceLocator()->get("TemTudoAqui\Common\Dao\MetierDao");
		$metier			= new Metier;
		$metier->id		= $obj->metier instanceof \stdClass ? $obj->metier->id : (int)$obj->metier;
		$obj->metier	= $dao->findById($metier);
		
		return $obj;
		
	}
	
	protected function validateFields(Business $obj){
		
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
