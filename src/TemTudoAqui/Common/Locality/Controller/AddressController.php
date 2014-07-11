<?php

namespace TemTudoAqui\Common\Locality\Controller;

use Zend\Mvc\Controller\ActionController,
	TemTudoAqui\Common\Controller,
	TemTudoAqui\User\User,
	TemTudoAqui\Common\Locality\City,
	TemTudoAqui\Common\Locality\State,
	TemTudoAqui\Common\Locality\Country,
	TemTudoAqui\Common\Locality\Address,
	TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Net\URLRequest,
	TemTudoAqui\Utils\Data\File,
	TemTudoAqui\Exception,
	TemTudoAqui\Utils\Data\String;

class AddressController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\Common\Locality\Dao\AddressDao");
    }

    public function saveAction(Address $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new Address;
    		$rs 			= parent::saveAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
	public function deleteAction(Address $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		= new Address;
    		$rs 			= parent::deleteAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;

    }
    
    public function listAction(Address $obj = null)
    {
    	try{
            if(is_null($obj))
                $obj 		    = new Address;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->zipCode 	= $this->getRequest()->getQuery()->get('query');
    		$rs 				= parent::listAction($obj);    		
    	}catch(\Exception $e){
    		$rs['success'] 		= false;
    		$rs['message']	 	= $e->getMessage();
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
	    		foreach($obj->getAddresses() as $address) 			
	    			$temp->append($address->toArray());
	    	
    		$rs = parent::listByArray($temp);
    		
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	 = $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);
    	
    }
	
	protected function getAddressByZipCodeAction(){
		
		try{
			
			$result = array();
    		$query 	= $this->getRequest()->getQuery();
			
			try{
				
				$con 	= $this->getServiceLocator()->get("doctrine.connection.orm_correios");
				
				$result = $con->fetchAll("SELECT CONCAT(l.LOG_TIPO_LOGRADOURO, ' ', l.LOG_NO) as logradouro, (SELECT c.LOC_NO FROM tta_correios.log_localidade c WHERE c.LOC_NU_SEQUENCIAL = l.LOC_NU_SEQUENCIAL) as cidade, l.LOC_NU_SEQUENCIAL as idcidade, l.UFE_SG as uf, (SELECT e.UFE_NO FROM tta_correios.log_faixa_uf e WHERE e.UFE_SG = l.UFE_SG) as estado, (SELECT b.BAI_NO FROM tta_correios.log_bairro b WHERE b.BAI_NU_SEQUENCIAL = l.BAI_NU_SEQUENCIAL_INI) as bairro FROM tta_correios.log_logradouro l WHERE l.CEP = '".str_replace("-", '', $query->get("zipcode"))."'");
				if(count($result) == 0){
					$result = $con->fetchAll("SELECT '' as logradouro, c.LOC_NO as cidade, c.LOC_NU_SEQUENCIAL as idcidade, c.UFE_SG as uf, (SELECT e.UFE_NO FROM tta_correios.log_faixa_uf e WHERE e.UFE_SG = c.UFE_SG) as estado, '' as bairro FROM tta_correios.log_localidade c WHERE c.CEP = '".str_replace("-", '', $query->get("zipcode"))."'");
					if(count($result) == 0)
						throw new Exception(26, __CLASS__, 138, 'ZipCode não existe');					
				}
			
			}catch(\Exception $e){
				$url = new URLRequest("http://www.temtudoaqui.info/webservice/locate/getEnderecoByCEP/".$query->get("zipcode"));
				$file = new File($url);
				$file->open();
				$data = \Zend\Json\Json::decode($file->data, 1);
				if($data['status'] == 'false')
					throw new Exception(26, __CLASS__, 138, 'ZipCode não existe');
				$result[0] = $data['root'];
			}
			
			$em 				= $this->getServiceLocator()->get("Doctrine\ORM\EntityManager");
			$con 				= $em->getConnection();
			
			$address 			= new Address;
			$address->street	= $result[0]['logradouro'];
			$address->neighborhood	= $result[0]['bairro'];
			$address->zipCode	= $query->get("zipcode");
						
			$countryDao	= $this->getServiceLocator()->get("TemTudoAqui\Common\Locality\Dao\CountryDao");
			$country	= new Country;
			$smtp = $con->prepare("SELECT c.* FROM tta_common_locality_country c WHERE UPPER(c.name) = UPPER('Brasil')");
			$smtp->execute();
			$rsC = $smtp->fetchAll();			
			if(count($rsC) > 0){
				$country->id 	= $rsC[0]['id'];
				$country		= $countryDao->findById($country);
			}else{
				$country->name 	= (string) String::GetInstance('Brasil')->toUpperCaseFirstChars();
				$countryDao->save($country);
			}
			
			$address->country	= $country;
			
			$stateDao 			= $this->getServiceLocator()->get("TemTudoAqui\Common\Locality\Dao\StateDao");
			$state 				= new State;
			$smtp 				= $con->prepare("SELECT s.id FROM tta_common_locality_state s WHERE UPPER(s.name) = UPPER('".$result[0]['estado']."')");
			$smtp->execute();
			$rsE = $smtp->fetchAll();
			if(count($rsE) > 0){
				$state->id 	= $rsE[0]['id'];
				$state		= $stateDao->findById($state);
			}else{
				$state->name 	= (string) String::GetInstance($result[0]['estado'])->toUpperCaseFirstChars();
				$state->uf		= strtoupper($result[0]['uf']);
				$state->country	= $country;
				$stateDao->save($state);
			}
				
			$cityDao 	= $this->getServiceLocator()->get("TemTudoAqui\Common\Locality\Dao\CityDao");
			$city 		= new City;
			$smtp = $con->prepare("SELECT c.* FROM tta_common_locality_city c WHERE UPPER(c.name) = UPPER('".$result[0]['cidade']."')");
			$smtp->execute();
			$rsC = $smtp->fetchAll();			
			if(count($rsC) > 0){
				$city->id 	= $rsC[0]['id'];
				$city		= $cityDao->findById($city);
			}else{
				$con->insert("tta_common_locality_city", array('id' => $result[0]['idcidade'], 'idstate' => $state->id));
				$city->id		= (int)$result[0]['idcidade'];
				$city->name 	= (string) String::GetInstance($result[0]['cidade'])->toUpperCaseFirstChars();
				$city->state	= $state;
				$city->country	= $country;
				$cityDao->save($city);
			}
			
			$address->state		= $state;
			$address->city		= $city;
    		
			$rs['success'] 	= true;
			$rs['root'][0]	= $address;
			
    	}catch(\Exception $e){
    		$rs['success'] 	= false;
    		$rs['message']	= $e->getMessage();
    	}
    	
    	return $this->encodeOutput($rs);
		
	}
    
	protected function convertFields(Address $obj){
		
		$cityDao 		= $this->getServiceLocator()->get("TemTudoAqui\Common\Locality\Dao\CityDao");
		$city			= new City;
		$city->id 		= $obj->city instanceof \stdClass ? $obj->city->id : (int) $obj->city;
		$city			= $cityDao->findById($city);
		
		$obj->city		= $city;
		$obj->state		= $city->state;
		$obj->country	= $city->state->country;
		
		return $obj;
		
	}
    
	protected function validateFields(Address $obj){
		
		if($obj->zipCode != null)
			$obj->zipCode 		= "%".$obj->zipCode."%";
		if(is_integer($obj->city)){
			$city			= new City;
			$city->id		= $obj->city;
			$obj->city		= $city;
		}
		if(is_integer($obj->state)){
			$state			= new State;
			$state->id		= $obj->state;
			$obj->state		= $state;
		}
		if(is_integer($obj->country)){
			$country		= new Country;
			$country->id	= $obj->country;
			$obj->country	= $country;
		}
		
		return $obj;
		
	}
    
}
