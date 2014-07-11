<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\Privilege;

class PrivilegeDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Privilege;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}