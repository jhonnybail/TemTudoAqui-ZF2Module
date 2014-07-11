<?php

namespace TemTudoAqui\Common\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Hobbie,
	TemTudoAqui\User\User,
	TemTudoAqui\Utils\Data\ArrayObject;

class HobbieController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Dao\HobbieDao");
    }

	public function saveAction(Hobbie $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Hobbie;
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
    
	public function deleteAction(Hobbie $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Hobbie;
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
    
    public function listAction(Hobbie $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Hobbie;
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
	    		foreach($obj->getHobbies() as $hob) {
								
	    			$temp->append($hob->toArray());
				}
	    	
    		$rs = parent::listByArray($temp);
    		
    	}catch(\Doctrine\Common\Proxy\Exception\UnexpectedValueException $e){
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);
    	
    }
    
	protected function validateFields(Hobbie $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";
			
		return $obj;
		
	}
    
}
