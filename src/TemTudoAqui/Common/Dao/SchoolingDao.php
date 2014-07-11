<?php

namespace TemTudoAqui\Common\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Schooling;

class SchoolingDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Schooling;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}