<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\User,
	TemTudoAqui\User\Email,
	TemTudoAqui\Utils\Data\Number;

class EmailController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\EmailDao");
    }
	
	public function saveAction(Email $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Email;
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
    
	public function deleteAction(Email $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Email;
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
    
    public function listAction(Email $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Email;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->email = $this->getRequest()->getQuery()->get('query');
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
    
	protected function convertFields(Email $obj){
		
		
		$dao 			= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\UserDao");
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
    
	protected function validateFields(Email $obj){
		
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
