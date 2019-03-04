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

    /**
     * gets array of users and roles and insert it in database
     * each user get each role
     * @param userId id of User
     * @param array id of roles
     */
    public function insertPrivilege($idUser, array $roles) {
        foreach ($roles as $role) {
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_user => $idUser,
                self::COLUMN_privilege => $role
            ]);
        }
    }
    public function deletePrivilegeOnUser($idUser){
        $count = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_user, $idUser)->delete();
        if($count > 0){
            return true;
        }else{
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
