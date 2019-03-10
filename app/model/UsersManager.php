<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;

/**
 * Users management.
 */
class UsersManager {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'm_users',
            COLUMN_ID = 'idUser',
            COLUMN_NAME = 'Username',
            COLUMN_PASSWORD_HASH = 'Password',
            COLUMN_EMAIL = 'Email';

    /** @var Nette\Database\Context */
    private $database;
    private $privileges;
    private $user;
    private $projectM;

    public function __construct(Nette\Database\Context $database, UserPrivilegesManager $privilege, \Nette\Security\User $user, ProjectManager $projectM) {
        $this->database = $database;
        $this->privileges = $privilege;
        $this->user = $user;
        $this->projectM = $projectM;
    }

    public function getUsers() {
        return $this->database->table(self::TABLE_NAME)->select('*');
    }
    
    public function getUserById($id){
        return $this->database->table(self::TABLE_NAME)->get($id);
    }

    public function deleteUser($id) {
        $user = $this->database->table(self::TABLE_NAME)->get($id);
        if ($user !== '') {
            try {
                $this->projectM->deleteProjectOnUser($user->idUser);
                $this->privileges->deletePrivilegeOnUser($user->idUser);
                $count = $user->delete();
            } catch (\Nette\Database\ConstraintViolationException $e) {
                return false;
            }
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getStudents() {
        $ids = $this->privileges->getPrivilegeUsers(2);
        $res = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $ids)->fetchPairs(self::COLUMN_ID, self::COLUMN_NAME);
        return $res;
    }

    public function getConsultants() {
        $ids = $this->privileges->getPrivilegeUsers(3);
        $res = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $ids)->fetchPairs(self::COLUMN_ID, self::COLUMN_NAME);
        return $res;
    }

    public function getOponents() {
        $ids = $this->privileges->getPrivilegeUsers(4);
        foreach ($ids as $id) {
            $res[$this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->fetch()->idUser] = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->fetch()->Username;
        }
        return $res;
    }

    public function getUserByEmail($email) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email);
    }

    public function insertTokken($user, $hash, $date) {
        $count = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $user)->update([
            'Tokken' => $hash,
            'Tocdate' => $date
        ]);
        if ($count) {
            return true;
        } else {
            return false;
        }
    }
    
    public function updatePass($hash, $user){
        return $this->database->table(self::TABLE_NAME)->get($user)->update([
            self::COLUMN_PASSWORD_HASH => $hash
        ]);
    }

}
