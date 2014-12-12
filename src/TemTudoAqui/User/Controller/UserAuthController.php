<?php

namespace TemTudoAqui\User\Controller;

use TemTudoAqui\Common\Controller,
    TemTudoAqui\User\User,
    TemTudoAqui\User\Role,
    TemTudoAqui\User\Resource,
    TemTudoAqui\User\Privilege,
    TemTudoAqui\User\Email,
	TemTudoAqui\User\UserAuth,
	TemTudoAqui\Utils\Data\Number,
    TemTudoAqui\Utils\Data\DateTime,
    TemTudoAqui\Exception,
    TemTudoAqui\System,
    Zend\Mail\Message,
    Zend\Mail\Transport\Sendmail,
    Zend\Mime\Message as MimeMessage,
    Zend\Mime\Part as MimePart;

class UserAuthController extends Controller
{

    public function __construct(){
        $this->setDaoName("TemTudoAqui\User\Dao\UserAuthDao");
    }

	public function saveAction(UserAuth $obj = null)
    {
    	try{

            $this->getDao()->beginTransaction();

            if($this->getRequest()->isPost()){

                $post 		= $this->getRequest()->getPost();
                $get 		= $this->getRequest()->getQuery();

                if(is_null($obj))
                    $obj 	= new UserAuth;

                if($get->get('input') == 'jsonextjs'){

                    $data 		= \Zend\Json\Decoder::decode($post->get('root'));

                    if(!empty($data->id)){
                        $obj->id    = $data->id;
                        $obj        = $this->getDao()->findById($obj);

                        $password   = $obj->password;
                        $password2  = $obj->password2;
                    }

                    $rs 		= parent::saveAction($obj);

                    //Resources
                    if(empty($obj->role->id)){
                        $daoResource = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\ResourceDao");
                        $daoPrivilege = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\PrivilegeDao");
                        $daoUserPrivilege = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\UserPrivilegeDao");
                        $daoUserPrivilege->deleteByUserAuth($obj);
                        $resources = $data->resources;
                        if(!empty($resources)){
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
                        }
                    }
                    //

                    //Password
                    if(empty($data->password)){
                        $obj->password = @$password;
                    }
                    if(empty($data->password2)){
                        $obj->password2 = @$password2;
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
    
	public function deleteAction(UserAuth $obj = null)
    {
    	try{
            if(is_null($obj))
    		    $obj 		= new UserAuth;
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
    
    public function listAction(UserAuth $obj = null)
    {
    	try{

            if(is_null($obj))
                $obj 			= new UserAuth;
    		if($this->getRequest()->getQuery()->get('query'))
				$obj->name = $this->getRequest()->getQuery()->get('query');
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

    public function sendForgetAccessDataAction(){

        try{

            $rs = [];

            if($this->getRequest()->isPost()){

                $post 		    = $this->getRequest()->getPost();
                $em             = $post->get("email");

                $emailDao       = $this->getServiceLocator()->get("TemTudoAqui\User\Dao\EmailDao");
                $email          = new Email;
                $email->email   = $em;

                if($emailDao->hasEmail($email)){

                    $rs = $emailDao->find($email);
                    $email = $rs[0];

                    $htmlMarkup = "
                    <p>Caro(a) {$email->user->name},</p>
                    <p>conforme solicitado pelo nosso sistema, este é um e-mail informando seus dados de acesso ao sistema, abaixo encontra-se os dados:</p>
                    ";

                    foreach($email->user->getPermissions() as $userAuth)
                        $htmlMarkup .= '
                        <br />
                        ==================================================<br />
                        '.(is_null($userAuth->role) ? '' : "<strong>Função: </strong> {$userAuth->role->name}")."
                        <br />
                        <strong>Usuário: </strong> {$userAuth->username}<br />
                        <strong>Senha: </strong> {$userAuth->password}<br />
                        ";

                    $htmlMarkup .= '
                    <br />
                    ================
                    <br /><br />

                    Em caso de dúvidas, entre em contato conosco através do email '.System::GetVariable('technicalEmail').'
                    <br />'.
                    System::GetVariable('companyName').'<br />'.
                    System::GetVariable('url');

                    $html = new MimePart($htmlMarkup);
                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->setParts(array($html));

                    $message = new Message();
                    $message->setBody($body);

                    $message
                        ->setEncoding("UTF-8")
                        ->addFrom((string)System::GetVariable('technicalEmail'), (string)System::GetVariable('companyName'))
                        ->addTo((string)$email->email)
                        ->setSubject("Recuperação dos dados de Acesso");

                    $transport = new Sendmail;
                    $transport->send($message);

                    $rs['success'] = true;

                }else
                    throw new Exception(26, __FILE__, 141, 'E-mail não cadastrado!');

            }

        }catch(Exception $e){
            $rs['success'] 	= false;
            $rs['message'] 	= $e->getMessage();
        }

        return $this->encodeOutput($rs);

    }
    
	protected function convertFields(UserAuth $obj){

        if(!is_null($obj->registrationDate)){
            $d = new DateTime($obj->registrationDate, DateTime::EXT);
            $obj->registrationDate = $d;
        }else
            $obj->registrationDate = new DateTime;

        $dao 			= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\UserDao");
        if(is_integer($obj->user)){
            $user		= new User;
            $user->id	= $obj->user;
            $obj->user	= $dao->findById($user);
        }elseif($obj->user instanceof \stdClass){
            $user		= new User;
            $user->id	= $obj->user->id;
            $obj->user	= $dao->findById($user);
        }

        $dao 			= $this->getServiceLocator()->get("TemTudoAqui\User\Dao\RoleDao");
        if(is_integer($obj->role)){
            $role		= new Role;
            $role->id	= $obj->role;
            $obj->role	= $dao->findById($role);
        }elseif($obj->role instanceof \stdClass){
            $role		= new Role;
            $role->id	= $obj->role->id;
            $obj->role	= $dao->findById($role);
        }

		return $obj;
		
	}
    
	protected function validateFields(UserAuth $obj){

        if($obj->registrationDate != null){
            $d = DateTime::createFromFormat("d/m/Y", $obj->registrationDate);
            $obj->registrationDate = $d;
        }

        if($obj->user instanceof Number){
            $user			= new User;
            $user->id		= $obj->user->getValue();
            $obj->user		= $user;
        }elseif(is_integer($obj->user)){
            $user			= new User;
            $user->id		= $obj->user;
            $obj->user		= $user;
        }

        if($obj->role instanceof Number){
            $role			= new Role;
            $role->id		= $obj->role->getValue();
            $obj->role		= $role;
        }elseif(is_integer($obj->role)){
            $role			= new Role;
            $role->id		= $obj->role;
            $obj->role		= $role;
        }

			
		return $obj;
		
	}
    
}
