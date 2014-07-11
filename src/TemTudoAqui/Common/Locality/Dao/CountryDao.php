<?php

namespace TemTudoAqui\Common\Locality\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Locality\Country;

class CountryDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Country;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}