<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\User,
	TemTudoAqui\User\Telephone,
	TemTudoAqui\Utils\Data\Number;

class TelephoneController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\TelephoneDao");
    }

	public function saveAction(Telephone $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Telephone;
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
    
	public function deleteAction(Telephone $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Telephone;
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
    
    public function listAction(Telephone $obj = null)
    {
    	try{
    		if(is_null($obj))
                $obj 		        = new Telephone;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->description   = $this->getRequest()->getQuery()->get('query');
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
    
	protected function convertFields(Telephone $obj){
		
		$dao 			= $this->getRequest()->get("TemTudoAqui\User\Dao\UserDao");
		if(is_integer($obj->user)){
			$user		= new User;
			$user->id	= $obj->user;
			$obj->user	= $dao->findById($user);
		}elseif($obj->user instanceof \stdClass){
			$user		= new User;
			$user->id	= $obj->user->id;
			$obj->user	= $dao->findById($user);
		}
		
		return $obj;
		
	}
    
	protected function validateFields(Telephone $obj){
		
		if($obj->description != null)
			$obj->description = "%".$obj->description."%";
		if($obj->number != null)
			$obj->number = $obj->number."%";
		
		if($obj->user instanceof Number){
			$user			= new User;
			$user->id		= $obj->user->getValue();
			$obj->user		= $user;
		}elseif(is_integer($obj->user)){
			$user			= new User;
			$user->id		= $obj->user;
			$obj->user		= $user;
		}
		
		return $obj;
		
	}
    
}
