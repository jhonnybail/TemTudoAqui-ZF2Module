<?php

namespace TemTudoAqui\Common\Locality\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Locality\State;

class StateDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new State;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}