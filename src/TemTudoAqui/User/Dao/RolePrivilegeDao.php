<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\RolePrivilege,
    TemTudoAqui\User\Role;

class RolePrivilegeDao extends Dao {

    public function findAll($order = array(), $limit = null, $offset = null){

        $obj = new RolePrivilege;
        return parent::findAll($obj, $order, $limit, $offset);

    }

    public function deleteByRole(Role $role){

        $sql = "DELETE
					FROM tta_user_roleprivilege
					WHERE idrole = :role";

        $stmt = $this->dbal->prepare($sql);

        $stmt->bindValue("role", (string)$role->id);
        $stmt->execute();

    }
	
}