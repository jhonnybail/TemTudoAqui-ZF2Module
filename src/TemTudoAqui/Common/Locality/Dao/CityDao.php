<?php

namespace TemTudoAqui\Common\Locality\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Locality\City;

class CityDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new City;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}