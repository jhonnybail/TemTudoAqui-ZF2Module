<?php

namespace TemTudoAqui\Store\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Store\Category;

class CategoryDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Category;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}