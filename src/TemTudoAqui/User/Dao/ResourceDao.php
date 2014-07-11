<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\Resource;

class ResourceDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Resource;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}