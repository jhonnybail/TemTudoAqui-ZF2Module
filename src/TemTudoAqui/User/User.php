<?php

namespace TemTudoAqui\User;

use Doctrine\ORM\Mapping as ORM,
    TemTudoAqui\System,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException,
	TemTudoAqui\Common\Business,
	TemTudoAqui\Common\Hobbie,
    TemTudoAqui\Utils\Net\NetException;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_user_user")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"tta" = "User", "wecan" = "\WeCan\User\User"})
 * @ORM\HasLifecycleCallbacks
 */
class User extends Object {

    /**
     * Caminho para o diretório do avatar
     */
    const   AVATAR_DIR  = '/images/profile/';
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
	 * Nome do usuário.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected $name;
    
    /**
	 * Razão Social.
	 * @var string
	 * 
     * @ORM\Column(length=100)
     */
    protected $corporateName;
    
    /**
	 * Apelido.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected $nickname;
    
    /**
	 * Sobrenome.
	 * @var string
	 * 
     * @ORM\Column(length=70)
     */
    protected $surname;
    
    /**
	 * Registro Geral.
	 * @var string
	 * 
     * @ORM\Column(length=12)
     */
    protected $rg;
    
    /**
	 * Cadastro de Pessoa Física.
	 * @var string
	 * 
     * @ORM\Column(length=14)
     */
    protected $cpf;
    
    /**
	 * Cadastro Nacional de Pessoa Jurídica.
	 * @var string
	 * 
     * @ORM\Column(length=18)
     */
    protected $cnpj;
    
    /**
	 * Data de Nascimento.
	 * @var \TemTudoAqui\Utils\Data\DateTime
	 * 
     * @ORM\Column(type="date")
     */
    protected $birthDate;
    
    /**
	 * Sexo.
	 * @var string
	 * 
     * @ORM\Column
     */
    protected $gender;
    
    /**
	 * Caso pessoa jurídica, pessoa responsável.
	 * @var \TemTudoAqui\User\User
	 * 
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="idcontactperson", referencedColumnName="id")
     */
    protected $contactPerson;

    /**
     * @var \TemTudoAqui\Utils\Data\ImageFile
     *
     * @ORM\Column(type="tta_file")
     */
    protected $avatar;
    
	/**
	 * Caso pessoa fisíca, profissão dela.
	 * @var \TemTudoAqui\Common\Profession
	 * 
     * @ORM\ManyToOne(targetEntity="TemTudoAqui\Common\Profession")
     * @ORM\JoinColumn(name="idprofession", referencedColumnName="id")
     */
    protected $profession;
	
	/**
	 * Caso pessoa fisíca, escolaridade dela.
	 * @var \TemTudoAqui\Common\Schooling
	 * 
     * @ORM\ManyToOne(targetEntity="TemTudoAqui\Common\Schooling")
     * @ORM\JoinColumn(name="idschooling", referencedColumnName="id")
     */
    protected $schooling;
	
    /**
	 * Endereços do usuário.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\OneToMany(targetEntity="UserAddress", mappedBy="user", cascade={"persist","remove"})
     */
    protected $address;
    
    /**
	 * Telefones do usuário.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\OneToMany(targetEntity="Telephone", mappedBy="user", cascade={"persist","remove"})
     */
    protected $telephone;
    
    /**
	 * E-mails do usuário.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user", cascade={"persist","remove"})
     */
    protected $email;
	
	/**
	 * Atividades do usuário.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\ManyToMany(targetEntity="TemTudoAqui\Common\Business")
     * @ORM\JoinTable(name="tta_rel_user_business",
     *      joinColumns={@ORM\JoinColumn(name="iduser", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idbusiness", referencedColumnName="id")}
     *      )
     */
    protected $business;
	
	/**
	 * Hobbies do usuário.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\ManyToMany(targetEntity="TemTudoAqui\Common\Hobbie")
     * @ORM\JoinTable(name="tta_rel_user_hobbie",
     *      joinColumns={@ORM\JoinColumn(name="iduser", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idhobbie", referencedColumnName="id")}
     *      )
     */
    protected $hobbie;
    
    /**
	 * Permissões de acesso ao sistemas.
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * 
     * @ORM\OneToMany(targetEntity="UserAuth", mappedBy="user", cascade={"persist","remove"})
     */
    protected $permission;
    
    public function __construct(){
    	if(!empty($this->birthDate))
    		$this->birthDate = new \TemTudoAqui\Utils\Data\DateTime($this->birthDate->format(\DateTime::RFC3339), \DateTime::RFC3339);
		if(empty($this->address))
    		$this->address = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->telephone))
    		$this->telephone = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->email))
    		$this->email = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->site))
    		$this->site = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->business))
    		$this->business = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->hobbie))
    		$this->hobbie = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->persmission))
    		$this->persmission = new \Doctrine\Common\Collections\ArrayCollection();

        $avatar = (string)$this->avatar;
        if(!empty($avatar)){
            try{
                $this->avatar = new \TemTudoAqui\Utils\Data\ImageFile(new \TemTudoAqui\Utils\Net\URLRequest((string) System::GetVariable('DOCUMENT_ROOT').self::AVATAR_DIR.$this->avatar));
            }catch(NetException $e){
                if($e->getCode() == 6)
                    $this->avatar = '';
                else
                    throw $e;
            }
        }

    	parent::__construct();
    }
    
	/**
     * Adiciona um endereço na lista.
     *
     * @param  \TemTudoAqui\Common\Locality\Address	$address
     * @param  integer								$type
     * @return void
     */
    public function addAddress(\TemTudoAqui\Common\Locality\Address	$address, $type = UserAddress::TYPE_COMMERCIAL){
    	$ua = new UserAddress;
    	$ua->user = $this;
    	$ua->address = $address;
    	$ua->type = $type;
    	$this->address->add($ua);
    }
    
	/**
     * Retorna a lista de endereços.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAddresses(){
    	return $this->address;
    }
    
	/**
     * Adiciona um telefone na lista.
     *
     * @param  mixed			$tele
     * @param  integer|string	$ddd
     * @param  integer|string	$ddi
     * @param  integer			$type
     * @param  string			$description
     * @return void|Telephone
     */
    public function addTelephone($tele, $ddd = null, $ddi = null, $type = Telephone::TYPE_COMMERCIAL, $description = null){
    	if($tele instanceof Telephone){
    		$tele->user = $this;
    		$this->telephone->add($tele);
    		return $tele;
    	}elseif(is_string($tele) && is_int((int) $ddd) && !empty($ddd) && !empty($tele)){
    		$tel = new Telephone;
    		$tel->type = $type;
    		$tel->user = $this;
    		$tel->description = $description;
    		$tel->ddi = $ddi;
    		$tel->ddd = $ddd;
    		$tel->number = $tele;
    		$this->telephone->add($tel);
    		return $tel;
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 178, 'Telefone inválido');
    }
    
	/**
     * Retorna a lista de telefones.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTelephones(){
    	return $this->telephone;
    }
    
	/**
     * Adiciona um e-mail na lista.
     *
     * @param  mixed			$email
     * @return void|Email
     */
    public function addEmail($email){
    	if($email instanceof Email){
    		$email->user = $this;
    		$this->email->add($email);
    		return $email;
    	}elseif(is_string($email) && !empty($email)){
    		$em = new Email;
    		$em->user = $this;
    		$em->email = $email;
    		$this->email->add($em);
    		return $em;
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 178, 'E-mail inválido');
    }
    
	/**
     * Retorna a lista de e-mails.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEmails(){
    	return $this->email;
    }
    
	/**
     * Adiciona um cartão na lista.
     *
     * @param  string		$card
     * @return void|Card
     */
    public function addCard($card){
    	if($card instanceof Card){
    		$card->user = $this;
    		$this->card->add($card);
    		return $card;
    	}elseif(is_string($card) && !empty($card)){
    		$ca = new Card;
    		$ca->user = $this;
    		$ca->number = $card;
			$ca->active = true;
    		$this->card->add($ca);
    		return $ca;
    	}
    }
    
	/**
     * Retorna a lista de cartões.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCards(){
    	return $this->card;
    }
	
	/**
     * Adiciona uma atividade na lista.
     *
     * @param  \TemTudoAqui\Common\Business	$business
     * @return void
     */
    public function addBusiness(Business $business){
   		$this->business->add($business);
    }
    
	/**
     * Retorna a lista de atividades.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBusinesses(){
    	return $this->business;
    }
	
	/**
     * Adiciona uma atividade na lista.
     *
     * @param  \TemTudoAqui\Common\Business	$business
     * @return void
     */
    public function addHobbie(Hobbie $hobbie){
   		$this->hobbie->add($hobbie);
    }
    
	/**
     * Retorna a lista de hobbies.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getHobbies(){
    	return $this->hobbie;
    }
    
	/**
     * Adiciona uma permissão ao usuário.
     *
     * @param  \TemTudoAqui\User\Role			$role
     * @return \TemTudoAqui\User\UserAuth
     */
    public function addPermission(Role $role){
    	$ua 			= new UserAuth;
    	$ua->user 		= $this;
    	$ua->role       = $role;
   		$this->permission->add($ua);
   		return $ua;
    }
    
	/**
     * Retorna a lista de permissões.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPermissions(){
    	return $this->permission;
    }
    
    /**
     * Valida o CPF.
     *
     * @param  string	$cpf
     * @return boolean
     */
	public static function ValidCPF($cpf){
          
		$RecebeCPF = $cpf;
		       
		//Retirar todos os caracteres que nao sejam 0-9
		   
		$s = "";
		for($x = 1; $x <= strlen($RecebeCPF); $x++){
			$ch = substr($RecebeCPF, $x-1, 1);           
			if(ord($ch) >= 48 && ord($ch) <= 57)
				$s = $s.$ch;
		}
		   
		$RecebeCPF = $s;
		      
		$Numero[1] = intval(substr($RecebeCPF,1-1,1));
		$Numero[2] = intval(substr($RecebeCPF,2-1,1));
		$Numero[3] = intval(substr($RecebeCPF,3-1,1));
		$Numero[4] = intval(substr($RecebeCPF,4-1,1));
		$Numero[5] = intval(substr($RecebeCPF,5-1,1));
		$Numero[6] = intval(substr($RecebeCPF,6-1,1));
		$Numero[7] = intval(substr($RecebeCPF,7-1,1));
		$Numero[8] = intval(substr($RecebeCPF,8-1,1));
		$Numero[9] = intval(substr($RecebeCPF,9-1,1));
		$Numero[10] = intval(substr($RecebeCPF,10-1,1));
		$Numero[11] = intval(substr($RecebeCPF,11-1,1));
		   
		$soma = 10*$Numero[1]+9*$Numero[2]+8*$Numero[3]+7*$Numero[4]+6*$Numero[5]+5*
		$Numero[6]+4*$Numero[7]+3*$Numero[8]+2*$Numero[9];
		$soma = $soma-(11*(intval($soma/11)));
		
		if($soma == 0 || $soma == 1)
			$resultado1 = 0;
		else      
			$resultado1 = 11-$soma;
		        
		if($resultado1 == $Numero[10]){
		     
			$soma=$Numero[1]*11+$Numero[2]*10+$Numero[3]*9+$Numero[4]*8+$Numero[5]*7+$Numero[6]*6+$Numero[7]*5+
			$Numero[8]*4+$Numero[9]*3+$Numero[10]*2;
			$soma=$soma-(11*(intval($soma/11)));
		
			if($soma == 0 || $soma == 1)
				$resultado2 = 0;
			else
				$resultado2 = 11-$soma;
			          
			if($resultado2 == $Numero[11])
				return true;
			else        
				return false;
		      
		}else     
			return false;
  
	}
	
	/**
     * Valida o CNPJ.
     *
     * @param  string	$cnpj
     * @return boolean
     */
	public static function ValidCNPJ($cnpj){
		
		$cnpj = str_replace(".", "", $cnpj);
		$cnpj = str_replace("_", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace("/", "", $cnpj);
   
		$RecebeCNPJ = $cnpj;
		$s = "";
		          
		for($x = 1; $x <= strlen($RecebeCNPJ); $x++){
			$ch = substr($RecebeCNPJ,$x-1,1);
			if(ord($ch) >= 48 && ord($ch) <= 57)
				$s=$s.$ch;
		}
		
		$RecebeCNPJ=$s;
		   
		if(strlen($RecebeCNPJ) != 14)     
			return false;
		else{
		       
			$Numero[1] = intval(substr($RecebeCNPJ,1-1,1));
			$Numero[2] = intval(substr($RecebeCNPJ,2-1,1));
			$Numero[3] = intval(substr($RecebeCNPJ,3-1,1));
			$Numero[4] = intval(substr($RecebeCNPJ,4-1,1));
			$Numero[5] = intval(substr($RecebeCNPJ,5-1,1));
			$Numero[6] = intval(substr($RecebeCNPJ,6-1,1));
			$Numero[7] = intval(substr($RecebeCNPJ,7-1,1));
			$Numero[8] = intval(substr($RecebeCNPJ,8-1,1));
			$Numero[9] = intval(substr($RecebeCNPJ,9-1,1));
			$Numero[10] = intval(substr($RecebeCNPJ,10-1,1));
			$Numero[11] = intval(substr($RecebeCNPJ,11-1,1));
			$Numero[12] = intval(substr($RecebeCNPJ,12-1,1));
			$Numero[13] = intval(substr($RecebeCNPJ,13-1,1));
			$Numero[14] = intval(substr($RecebeCNPJ,14-1,1));
			
			$soma = $Numero[1]*5+$Numero[2]*4+$Numero[3]*3+$Numero[4]*2+$Numero[5]*9+$Numero[6]*8+$Numero[7]*7+
			$Numero[8]*6+$Numero[9]*5+$Numero[10]*4+$Numero[11]*3+$Numero[12]*2;
			
			$soma = $soma-(11*(intval($soma/11)));
		
			if($soma == 0 || $soma == 1)		  
				$resultado1 = 0;
			else
				$resultado1 = 11-$soma;
				  
			if($resultado1 == $Numero[13]){
			    
				$soma = $Numero[1]*6+$Numero[2]*5+$Numero[3]*4+$Numero[4]*3+$Numero[5]*2+$Numero[6]*9+
				$Numero[7]*8+$Numero[8]*7+$Numero[9]*6+$Numero[10]*5+$Numero[11]*4+$Numero[12]*3+$Numero[13]*2;
				$soma = $soma-(11*(intval($soma/11)));
			
				if($soma == 0 || $soma == 1)
					$resultado2 = 0;
				else			 
					$resultado2 = 11-$soma;
		   		
				if($resultado2 == $Numero[14])
					return true;
				else			 
					return false;
			}else		  
				return false;
						
		}
		  
	}
	
	public function __set($property, $value){
	 	if($property == 'cpf'){
	 		if(!self::ValidCPF((string)$value) && !empty($value))
	 			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 370, 'CPF inválido');
	 		else
	 			$this->cpf = $value; 
	 	}elseif($property == 'cnpj'){
	 		if(!self::ValidCNPJ($value) && !empty($value))
	 			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 370, 'CNPJ inválido');
	 		else
	 			$this->cnpj = $value; 
	 	}elseif($property == 'contactPerson'){
	 		if(empty($value))
	 			$this->contactPerson = null;
	 		else
	 			$this->contactPerson = $value; 
	 	}elseif($property == 'profession'){
	 		if(empty($value))
	 			$this->profession = null;
	 		else
	 			$this->profession = $value; 
	 	}elseif($property == 'schooling'){
	 		if(empty($value))
	 			$this->schooling = null;
	 		else
	 			$this->schooling = $value; 
	 	}else
	 		parent::__set($property, $value);
	 }
    
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
        $avatar = (string)$this->avatar;
        if(!empty($avatar)){
            try{
                $this->avatar = new \TemTudoAqui\Utils\Data\ImageFile(new \TemTudoAqui\Utils\Net\URLRequest((string) System::GetVariable('DOCUMENT_ROOT').self::AVATAR_DIR.$this->avatar));
            }catch(NetException $e){
                if($e->getCode() == 6)
                    $this->avatar = '';
                else
                    throw $e;
            }
        }
		parent::postLoad();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String(!empty($this->corporateName) ? $this->corporateName : $this->name." ".$this->surname);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}