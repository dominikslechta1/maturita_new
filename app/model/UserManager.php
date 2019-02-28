<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use App\Model\UserPrivilegesManager;

/**
 * Users management.
 */
class UserManager implements \Nette\Security\IAuthenticator {

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

    public function __construct(Nette\Database\Context $database, UserPrivilegesManager $privilege) {
        $this->database = $database;
        $this->privileges = $privilege;
    }

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($email, $password) = $credentials;

        $row = $this->database->table(self::TABLE_NAME)
                ->where(self::COLUMN_EMAIL, $email)
                ->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Email is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new Nette\Security\AuthenticationException('Password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update([
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ]);
        }

        $privileges = $this->privileges->getUserPrivilege($row[self::COLUMN_ID]);
        
        return new Nette\Security\Identity($row[self::COLUMN_ID], $privileges, [$row->Username, $row->Email]);
    }

    /**
     * Adds new user.
     * @param string $username username
     * @param  string $email email
     * @param  string $password password
     * @param  array $roles roles
     * @return void
     * @throws throw UniqueConstraintViolationException
     */
    public function add($username, $email, $password, $roles) {
        Nette\Utils\Validators::assert($email, 'email');
            $id = $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_NAME => $username,
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
                self::COLUMN_EMAIL => $email,
            ]);

            $this->privileges->insertPrivilege($id, $roles);
    }

}
