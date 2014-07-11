<?php

namespace TemTudoAqui\Store\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Store\Product;

class ProductDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Product;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}