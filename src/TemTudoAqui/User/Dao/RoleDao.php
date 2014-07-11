<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\Role;

class RoleDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Role;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}