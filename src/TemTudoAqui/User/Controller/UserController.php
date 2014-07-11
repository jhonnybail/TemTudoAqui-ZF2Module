<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
	TemTudoAqui\User\User,
	TemTudoAqui\Common\Locality\Address,
	TemTudoAqui\Common\Profession,
	TemTudoAqui\Common\Schooling,
	TemTudoAqui\Common\Business,
	TemTudoAqui\Common\Hobbie,
	TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Utils\Data\Number,
	TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Data\DateTime,
    TemTudoAqui\Utils\Net\FileReference,
    TemTudoAqui\Utils\Net\URLRequest,
    TemTudoAqui\Utils\Data\ImageFile,
    TemTudoAqui\Exception,
    TemTudoAqui\System,
    TemTudoAqui\Utils\Net\NetException;

class UserController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\UserDao");
    }

	public function saveAction(User &$obj = null)
    {
		try{
			
			$this->getDao()->beginTransaction();

            if(is_null($obj))
    		    $obj 		= new User;
    		$rs 			= parent::saveAction($obj);  

    		if($this->getRequest()->isPost()){
    			
		    	$post 		= $this->getRequest()->getPost();
		    	$get 		= $this->getRequest()->getQuery();
		    	
		    	$obj 		= $this->getDao()->findById($obj);
		    	
		    	if($get->get('input') == 'jsonextjs'){
		    		
		    		$data 		= \Zend\Json\Decoder::decode($post->get('root'));
		    		
		    		//Endereços
		    		$addresses	= $data->addresses;
					$adDao			= $this->getServiceLocator()->get('TemTudoAqui\Common\Locality\Dao\AddressDao');

                    if(!empty($addresses)){
                        foreach($addresses as $addressN){

                            $ad 			= new Address;
                            $ad->zipCode	= $addressN->address->zipCode;
                            $ad->number		= $addressN->address->number;
                            $ad->complment	= $addressN->address->complement;
                            $rsAd			= $adDao->find($ad);

                            if($rsAd->count() > 0)
                                $ad = $rsAd[0];

                            if(!@$addressN->remove){


                                $ad->street				= $addressN->address->street;
                                $ad->neighborhood		= $addressN->address->neighborhood;

                                if($ad->city->id != $addressN->address->city->id){
                                    $dao 					= $this->getServiceLocator()->get('TemTudoAqui\Common\Locality\Dao\CityDao');
                                    $ad->city->id			= $addressN->address->city->id;
                                    $ad->city				= $dao->findById($ad->city);
                                }

                                if($ad->state->id != $addressN->address->state->id){
                                    $dao 					= $this->getServiceLocator()->get('TemTudoAqui\Common\Locality\Dao\StateDao');
                                    $ad->state->id			= $addressN->address->state->id;
                                    $ad->state				= $dao->findById($ad->state);
                                }

                                if($ad->country->id != $addressN->address->country->id){
                                    $dao 					= $this->getServiceLocator()->get('TemTudoAqui\Common\Locality\Dao\CountryDao');
                                    $ad->country->id		= $addressN->address->country->id;
                                    $ad->country			= $dao->findById($ad->country);
                                }

                                $adDao->save($ad);

                                $add = true;
                                foreach($obj->getAddresses() as $adN)
                                    if($adN->address->id == $ad->id){
                                        $adN->type = $addressN->type;
                                        $add = false;
                                        break;
                                    }
                                if($add)
                                    $obj->addAddress($ad, $addressN->type);

                            }else{
                                $obj->getAddresses()->removeElement($ad);
                            }

                        }
                    }
		    		
		    		//
		    		
		    		//Telefones
		    		$telephones	= new ArrayObject(\Zend\Json\Decoder::decode(\Zend\Json\Encoder::encode($data->telephones)));

                    if(!empty($telephones)){
                        foreach($telephones as $telephoneN){

                            if($telephoneN->id == 0){

                                $obj->addTelephone($telephoneN->number, $telephoneN->ddd, $telephoneN->ddi, $telephoneN->type, $telephoneN->description);

                            }else{

                                foreach($obj->getTelephones() as $telephone){

                                    if($telephone->id == $telephoneN->id){

                                        if(!@$telephoneN->remove){

                                            $telephone->type 		= $telephoneN->type;
                                            $telephone->description = $telephoneN->description;
                                            $telephone->ddi			= $telephoneN->ddi;
                                            $telephone->ddd			= $telephoneN->ddd;
                                            $telephone->number		= $telephoneN->number;

                                        }else
                                            $this->getDao()->delete($telephone);

                                    }

                                }

                            }

                        }
                    }
		    		//
		    		
		    		//E-mails
		    		$emails	= new ArrayObject(\Zend\Json\Decoder::decode(\Zend\Json\Encoder::encode($data->emails)));

                    if(!empty($emails)){
                        foreach($emails as $emailN){

                            if($emailN->id == 0){

                                $obj->addEmail($emailN->email);

                            }else{

                                foreach($obj->getEmails() as $email){

                                    if($email->id == $emailN->id){

                                        if(!$emailN->remove)
                                            $email->email	= $emailN->email;
                                        else
                                            $this->getDao()->delete($email);

                                    }

                                }

                            }

                        }
                    }
		    		//
					
					//Business
					$businesss = $data->businesss;
		    		$dao = $this->getServiceLocator()->get("TemTudoAqui\Common\Dao\BusinessDao");
                    if(!empty($businesss)){
                        foreach($businesss as $businessN){
                            $exists = false;
                            foreach($obj->getBusinesses() as $business){

                                if($business->id == $businessN->id){
                                    $exists = true;
                                    if(isset($businessN->remove)){
                                        if($businessN->remove)
                                            $obj->getBusinesses()->removeElement($business);
                                    }

                                }

                            }

                            if(!$exists){

                                $business = new Business;
                                $business->id = $businessN->id;
                                $business = $dao->findById($business);
                                $obj->addBusiness($business);

                            }

                        }
                    }
					//
					
					//Hobbie
					$hobbies = $data->hobbies;
		    		$dao = $this->getServiceLocator()->get("TemTudoAqui\Common\Dao\HobbieDao");

                    if(!empty($hobbies)){
                        foreach($hobbies as $hobbieN){
                            $exists = false;
                            foreach($obj->getHobbies() as $hobbie){

                                if($hobbie->id == $hobbieN->id){
                                    $exists = true;
                                    if(isset($hobbieN->remove)){
                                        if($hobbieN->remove)
                                            $obj->getHobbies()->removeElement($hobbie);
                                    }

                                }

                            }

                            if(!$exists){

                                $hobbie = new Hobbie;
                                $hobbie->id = $hobbieN->id;
                                $hobbie = $dao->findById($hobbie);
                                $obj->addHobbie($hobbie);

                            }

                        }
                    }
					//
		    		
		    	}
		    	
		    	$this->getDao()->save($obj);
		    	
    		}

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

    public function saveAvatarAction(){

        try{

            if(true){

                $file       = $this->getRequest()->getFiles()->get("image");
                $img        = new ImageFile(new URLRequest($file['tmp_name']));
                $ex         = explode(".", $file['name']);
                $newName    = md5($img->fileName.date("YmdHis")).".".$ex[count($ex)-1];
                FileReference::Move($img, System::GetVariable('DOCUMENT_ROOT').User::AVATAR_DIR.$newName, true);

                $rs['file'] = System::GetVariable("REQUEST_SCHEME")."://".System::GetVariable("HTTP_HOST").User::AVATAR_DIR.$newName;
                $rs['success'] = true;

            }

        }catch(Exception $e){
            $rs['success'] = false;
            $rs['message'] = $e->getMessage();
        }

        return $this->encodeOutput($rs);

    }
    
	public function deleteAction(User $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new User;
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
    
    public function listAction(User $obj = null)
    {

        try{

            if(is_null($obj))
    		    $obj 		= new User;
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
    
    public function userpermissionAction(User $obj = null){
    	
    	try{
            if(is_null($obj))
    		    $obj 		= new User;
    		$obj->id		= $this->getRequest()->getQuery()->get('user');
    		$obj			= $this->getDao()->findById($obj);   
    		$rs['root'][0]	= $obj->toArray();
    		
    		foreach($obj->getPermissions() as $key => $value){
    			$rs['root'][0]['permission'][$key] = $value->toArray();

                if(!is_null($value->role)){
                    $rs['root'][0]['permission'][$key]['role']['resources'] = [];
                    if($value->role->getPrivilegies()->count() > 0){
                        foreach($value->role->getPrivilegies() as $v){
                            $rs['root'][0]['permission'][$key]['role']['resources'][$v->resource->type]['id'] = $v->resource->id;
                            $rs['root'][0]['permission'][$key]['role']['resources'][$v->resource->type]['privilegies'][$v->privilege->id] = $v->privilege->name;
                        }
                    }
                }else{
                    $rs['root'][0]['permission'][$key]['resources'] = [];
                    foreach($value->getPrivilegies() as $v){
                        $rs['root'][0]['permission'][$key]['resources'][$v->resource->type]['id'] = $v->resource->id;
                        $rs['root'][0]['permission'][$key]['resources'][$v->resource->type]['privilegies'][$v->privilege->id] = $v->privilege->name;
                    }
                }
    			unset($rs['root'][0]['permission'][$key]['user']);
    		}

    		$rs['size']		= 1;
    	}catch(\Exception $e){
    		$rs['sucess'] 	= false;
    		$rs['message'] 	= $e->getMessage();
    	}

        if(__CLASS__ == get_class($this))
            return $this->encodeOutput($rs);
        else
            return $rs;
    	
    }
    
	protected function convertFields(User $obj){
		
		if($obj->birthDate != null){
			$d = new DateTime($obj->birthDate, DateTime::EXT);
			$obj->birthDate = $d;
		}
		if($obj->cpf != null)
			$obj->cpf 	= new String(str_replace("_", "", str_replace("-", "", str_replace(".", "", $obj->cpf))));
		if($obj->cnpj != null)
			$obj->cnpj 	= new String(str_replace("_", "", str_replace("-", "", str_replace(".", "", str_replace("/", "", $obj->cnpj)))));

        if($obj->avatar != null){
            try{
                $obj->avatar = new ImageFile(new URLRequest($obj->avatar));
            }catch(NetException $e){
                if($e->getCode() == 6){
                    try{
                        $obj->avatar = new ImageFile(new URLRequest($_SERVER['DOCUMENT_ROOT'].User::AVATAR_DIR.$obj->avatar));
                    }catch(NetException $e2){
                        if($e2->getCode() == 6)
                            $obj->avatar = '';
                        else
                            throw $e2;
                    }
                }else
                    throw $e;
            }
        }

		if(is_integer($obj->contactPerson)){
			$contactPerson		= new User;
			$contactPerson->id	= $obj->contactPerson;
			$obj->contactPerson	= $contactPerson;
		}elseif($obj->contactPerson instanceof \stdClass){
			$contactPerson		= new User;
			$contactPerson->id	= $obj->contactPerson->id;
			$obj->contactPerson	= $this->getDao()->findById($contactPerson);
		}
		
		if(is_integer($obj->profession)){
			$profession			= new Profession;
			$profession->id		= $obj->profession;
			$obj->profession	= $profession;
		}elseif($obj->profession instanceof \stdClass){
			$dao 				= $this->getServiceLocator()->get("TemTudoAqui\Common\Dao\ProfessionDao");
			$profession			= new Profession;
			$profession->id		= $obj->profession->id;
			$obj->profession	= $dao->findById($profession);
		}
		
		if(is_integer($obj->schooling)){
			$schooling			= new Schooling;
			$schooling->id		= $obj->schooling;
			$obj->schooling		= $schooling;
		}elseif($obj->schooling instanceof \stdClass){
			$dao 				= $this->getServiceLocator()->get("TemTudoAqui\Common\Dao\SchoolingDao");
			$schooling			= new Schooling;
			$schooling->id		= $obj->schooling->id;
			$obj->schooling		= $dao->findById($schooling);
		}
		
		if(empty($obj->permission))
			$obj->permission = new \Doctrine\Common\Collections\ArrayCollection();
		
		return $obj;
		
	}
    
	protected function validateFields(User $obj){
		
		if($obj->name != null)
			$obj->name = "%".$obj->name."%";
		if($obj->surname != null)
			$obj->surname = "%".$obj->surname."%";
		if($obj->corporateName != null)
			$obj->corporateName = "%".$obj->corporateName."%";
		if($obj->birthDate != null){
			$d = DateTime::createFromFormat("d/m/Y", $obj->birthDate);
			$obj->birthDate = $d;
		}
		if($obj->cpf != null)
			$obj->cpf 	= str_replace("_", "", str_replace("-", "", str_replace(".", "", $obj->cpf)));
		if($obj->cnpj != null)
			$obj->cnpj 	= str_replace("_", "", str_replace("-", "", str_replace(".", "", str_replace("/", "", $obj->cnpj))));
		
		if($obj->profession instanceof Number){
			$profession			= new Profession;
			$profession->id		= $obj->profession->getValue();
			$obj->profession	= $profession;
		}elseif(is_integer($obj->profession)){
			$profession			= new Profession;
			$profession->id		= $obj->profession;
			$obj->profession	= $profession;
		}
		
		if($obj->schooling instanceof Number){
			$schooling			= new Schooling;
			$schooling->id		= $obj->schooling->getValue();
			$obj->schooling		= $schooling;
		}elseif(is_integer($obj->schooling)){
			$schooling			= new Schooling;
			$schooling->id		= $obj->schooling;
			$obj->schooling		= $schooling;
		}
			
		return $obj;
		
	}
    
}
