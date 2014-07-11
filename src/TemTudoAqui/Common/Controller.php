<?php

namespace TemTudoAqui\Common;

use Zend\Mvc\Controller\AbstractActionController,
	Zend\Mvc\MvcEvent,
	Zend\View\Model\JsonModel,
	Zend\View\Model\ViewModel,
	Zend\ServiceManager\Exception\ServiceNotFoundException,
	TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Object;

abstract class Controller extends AbstractActionController
{
	
	private $daoName;
    private $dao;
    private $resourceName;
    private $protectedActions   = [];

    protected function setDaoName($daoName){
        $this->daoName = $daoName instanceof String ? $daoName : new String($daoName);
        return $this;
    }

    protected function getDao(){
        return $this->dao;
    }

    protected function setResourceName($resourceName){
        $this->resourceName = $resourceName instanceof String ? $resourceName : new String($resourceName);
        return $this;
    }

    public function getResourceName(){
        return $this->resourceName;
    }

    protected function setProtectionAction($action){
        $this->protectedActions[$action] = true;
    }

    public function getProtectedActions(){
        return new ArrayObject($this->protectedActions);
    }
	
	public function onDispatch(MvcEvent $e){
		
		if(empty($this->daoName)){
            $daoName        = new String(get_class($this));
			$this->daoName  = $daoName->replace("Controller", "Dao");
		}
        if(empty($this->resourceName)){
            $resourceName       = new String(get_class($this));
            $names              = $resourceName->split("\\");
            $this->resourceName = $names->end()->replace("Controller", "")->toLowerCase();
        }

		try{
			$this->dao = $this->getServiceLocator()->get($this->daoName->toString());
		}catch(ServiceNotFoundException $ex){}

		return parent::onDispatch($e);
		
	}
		
    public function saveAction(Object &$obj)
    {
    	try{
    		
    		$class = $obj->GetReflection()->getName();
    		$get = $this->getRequest()->getQuery();
    		
    		if($this->getRequest()->isPost()){
    			
		    	$post 		= $this->getRequest()->getPost();
		    	
		    	if($get->get('input') == 'jsonextjs'){
					//\Zend\Json\Json::$useBuiltinEncoderDecoder = true;
					$data = \Zend\Json\Json::decode($post->get('root'));
		    		//var_dump($data);exit;
		    		if(is_array($data)){
			    		
		    			$values = new ArrayObject;
		    			
		    			foreach($data as $value){
		    				
		    				$value = get_object_vars($value);
		    				$obj 		= new $class;
		    				foreach($value as $k => $v)
		    					@$obj->$k	= $v;
		    					
				    		$this->dao->save($this->convertFields($obj));
		    				if(empty($values['id'])) $values->append($obj);
		    			}
		    			
		    			$rs['root'] = $values;
		    			
		    		}else{
		    			$obj 		= new $class;
		    			$data = get_object_vars($data);
						
		    			foreach($data as $k => $v){
							if(!($obj->$k instanceof \Doctrine\Common\Collections\ArrayCollection))
								@$obj->$k	= $v;
						}
		    			
		    			$obj = $this->convertFields($obj);

			    		$this->dao->save($obj);

			    		if(empty($data['id'])) $rs['root'] = array($obj);

		    		}
		    	}else{
		    		$post = $post->toArray();
		    		$obj = new $class;
		    		
					foreach($post as $k => $v)
		    			@$obj->$k 	= $v;
		    		
					$this->dao->save($this->convertFields($obj));
		    		if(!$post['id']) $rs['root'] = $obj;
		    	}
		    	
		    	$rs['success'] = true;
		    	
    		}
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 		= $e->getMessage();
    	}

    	return $rs;
    }
    
    public function deleteAction(Object $obj){
    	
    	try{
    		
    		$class = $obj->GetReflection()->getName();
    		$post = $this->getRequest()->getPost();
    		$data = \Zend\Json\Decoder::decode($post->get('root'));
    		if(is_array($data)){
    			
    			foreach($data as $value){
		    				
    				$obj 		= new $class;
    				$obj->id	= $value->id;
    				$obj		= $this->dao->findById($obj);
		    		$this->dao->delete($obj);
    			}
    			
    		}else{
    			$obj		= new $class;
		    	$obj->id 	= $data->id;
				$obj		= $this->dao->findById($obj);
		    	$this->dao->delete($obj);
    		}
    		
    		$rs['success'] 	= true;
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 		= $e->getMessage();
    	}
    	
    	return $rs;
    	
    }
    
	public function listAction(Object $obj)
    {
    	try{
			
    		$limit 	= null;
    		$start 	= null;
    		$order 	= null;
    		$dir 	= null;
    		
			$query = $this->getRequest()->getQuery();
			
    		if($query->get('input') == 'jsonextjs'){
		    	//pegando o filtro digitado
				$arrayFilter = \Zend\Json\Decoder::decode($query->get('filter'));
				if(!empty($arrayFilter)){
					foreach($arrayFilter as $filter){
						$property = $filter->property;
						@$obj->$property = $filter->value;
					}
				}
				
				if($query->get('limit')){
					$start = $query->get('start');
					$limit = $query->get('limit');
				}
				
				if($query->get('dir')){
					$order = $query->get('sort');
					$dir = $query->get('dir');
				}
    		}
			
    		$obj = $this->validateFields($obj);
    		if(get_class($this->dao) == 'TemTudoAqui\Common\Dao'){
    			$class = $obj->GetReflection()->getName();
    			$rsM = $this->dao->getTotalRows(new $class);
    		}else{
				$rsM = $this->dao->getTotalRows($obj);
			}
			//var_dump($rsM); exit;
    		$rs = $this->dao->find($obj, array($order => $dir), $limit, $start);
	    	//echo var_dump($rs);exit;
    		$rss['size'] = $rsM;
	    	$rss['root'] = $rs;
    	}catch(\Exception $e){
    		$rss['success'] = false;
    		$rss['message'] = $e->getMessage();
    	}
    	
    	return $rss;
    	
    }
    
    public function listByArray($list){
    	
    	try{
    		
    		$query 				= $this->getRequest()->getQuery();
    		$filters			= '';
    		$start		 		= '';
    		$limit				= '';
    		$order				= '';
    		$dir				= '';
			
    		$rs					= new ArrayObject;
    		$rs['root']			= new ArrayObject((array) $list);
    		
    		if($query->get('input') == 'jsonextjs'){
    			
    			//pegando o filtro digitado
				$arrayFilter = \Zend\Json\Decoder::decode($query->get('filter'));
				if(!empty($arrayFilter)){
					foreach($arrayFilter as $filter){
						$filters['f'][$filter->property] = $filter->value;
					}
				}
				
				if($query->get('limit')){
					$start = $query->get('start');
					$limit = $query->get('limit');
				}
				
				if($query->get('dir')){
					$order = $query->get('sort');
					$dir = $query->get('dir');
				}
				
    		}
    		
    		if(!empty($dir) && !empty($order)){
	    		
	    		if($dir == 'ASC')
	    			$dir = SORT_ASC;
	    		elseif($dir == 'DESC')
	    			$dir = SORT_DESC;
	    		else
	    			$dir = SORT_ASC;
	    			    		
	    		$rs['root'] = ArrayObject::ArrayOrderBy($rs['root'], $order, $dir);

	    	}
	    	
	    	if(!empty($filters['f'])){
	    		
	    		$filters['sel'] = new ArrayObject;
	    		
	    		$rs['root']->reset();
	    		$rs['root'] = (array) $rs['root'];
	    		foreach($rs['root'] as $arr){
	    			
	    			$valid = true;
	    			foreach($filters['f'] as $c => $f){
						if(is_array($f) || $f instanceof ArrayObject){
	    					$validArray = true;
	    					foreach($f as $key => $values){
								if(is_numeric($values)){
									if(is_float((float)$values)){
										if($arr[$c][$key] != $f || (float) $arr[$c][$key] > 0)
											$validArray = false;
									}else{
										if($arr[$c][$key] != $f)
											$validArray = false;
									}
			    				}elseif(is_string($values)){
			    					if(!preg_match("|".$values."|", $arr[$c][$key]))
			    						$validArray = false;
			    				}
			    				if(!$validArray) break;
	    					}
	    					$valid = $validArray;
	    				}elseif(is_numeric($f)){
							if(is_float((float)$f)){
								if(((float) $f) > 0)
									$valid = false;
							}elseif(is_array($arr[$c]) || $arr[$c] instanceof ArrayObject){
	    						if($arr[$c]['id'] != $f)
	    							$valid = false;
	    					}elseif($arr[$c] != $f)
	    						$valid = false;
	    				}else{
	    					if(!preg_match("|".(string)$f."|", $arr[$c]))
	    						$valid = false;
	    				}
	    				
	    				if(!$valid) break;
	    				
	    			}
	    			
	    			if($valid) $filters['sel']->append($arr);
	    			
	    		}
	    		
	    		unset($rs['root']);
	    		$rs['root'] = $filters['sel'];
	    		
	    	}
	    	
    		$rs['size'] = count($rs['root']);
    		
    		if(!empty($limit)){
    			
    			if(empty($start)) $start = 0;
    			
    			$temp = new ArrayObject;
    			for($i = $start; $i < ($start+$limit) && $i < $rs['root']->length(); $i++)
    				$temp->append($rs['root'][$i]);
    			
    			unset($rs['root']);
	    		$rs['root'] = $temp;	
    				
    		}
    		
    		$rs['success']	= true;
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}
    	
    	return $rs;
    	
    }
	
	protected function encodeOutput($value)
    {

        $query = $this->getRequest()->getQuery();
    	if($query->get('output') == 'json' && !empty($value)){
    		return new JsonModel($this->convertToArray($value));
    	}elseif($query->get('output') == 'jsonextjs' && !empty($value)){
    		echo $query->get('callback').'('.\Zend\Json\Encoder::encode($this->convertToArray($value)).');';
    		exit;
    	}else{
			return new ViewModel($value);
		}
    	
    }
	
    protected function convertToArray($value)
    {   
    	if($value instanceof ArrayObject || is_array($value)){
    		$value = (array) $value;
    		foreach($value as $key => $v){
    			if($v instanceof \TemTudoAqui\Object || $v instanceof ArrayObject || is_array($v))
    				$value[$key] = $this->convertToArray($v);    				
    		}
    	}elseif($value instanceof \TemTudoAqui\Object)
			$value = $value->toArray();
			
    		
    	return (array)$value;
    	
    }
    
	protected function convertFields(Object $obj){
		return $obj;
	}
    
    protected function validateFields(Object $obj){
		return $obj;
	}
    
}
