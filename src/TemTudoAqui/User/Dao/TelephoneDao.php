<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\Telephone;

class TelephoneDao extends Dao {
	
public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Telephone;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}