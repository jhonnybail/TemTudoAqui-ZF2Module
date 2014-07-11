<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\Resource,
    TemTudoAqui\User\Role,
    TemTudoAqui\User\UserAuth;

class ResourceController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\ResourceDao");
    }

	public function saveAction(Resource $obj = null)
    {
    	try{

            if(is_null($obj))
			    $obj 		= new Resource;
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
    
	public function deleteAction(Resource $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Resource;
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

    public function listAction(Resource $obj = null)
    {
        try{
            if(is_null($obj))
                $obj 		= new Resource;
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

    public function listByRoleAction()
    {
        try{
            $roleDao        = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\RoleDao");
            $obj 			= new Role;
            $obj->id		= $this->getRequest()->getQuery()->get('role');
            $obj			= $roleDao->findById($obj);

            if($obj->getPrivilegies()->count() > 0)
                foreach($obj->getPrivilegies() as $key => $value){
                    $rs['root'][] = $value;
                }

            $rs['size']		= $obj->getPrivilegies()->count();
        }catch(\Exception $e){
            $rs['success'] 	= false;
            $rs['message'] 	= $e->getMessage();
        }

        return $this->encodeOutput($rs);

    }

    public function listByUserAction()
    {
        try{
            $userAuthDao    = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\UserAuthDao");
            $obj 			= new UserAuth;
            $obj->id		= $this->getRequest()->getQuery()->get('userauth');
            $obj			= $userAuthDao->findById($obj);

            if($obj->getPrivilegies()->count() > 0)
                foreach($obj->getPrivilegies() as $key => $value){
                    $rs['root'][] = $value;
                }

            $rs['size']		= $obj->getPrivilegies()->count();
        }catch(\Exception $e){
            $rs['success'] 	= false;
            $rs['message'] 	= $e->getMessage();
        }

        return $this->encodeOutput($rs);

    }
    
}
