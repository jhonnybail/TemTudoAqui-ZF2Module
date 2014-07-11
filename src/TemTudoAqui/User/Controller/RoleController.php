<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\Role,
    TemTudoAqui\User\Resource,
    TemTudoAqui\User\Privilege;

class RoleController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\RoleDao");
    }

	public function saveAction(Role $obj = null)
    {
    	try{

            $this->getDao()->beginTransaction();

            if(is_null($obj))
			    $obj 		= new Role;
    		$rs 			= parent::saveAction($obj);

            if($this->getRequest()->isPost()){

                $post 		= $this->getRequest()->getPost();
                $get 		= $this->getRequest()->getQuery();

                $obj 		= $this->getDao()->findById($obj);

                if($get->get('input') == 'jsonextjs'){

                    $data 		= \Zend\Json\Decoder::decode($post->get('root'));

                    //Resources
                    $daoResource = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\ResourceDao");
                    $daoPrivilege = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\PrivilegeDao");
                    $daoRolePrivilege = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\RolePrivilegeDao");
                    $daoRolePrivilege->deleteByRole($obj);
                    $resources = $data->resources;
                    foreach($resources as $re => $v){
                        if(is_array($v)){
                            foreach($v as $pr){
                                $resource                   = new Resource;
                                $resource->id               = $re;
                                $privilege                  = new Privilege;
                                $privilege->id              = $pr;
                                $obj->addPrivilege($daoResource->findById($resource), $daoPrivilege->findById($privilege));
                            }
                        }
                    }
                    //

                }

            }
            $this->getDao()->save($obj);
            $this->getDao()->commit();
    		
    	}catch(\Exception $e){
            $this->getDao()->rollback();
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
	public function deleteAction(Role $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Role;
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
    
    public function listAction(Role $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Role;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name = $this->getRequest()->getQuery()->get('query');
    		$rs 			= parent::listAction($obj);

            $rsA = [];
            $manager = $this->getServiceLocator()->get('Zend\Session\SessionManager');
            if($manager->getStorage()->role != 1){
                foreach($rs['root'] as $v){
                    if($v->id != 1)
                        $rsA[] = $v;
                }

                unset($rs['root']);
                $rs['root'] = $rsA;
                $rs['size'] = count($rsA);
            }

    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;
    	
    }
    
}
