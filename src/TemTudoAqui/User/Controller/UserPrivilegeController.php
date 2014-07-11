<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
    TemTudoAqui\User\UserAuth,
    TemTudoAqui\User\Resource,
	TemTudoAqui\User\Privilege,
	TemTudoAqui\User\UserPrivilege,
	TemTudoAqui\Utils\Data\Number;

class UserPrivilegeController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\UserPrivilegeDao");
    }

	public function saveAction(UserPrivilege $obj = null)
    {
    	try{

            if(is_null($obj))
			    $obj 		= new UserPrivilege;
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
    
	public function deleteAction(UserPrivilege $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new UserPrivilege;
    		$rs 			= parent::deleteAction($obj);    		
    	}catch(\Exception $e){
    		$rs['sucess'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
    public function listAction(UserPrivilege $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new UserPrivilege;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name  = $this->getRequest()->getQuery()->get('query');
    		$rs 			= parent::listAction($obj);    		
    	}catch(\Exception $e){
    		$rs['sucess'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;
    	
    }
    
	protected function convertFields(UserPrivilege $obj){
		
		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\UserAuthDao");
		if(is_integer($obj->userAuth)){
			$user			= new UserAuth;
			$user->id		= $obj->userAuth;
			$obj->userAuth	= $dao->findById($user);
		}elseif($obj->userAuth instanceof \stdClass){
			$user			= new UserAuth;
			$user->id		= $obj->userAuth->id;
			$obj->userAuth	= $dao->findById($user);
		}
		
		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\ResourceDao");
		if(is_integer($obj->resource)){
			$resource		= new Resource;
			$resource->id	= $obj->resource;
			$obj->resource	= $dao->findById($resource);
		}elseif($obj->resource instanceof \stdClass){
			$resource		= new Resource;
			$resource->id	= $obj->resource->id;
			$obj->resource	= $dao->findById($resource);
		}
		
		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\PrivilegeDao");
		if(is_integer($obj->privilege)){
			$privilege		= new Privilege;
			$privilege->id	= $obj->privilege;
			$obj->privilege	= $dao->findById($privilege);
		}elseif($obj->privilege instanceof \stdClass){
			$privilege		= new Privilege;
			$privilege->id	= $obj->privilege->id;
			$obj->privilege	= $dao->findById($privilege);
		}
		
		return $obj;
		
	}
    
	protected function validateFields(UserPrivilege $obj){
		
		if($obj->userAuth instanceof Number){
			$user			= new UserAuth;
			$user->id		= $obj->userAuth->getValue();
			$obj->userAuth	= $user;
		}elseif(is_integer($obj->userAuth)){
			$user			= new UserAuth;
			$user->id		= $obj->userAuth;
			$obj->userAuth	= $user;
		}
		
		if($obj->resource instanceof Number){
			$resource		= new Resource;
			$resource->id	= $obj->resource->getValue();
			$obj->resource	= $resource;
		}elseif(is_integer($obj->resource)){
			$resource		= new Resource;
			$resource->id	= $obj->resource;
			$obj->resource	= $resource;
		}
		
		if($obj->privilege instanceof Number){
			$privilege		= new Privilege;
			$privilege->id	= $obj->privilege->getValue();
			$obj->privilege	= $privilege;
		}elseif(is_integer($obj->privilege)){
			$privilege		= new Privilege;
			$privilege->id	= $obj->privilege;
			$obj->privilege	= $privilege;
		}
			
		return $obj;
		
	}
    
}
