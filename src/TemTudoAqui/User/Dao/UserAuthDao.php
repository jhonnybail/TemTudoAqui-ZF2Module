<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\UserAuth;

class UserAuthDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new UserAuth;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}

    public function findByPassword($username, $password){

        $sql = "SELECT ua.id
					FROM tta_user_userauth ua
					WHERE ua.username = :username
						AND ua.password = :password";

        $stmt = $this->dbal->prepare($sql);

        $stmt->bindValue("username", $username);
        $stmt->bindValue("password", $password);
        $stmt->execute();

        return $stmt->fetchAll();

    }
	
}