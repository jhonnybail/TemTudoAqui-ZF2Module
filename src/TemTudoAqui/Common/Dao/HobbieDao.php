<?php

namespace TemTudoAqui\Common\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Hobbie;

class HobbieDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Hobbie;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}