<?php

namespace TemTudoAqui\Store;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object,
	TemTudoAqui\InvalidArgumentException,
	TemTudoAqui\User\System,
	TemTudoAqui\Utils\Data\ImageFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_product")
 * @ORM\HasLifecycleCallbacks
 */
class Product extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected 	$id;
    
    /**
	 * Referência do produto.
	 * @var string
	 * 
     * @ORM\Column(length=50)
     */
    protected 	$reference;
    
    /**
	 * Nome do produto.
	 * @var string
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$name;
    
   	/**
	 * Palavras-chaves do produto.
	 * @var string
	 * 
     * @ORM\Column(length=255)
     */
    protected 	$keyWords;
    
    /**
	 * Define se o produto possui frete grátis.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$freeShipping = false;
    
	/**
	 * Palavras-chaves do produto.
	 * @var string
	 * 
     * @ORM\Column(length=30)
     */
    protected 	$warranty;
	
    /**
	 * Define quantas parcelas o produto poderá ser parcelado.
	 * @var integer
	 * 
     * @ORM\Column(type="integer",length=2)
     */
    protected 	$subDivision;
    
    /**
	 * Define quanto será cobrado de juro nas parcelas para este produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$interest;
    
    /**
	 * Define quantas parcelas sem juros o produto poderá ser parcelado.
	 * @var integer
	 * 
     * @ORM\Column(type="integer",length=2)
     */
    protected 	$subDivisionWithoutInterest;
    
    /**
	 * Peso do produto em Kg.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$weight;
    
    /**
	 * Comprimento do produto em cm.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$length;
    
    /**
	 * Altura do produto em cm.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$height;
    
    /**
	 * Profundidade do produto em cm.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=3)
     */
    protected 	$depth;
    
    /**
	 * Define se o produto não terá preço.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$withoutPrice = false;
    
    /**
	 * Preço do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=2)
     */
    protected 	$price;
    
    /**
	 * Preço promocional do produto.
	 * @var float
	 * 
     * @ORM\Column(type="float",scale=7,precision=2)
     */
    protected 	$promotionalPrice;
    
    /**
	 * Estoque do produto.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$stock;
    
    /**
	 * Tipo de frete do produto.
	 * @var integer
	 * 
     * @ORM\Column(type="integer")
     */
    protected 	$shipping;
    
    /**
	 * Curta descrição do produto.
	 * @var string
	 * 
     * @ORM\Column
     */
    protected 	$shortDescription;
	
	/**
	 * Descrição do produto.
	 * @var string
	 * 
     * @ORM\Column
     */
    protected 	$description;
    
    /**
	 * Define se o produto está ativo.
	 * @var boolean
	 * 
     * @ORM\Column(type="boolean")
     */
    protected 	$active;
    
    /**
     * Lista de categorias.
     * @var	array
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="product", cascade={"detach"})
     * @ORM\JoinTable(name="tta_rel_product_category",
     *      joinColumns={@ORM\JoinColumn(name="idproduct", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idcategory", referencedColumnName="id")}
     *      )
     **/
    protected 	$category;
    
    /**
     * Atributos do produto.
     * @var	array
     *
     * @ORM\OneToMany(targetEntity="ProductAttribute", mappedBy="product", cascade={"persist","detach"})
     */
    protected 	$attribute;
	
	/**
     * Imagens relacionadas aos produtos.
     * @var	array
     *
	 * @ORM\ManyToMany(targetEntity="ProductImage", cascade={"all"})
     * @ORM\JoinTable(name="tta_rel_store_product_image",
     *      joinColumns={@ORM\JoinColumn(name="idproduct")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idimage", unique=true)}
     *      )
     */
    protected 	$image;
    
    public function __construct(){
    	if(empty($this->category))
    		$this->category = new \Doctrine\Common\Collections\ArrayCollection();
    	if(empty($this->attribute))
    		$this->attribute = new \Doctrine\Common\Collections\ArrayCollection();
		if(empty($this->image))
    		$this->image = new \Doctrine\Common\Collections\ArrayCollection();
		parent::__construct();
    }
    
	/**
     * Adiciona uma categoria na lista.
     *
     * @param  \TemTudoAqui\Store\Category	$category
     * @return void
     */
    public function addCategory($category){
	    $this->category->add($category);
    }
    
	/**
     * Retorna a lista de categorias.
     *
     * @return array
     */
    public function getCategories(){
    	return $this->category;
    }
    
	/**
     * Adiciona um atributo na lista.
     *
     * @param  \TemTudoAqui\Store\Attribute		$attribute
     * @param  boolean							$required
     * @param  boolean							$visibleFrontEnd
     * @param  boolean							editFrontEnd
     * @return \TemTudoAqui\Store\ProductAttribute
     */
    public function addAttribute(Attribute $attribute, $required = false, $visibleFrontEnd = false, $editFrontEnd = false){
    	if(!empty($this->id)){
	    	$attr = new ProductAttribute;
	    	$attr->attribute = $attribute;
	    	$attr->product = $this;
	    	$attr->required = $required;
	    	$attr->visibleFrontEnd = $visibleFrontEnd;
	    	$attr->editFrontEnd = $editFrontEnd;
	    	$this->attribute->add($attr);
	    	return $attr;
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 253, 'Não é permitido adicionar um atributo antes de salvar o Produto.');
    }
    
	/**
     * Retorna a lista de atributos.
     *
     * @return array
     */
    public function getAttributes(){
    	return $this->attribute;
    }
	
	/**
     * Adiciona uma imagem na lista.
     *
     * @param  \TemTudoAqui\Utils\Data\ImageFile	$image
     * @param  string							$description
     * @return \TemTudoAqui\Common\Image
     */
    public function addImage(ImageFile $image, $position = null, $description = ''){
    	if(!empty($this->id)){

			$img = new ProductImage(__CLASS__);
			$img->image = $image;
			$img->description = $description;
			
			if($position == null)
				$position = $this->image->count();
			
			$img->position = $position;
			$this->image->add($img);			
			
	    	return $img;
    	}else
    		throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 253, 'Não é permitido adicionar uma imagem antes de salvar o Produto.');
    }
    
	/**
     * Retorna a lista de imagens.
     *
     * @return array
     */
    public function getImages(){
    	return $this->image;
    }
	
    /**
     * @ORM\PostLoad
     */
	public function postLoad(){
		self::__construct();
	}
    
	public function toString(){
    	return new \TemTudoAqui\Utils\Data\String($this->name);
    }
    
    public function __toString(){
    	return (string) $this->toString();
    }  
	
}