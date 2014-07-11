<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\Logger;

class LoggerDao extends Dao {
	
    public function findAll($order = array(), $limit = null, $offset = null){

        $obj = new Logger;
        return parent::findAll($obj, $order, $limit, $offset);

    }
	
}