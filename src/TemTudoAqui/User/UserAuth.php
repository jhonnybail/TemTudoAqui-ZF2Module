<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
    TemTudoAqui\System,
    Zend\Mail\Message,
    Zend\Mail\Transport\Sendmail,
    Zend\Mime\Message as MimeMessage,
    Zend\Mime\Part as MimePart;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_userauth")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"tta" = "UserAuth", "wecan" = "\WeCan\User\UserAuth"})
 * @ORM\HasLifecycleCallbacks
 */
class UserAuth extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @ORM\GeneratedValue
     */
    protected 	$id;

	/**
	 * Usuário.
	 * @var \TemTudoAqui\User\User
	 *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="permission", cascade={"persist"})
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     */
    protected 	$user;
    
   	/**
	 * Recursos.
	 * @var \TemTudoAqui\User\Role
	 * 
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="idrole", referencedColumnName="id")
     */
    protected 	$role;

    /**
	 * Privilégios.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
     * @ORM\OneToMany(targetEntity="UserPrivilege", mappedBy="userAuth", cascade={"persist","remove"})
     */
    protected $privilege;

    /**
     * Usuário para acesso.
     * @var string
     *
     * @ORM\Column(length=30)
     */
    protected 	$username;

    /**
     * Senha para acesso.
     * @var string
     *
     * @ORM\Column(length=30)
     */
    protected 	$password;

    /**
     * Senha para confirmação.
     * @var string
     *
     * @ORM\Column(length=30)
     */
    protected 	$password2;

    /**
     * Data e Hora do registro.
     * @var date
     *
     * @ORM\Column(type="datetime")
     */
    protected 	$registrationDate;
    
    public function __construct(){
    	if(empty($this->privilege))
    		$this->privilege = new \Doctrine\Common\Collections\ArrayCollection();
    	parent::__construct();
    }
    
	/**
     * Adiciona um privilégio na lista.
     *
     * @param  \TemTudoAqui\User\Resource	$resource
     * @param  \TemTudoAqui\User\Privilege	$privilege
     * @return void
     */
    public function addPrivilege(Resource $resource, Privilege $privilege){
    	$userPrivilege = new UserPrivilege;
        $userPrivilege->userAuth = $this;
        $userPrivilege->resource = $resource;
        $userPrivilege->privilege = $privilege;
        $this->privilege->add($userPrivilege);
    }
    
	/**
     * Retorna a lista de privilegios.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrivilegies(){
    	return $this->privilege;
    }

    public function sendRegisterEmail(){

        if($this->role->id > 0)
            $nivel = $this->role->name;
        else
            $nivel = 'Usuário Personalizado';

        $htmlMarkup = "
                    <p>Caro(a) {$this->user->name},</p>
                    <p>foi realizado um cadastro de usuário de nível {$nivel} no sistema ".System::GetVariable('companyName').",</p>
                    <p>acesse o link abaixo para registrar seus dados de acesso:</p>
                    <p><a href='".System::GetVariable('url')."register?idauth={$this->id}'>".System::GetVariable('url')."register?idauth={$this->id}</a></p>
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

        if($this->user->getEmails()->count() > 0){

            foreach($this->user->getEmails() as $email){
                $message
                    ->setEncoding("UTF-8")
                    ->addFrom((string)System::GetVariable('technicalEmail'), (string)System::GetVariable('companyName'))
                    ->addTo($email->email)
                    ->setSubject("Registro de Dados de Acesso");

                $transport = new Sendmail;
                $transport->send($message);
            }

        }

    }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->username." ({$this->user})".(!is_null($this->enterprise) ? ": ".$this->enterprise : ""));
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}