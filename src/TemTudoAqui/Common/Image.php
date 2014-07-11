<?php

namespace TemTudoAqui\Common;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\System,
	TemTudoAqui\Utils\Data\ImageFile,
	TemTudoAqui\Utils\Net\URLRequest,
	TemTudoAqui\Utils\Net\FileReference;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_image")
 * @ORM\HasLifecycleCallbacks
 */
class Image extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * Descrição da Imagem.
     * @var string
     * @ORM\Column(length=255) 
     */
    protected $description;
	
	/** 
     * Arquivo da imagem.
     * @var string
     * @ORM\Column(length=255) 
     */
    protected $image;
    
    /** 
     * Campo contendo a imagem.
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $imageDefault = false;
	
	/** 
     * Diretorio para as fotos.
     * @var string
     */
    protected $directory;
    
    public function __construct(){
		
		if(!empty($this->image) && !empty($this->directory))
			$this->image = new ImageFile(new URLRequest(System::GetVariable('directory').$this->directory.$this->image));
		
    	parent::__construct();
		
    }
	
	/**
     * @ORM\PrePersist
     */
	public function prePersist(){
		
		if($this->image instanceof \TemTudoAqui\Utils\Data\ImageFile && !empty($this->directory)){
			
			$img = FileReference::Save($this->image, System::GetVariable('directory').$this->directory);
			$this->image = $img->urlRequest->baseName();
			
		}
		
	}
	
	/**
     * @ORM\PostPersist
     */
	public function postPersist(){
		
		if(!empty($this->image) && !empty($this->directory)){
			
			$this->image = new ImageFile(new URLRequest(System::GetVariable('directory').$this->directory.$this->image));
			
		}
		
	}
	
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->description);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    } 
	
}