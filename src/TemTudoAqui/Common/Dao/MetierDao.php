<?php

namespace TemTudoAqui\Common\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\Common\Metier;

class MetierDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new Metier;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}
	
}