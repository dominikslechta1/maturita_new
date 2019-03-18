<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;

/**
 * Users Privilege management.
 */
class UserPrivilegesManager {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'm_users_has_privileges',
            COLUMN_ID = 'idUsersHasPrivileges',
            COLUMN_user = 'idUser',
            COLUMN_privilege = 'idPrivilege';

    /** @var \Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /**
     * @return array return array of all privileges named
     */
    public function getAllPrivileges() {
        $rows = $this->database->query('select idUser, Privilege 
from m_users_has_privileges join m_privileges on m_privileges.idPrivileges =
m_users_has_privileges.idPrivilege 
order by m_users_has_privileges.idUser ASC;');
        $res = array();
        foreach ($rows as $id => $row) {
            array_push($res, array($row->idUser, $row->Privilege));
        }
        return $res;
    }

    /**
     * @return array return array of user roles
     * @param type $idUser
     */
    public function getUserPrivilege($idUser) {
        $n = $this->database->table(self::TABLE_NAME)
                ->where(self::COLUMN_user, $idUser);
        $res;
        while ($row = $n->fetch()) {
            $res[] = $row->ref('m_privileges', self::COLUMN_privilege)->Privilege;
        }
        return $res;
    }

    public function getUserPrivilegeIds($idUser) {
        $n = $this->database->table(self::TABLE_NAME)
                ->where(self::COLUMN_user, $idUser);
        $res = array();
        while ($row = $n->fetch()) {
            array_push($res, $row[self::COLUMN_privilege]);
        }
        return $res;
    }

    /**
     * gets array of users and roles and insert it in database
     * each user get each role
     * @param userId id of User
     * @param array id of roles
     */
    public function insertPrivilege($idUser, array $roles) {
        foreach ($roles as $role) {
            $res = $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_user => $idUser,
                self::COLUMN_privilege => $role
            ]);
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    public function deletePrivilegeOnUser($idUser) {
        $count = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_user, $idUser)->delete();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getPrivilegeUsers($idPrivilege) {
        $n = $this->database->table(self::TABLE_NAME)
                ->where(self::COLUMN_privilege, $idPrivilege);
        $res;
        while ($row = $n->fetch()) {
            $res[] = $row[self::COLUMN_user];
        }
        return $res;
    }

}
