<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
    TemTudoAqui\User\Role,
    TemTudoAqui\User\Resource,
	TemTudoAqui\User\Privilege,
	TemTudoAqui\Utils\Data\Number,
    TemTudoAqui\User\RolePrivilege;

class RolePrivilegeController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\RolePrivilegeDao");
    }

	public function saveAction(RolePrivilege $obj = null)
    {
    	try{

            if(is_null($obj))
			    $obj 		= new RolePrivilege;
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
    
	public function deleteAction(RolePrivilege $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new RolePrivilege;
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
    
    public function listAction(RolePrivilege $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new RolePrivilege;
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
    
	protected function convertFields(RolePrivilege $obj){
		
		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\RoleDao");
		if(is_integer($obj->role)){
			$role		= new Role;
			$role->id	= $obj->role;
			$obj->role	= $dao->findById($role);
		}elseif($obj->role instanceof \stdClass){
			$role		= new Role;
			$role->id	= $obj->role->id;
			$obj->role	= $dao->findById($role);
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
    
	protected function validateFields(RolePrivilege $obj){
		
		if($obj->role instanceof Number){
			$role		= new Role;
			$role->id	= $obj->role->getValue();
			$obj->role	= $role;
		}elseif(is_integer($obj->role)){
			$role		= new Role;
			$role->id	= $obj->role;
			$obj->role	= $role;
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
