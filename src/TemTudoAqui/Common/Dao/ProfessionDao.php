<?php

namespace TemTudoAqui\Common\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Profession;

class ProfessionDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Profession;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}