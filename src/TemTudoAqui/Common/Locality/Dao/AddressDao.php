<?php

namespace TemTudoAqui\Common\Locality\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Locality\Address;

class AddressDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Address;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}