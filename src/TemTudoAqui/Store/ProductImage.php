<?php

namespace TemTudoAqui\Store;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\System,
	TemTudoAqui\Common\Image;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_common_image")
 * @ORM\HasLifecycleCallbacks
 */
class ProductImage extends Image {
	
    public function __construct(){
		
		$this->directory = System::GetVariable('dataUsers')."user-".System::GetVariable('idUser')."/site-".System::GetVariable('idSite')."/".System::GetVariable('dataProducts');
		@mkdir($this->directory, 0777);
		
    	parent::__construct();
		
    }
    
	/**
     * @ORM\PostLoad
     */
	public function postLoad(){
		self::__construct();
	}
	
	/**
     * @ORM\PrePersist
     */
	public function prePersist(){
		parent::prePersist();
	}
	
	/**
     * @ORM\PostPersist
     */
	public function postPersist(){
		parent::postPersist();
	}
	
	/**
     * @ORM\PreUpdate
     */
	public function preUpdate(){
		parent::preUpdate();
	}
	
	/**
     * @ORM\PostUpdate
     */
	public function postUpdate(){
		parent::postUpdate();
	}
	
}