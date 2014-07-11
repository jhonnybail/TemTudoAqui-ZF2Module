<?php

namespace TemTudoAqui\Store\Controller;

use TemTudoAqui\Store\CategoryException,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\Store\Category,
	TemTudoAqui\Store\Product,
	TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Utils\Data\ArrayObject,
	Zend\Soap\Client;

class CategoryController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Store\Dao\CategoryDao");
    }

	public function saveAction(Category $obj = null)
    {
    	try{

            if(is_null($obj))
    		    $obj 		= new Category;
    		$rs				= new ArrayObject;
    		$get = $this->getRequest()->getQuery();
    		
    		if($this->getRequest()->isPost()){
    			
		    	$post 		= $this->getRequest()->getPost();
		    	
		    	if($get->get('input') == 'jsonextjs'){
		    		
		    		$data = \Zend\Json\Decoder::decode($post->get('root'));
		    		
		    		if(is_array($data)){
			    		
		    			$values = new ArrayObject;
		    			
		    			foreach($data as $value){
		    				
							$value = get_object_vars($value);
							$obj 		= new Category;
							
							foreach($value as $k => $v)
								if(!($k == 'site' && empty($v)))
									$obj->$k	= $v;
							
							if($value['parentId'] != 'NaN' && $value['parentId'] != '')
								$obj->parent = new String($value['parentId']);
							else
								$obj->parent = null;
							
							$obj = $this->convertFields($obj);
							
							$this->getDao()->save($obj);
							$obj->parentNode = $obj->parent->id;
							$obj->checked = false;
							$values->append($obj);
							
		    			}
		    			
		    			if(!empty($values))
		    				$rs = $values;
		    			
		    		}else{
		    			
		    			$obj 		= new Category;
		    			$data = get_object_vars($data);
								    			
		    			foreach($data as $k => $v)
		    				if(!($k == 'site' && empty($v)))
		    					$obj->$k	= $v;
		    			
		    			if($data['parentId'] != 'NaN' && $data['parentId'] != '')
		    				$obj->parent = new String($data['parentId']);
		    			else
		    				$obj->parent = null;
		    			
		    			$obj = $this->convertFields($obj);
		    			$this->getDao()->save($obj);
			    		$obj->node	= $obj->id;
						$obj->root	= $obj->id;
		    			$obj->parentNode = $obj->parent->id;
			    		if(empty($data['id'])) $rs = array($obj);
			    		
		    		}
		    	}
		    	
		    	if(!empty($rs))
		    		$rs['success'] = true;
		    	
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
    
	public function deleteAction(Category $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Category;
    		
    		$post = $this->getRequest()->getPost();
    		$data = \Zend\Json\Decoder::decode($post->get('root'));
    		$this->getDao()->beginTransaction();
    		if(is_array($data)){
    			
    			foreach($data as $value){

    				$obj  		= new Category;
    				$obj->id	= $value->id;
		    		$obj		= $this->getDao()->findById($obj);
    				if(!empty($obj))
		    			if($obj->getSubCategories()->count() > 0)
    						throw new CategoryException(22, __CLASS__, 105, 'A categoria não pode ser excluída devido ela possuir subcategorias');
    				
    				$this->getDao()->delete($obj);
		    		
    			}
    			
    		}else{
    			
		    	$obj->id 	= $data->id;
		    	$obj		= $this->getDao()->findById($obj);
		    	if(!empty($obj))
	    			if($obj->getSubCategories()->count() > 0)
	    				throw new CategoryException(22, __CLASS__, 105, 'A categoria não pode ser excluída devido ela possuir subcategorias');
    			
		    	$this->getDao()->delete($obj);
		    	
    		}
    		$this->getDao()->commit();
    		
    		$rs['success'] 	= true;
    		
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
    
    public function listAction(Category $obj = null)
    {
    	try{

            if(is_null($obj))
                $obj 		= new Category;
    		$recursive		= false;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name = $this->getRequest()->getQuery()->get('query');
			
			if($this->getRequest()->getQuery()->get('node') != '' && $this->getRequest()->getQuery()->get('node') != 'NaN')
				$obj->parent = $this->getRequest()->getQuery()->get('node');

			
			if($this->getRequest()->getQuery()->get('recursive')){	
				
				$idproduct = $this->getRequest()->getQuery()->get('product');
				
				function listCategories($cat = null, $idproduct = null){
					
					$con = $this->getServiceLocator()->get("Doctrine\ORM\EntityManager");
					
					$query = $con->getConnection()->executeQuery("SELECT *, (SELECT COUNT(*) FROM tta_rel_product_category rpc WHERE rpc.idcategory = c.id AND rpc.idproduct = '".$idproduct."') as products FROM tta_store_category c WHERE c.idcategoryparent ".($cat ? "= ".$cat['id'] : "is NULL"));
					
					$rs = array();
					while($c = $query->fetch()){
						 
						if($c['products'] > 0)
							$c['checked'] = true;
						$c['idcategoryparent'] = $cat;
						$c['root'] = listCategories($c, $idproduct);
						$rs[] = $c;
					}
					
					return $rs;
					
				}	
				
				$rs['root'] = listCategories(null, $idproduct);
				
			}else			
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
    
	public function listbyproductAction()
    {
    	try{
    		
    		$get 				= $this->getRequest()->getQuery();
    		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\Store\Dao\StoreDao");
    		
    		$obj 				= new Product;
    		$obj->id			= $get->get('product');
    		$obj 				= $dao->findById($obj);
    		$temp				= new ArrayObject;
    		
    		if($obj != null)
    			if($obj->getCategories() != null)
		    		foreach($obj->getCategories() as $p) 			
		    			$temp->append($p->toArray());
	    	
    		$rs = parent::listByArray($temp);
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);
    	
    }
    
    protected function convertFields(Category $obj){
		
		$dao		= $this->getDao();
		
		if(((string)$obj->parent) == 'NaN'){
			$obj->parent			= null;
		}elseif($obj->parent instanceof String){
    		$parent 				= (string)$obj->parent;
			$cat					= new Category;
			$cat->id				= (int)$parent;
			$obj->parent			= $this->getDao()->findById($cat);
		}elseif($obj->parent instanceof \stdClass){
			$cat					= new Category;
			$cat->id				= $obj->parent->id;
			$obj->parent			= $this->getDao()->findById($cat);
		}
		
		return $obj;
		
	}
    
	protected function validateFields(Category $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";

		if($obj->parent instanceof String){
			$parent 			= (string)$obj->parent;
			$cat				= new Category;
			$cat->id			= $parent;
			$obj->parent		= $cat;
		}elseif(is_integer($obj->parent)){
			$cat				= new Category;
			$cat->id			= $obj->parent;
			$obj->parent		= $cat;
		}elseif(!$obj->parent){
			$obj->parent		= new Category;
			$obj->parent->id	= -1;
		}
		
		return $obj;
		
	}
    
}
