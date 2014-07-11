<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\UserPrivilege,
    TemTudoAqui\User\UserAuth;

class UserPrivilegeDao extends Dao {
	
	public function findAll($order = array(), $limit = null, $offset = null){
		
		$obj = new UserPrivilege;
		return parent::findAll($obj, $order, $limit, $offset);
		
	}

    public function deleteByUserAuth(UserAuth $userAuth){

        $sql = "DELETE
					FROM tta_user_userprivilege
					WHERE iduser = :user";

        $stmt = $this->dbal->prepare($sql);

        $stmt->bindValue("user", (string)$userAuth->id);
        $stmt->execute();

    }
	
}