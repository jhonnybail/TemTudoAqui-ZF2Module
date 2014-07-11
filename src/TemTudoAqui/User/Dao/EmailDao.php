<?php

namespace TemTudoAqui\User\Dao;

use TemTudoAqui\Common\Dao,
	TemTudoAqui\User\Email;

class EmailDao extends Dao {
	
    public function findAll($order = array(), $limit = null, $offset = null){

        $obj = new Email;
        return parent::findAll($obj, $order, $limit, $offset);

    }

    public function hasEmail($email){

        $sql = "SELECT e.id
					FROM tta_user_email e
					WHERE e.email = :email";

        $stmt = $this->dbal->prepare($sql);

        $stmt->bindValue("email", $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
	
}