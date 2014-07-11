<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Locality,
	TemTudoAqui\User\User;

class UserDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new User;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}