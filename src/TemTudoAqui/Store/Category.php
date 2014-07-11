<?php

namespace TemTudoAqui\Store;

use Doctrine\ORM\Mapping as ORM,
	TemTudoAqui\Object;

/**
 * @ORM\Entity
 * @ORM\Table(name="tta_store_category")
 * @ORM\HasLifecycleCallbacks
 */
class Category extends Object {
	
	/**
	 * Chave primária.
	 * @var integer
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;
	
	/**
	 * Categoria pai.
	 * @var \TemTudoAqui\Store\Category
	 * 
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
	 * @ORM\JoinColumn(name="idcategoryparent", referencedColumnName="id")
	 */
	protected $parent;
	
	/**
	 * Nome da categoria.
	 * @var string
	 * 
	 * @ORM\Column(length=50)
	 */
	protected $name;
	
	/**
	 * Define se a categoria está ativa ou não.
	 * @var integer
	 * 
	 * @ORM\Column(type="boolean")
	 */
	protected $active;
	
	/**
	 * Define se a categoria aparecerá na lista de categorias no Front-End.
	 * @var boolean
	 * 
	 * @ORM\Column(type="boolean")
	 */
	protected $includeMenu;
	
	/**
	 * Lista de subcategorias.
	 * @var	array
	 *
	 * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
	 **/
	protected $children;
	
	/**
	 * Lista de produtos.
	 * @var	array
	 *
	 * @ORM\ManyToMany(targetEntity="Product", mappedBy="category", cascade={"detach"})
	 **/
	protected $product;
	
	public function __construct() {
		parent::__construct ();
		if (empty ( $this->children ))
			$this->children = new \Doctrine\Common\Collections\ArrayCollection ();
		if (empty ( $this->product ))
			$this->product = new \Doctrine\Common\Collections\ArrayCollection ();
	}
	
	/**
	 * Adiciona uma subcategoria na lista.
	 *
	 * @param  \TemTudoAqui\Store\Category	$category
	 * @return void
	 */
	public function addSubCategory(Category $category) {
		$category->parent = $this;
		$category->site = $this->site;
		$this->children->add ( $category );
	}
	
	/**
	 * Retorna a lista de subcategorias.
	 *
	 * @return array
	 */
	public function getSubCategories() {
		return $this->children;
	}
	
	/**
	 * Adiciona um produto na lista.
	 *
	 * @param  \TemTudoAqui\Store\Product	$product
	 * @return void
	 */
	public function addProduct(Product $product) {
		$this->product->add ( $product );
	}
	
	/**
	 * Retorna a lista de produtos.
	 *
	 * @return array
	 */
	public function getProducts() {
		return $this->product;
	}
	
	/**
	 * @ORM\PostLoad
	 */
	public function postLoad() {
		self::__construct ();
	}
	
	public function toString() {
		return new \TemTudoAqui\Utils\Data\String ( $this->name );
	}
	
	public function __toString() {
		return ( string ) $this->toString ();
	}

}