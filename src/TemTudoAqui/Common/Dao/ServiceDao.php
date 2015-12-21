<?php

namespace TemTudoAqui\Common\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Service;

class ServiceDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Service;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}