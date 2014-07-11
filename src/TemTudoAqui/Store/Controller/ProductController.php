<?php

namespace TemTudoAqui\Store\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\Common\Dao,
	TemTudoAqui\Store\Product,
	TemTudoAqui\Store\Dao\ProductImageDao,
	TemTudoAqui\Store\Category,
	TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Data\ImageFile,
	TemTudoAqui\Utils\Net\URLRequest,
	Zend\Soap\Client;

class ProductController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Store\Dao\ProductDao");
    }

	public function saveAction(Product $obj = null)
    {
    	try{

            if(is_null($obj))
    		    $obj 		= new Product;
    		$rs 			= parent::saveAction($obj); 	
			
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
		    			
						$p 		= $obj;
		    			$data 	= get_object_vars($data);
						
		    			if(!empty($data['categories'])){
							
							$categoryDao = $this->getServiceLocator()->get("TemTudoAqui\Store\Dao\CategoryDao");
							
							foreach($data['categories'] as $v){
								
								$cat 		= new Category;
								$cat->id	= $v->id;
								$cat		= $categoryDao->findById($cat);
								
								if($v->checked)
									$p->addCategory($cat);
								else
									$p->getCategories()->removeElement($cat);
								
							}
							
							$this->getDao()->save($p);
							
						}
			    		
		    		}
		    	}
		    	
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
    
	public function deleteAction(Product $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Product;
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
    
	public function listAction(Product $obj = null)
    {
    	
		try{

            if(is_null($obj))
                $obj 		= new Product;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name = $this->getRequest()->getQuery()->get('query'); 
			
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
    
	public function listbycategoryAction()
    {
    	try{
    		
    		$get 				= $this->getRequest()->getQuery();
    		$dao 				= $this->getServiceLocator()->get("TemTudoAqui\Store\Dao\CategoryDao");
    		
    		$obj 				= new Category;
    		$obj->id			= $get->get('category');
    		$obj 				= $dao->findById($obj);
    		$temp				= new ArrayObject;
    		
    		if($obj != null)
    			if($obj->getProducts() != null)
		    		foreach($obj->getProducts() as $cat) 			
		    			$temp->append($cat->toArray());
	    	
    		$rs = parent::listByArray($temp);
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);
    	
    }
	
	public function saveimageAction(){
		
		try{
    		
			$files = $this->getRequest()->file();
			
			if($files->count() > 0){
				$obj 		= new Product;
				$obj->id	= $this->getRequest()->getQuery()->get('product');
				$obj		= $this->getDao()->findById($obj);
				
				$img		= new ImageFile(new URLRequest($files['file']['tmp_name']));
				$img->open();
				
				$div1 = explode(".", $files['file']['name']);
				$new = $div1[0];
				for($i = 1; $i < count($div1)-1; $i++)
					$new .= ".".$div1[$i];
					
				$name 		= $new;
				$extension	= $div1[count($div1)-1];
				
				$img->fileName 	= date("YmdHis")."-".$name;
				$img->extension	= $extension;
				
				$obj->addImage($img);
				
				$this->getDao()->save($obj);
				
				$rs['success'] 	= true;
				
			}
			
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);
		
	}
	
	public function listimageAction(){
		
		try{
    		
    		$get 				= $this->getRequest()->getQuery();
			$post				= $this->getRequest()->getPost();
    		
    		$obj 				= new Product;
    		$obj->id			= $get->get('product');
    		$obj 				= $this->getDao()->findById($obj);
    		
			if($this->getRequest()->isPost()){
				
				$images = new ArrayObject;
				
				if($get->get('input') == 'jsonextjs'){
					
					$data = \Zend\Json\Decoder::decode($post->get('root'));
					
					if(is_array($data)){
		    			
		    			foreach($data as $value){
		    				
							if(property_exists($value, "edit")){
								if($value->edit){
									foreach($obj->getImages() as $img){
										
										if($img->id == $value->id){
											$img->description 	= $value->description;
											$img->imageDefault 	= $value->imageDefault;
										}
										
									}
								}
							}
							
							if(property_exists($value, "remove")){
								if($value->remove){
									foreach($obj->getImages() as $img){
										
										if($img->id == $value->id){
											$obj->getImages()->removeElement($img);
											$this->getDao()->delete($img);
										}
										
									}
								}
							}
							
		    			}
						
						$rs['success'] = true;
		    			
		    		}else{
		    			
						if(property_exists($data, "edit")){
							if($data->edit){
								
								foreach($obj->getImages() as $img){
										
									if($img->id == $data->id){
										$img->description 	= $data->description;
										$img->imageDefault 	= $data->imageDefault;
									}
									
								}
							}
						}
						
						if(property_exists($data, "remove")){
							if($data->remove){
								
								foreach($obj->getImages() as $img){
										
									if($img->id == $data->id){
										$obj->getImages()->removeElement($img);
										$this->getDao()->delete($img);
									}
									
								}
							}
						}
						
						$rs['success'] = true;
						
		    		}
					
				}
				
				$this->getDao()->save($obj);
				
				
			}else{
			
				$temp				= new ArrayObject;
				
				if($obj != null)
					if($obj->getImages() != null)
						foreach($obj->getImages() as $img) 			
							$temp->append($img->toArray());
				
				$rs = parent::listByArray($temp);
			
			}
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);		
	}
    
    protected function convertFields(Product $obj){

		if((float)((string)$obj->lengthc) > 0)
			$obj->length = $obj->lengthc;
		
		return $obj;
		
	}
    
	protected function validateFields(Product $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";


		if((float)((string)$obj->lengthc) > 0)
			$obj->length = $obj->lengthc;
		
		return $obj;
		
	}
	
}
