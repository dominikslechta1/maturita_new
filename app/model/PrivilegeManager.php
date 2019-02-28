<?php

namespace App\Model;

use Nette;

/**
 * Privilege management.
 */
class PrivilegeManager{

    use Nette\SmartObject;

    const
            TABLE_NAME = 'm_privileges',
            COLUMN_ID = 'idPrivileges',
            COLUMN_PRIVILEGE = 'Privilege';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }
    /**
     * 
     * @return assoc_array all privileges
     */
    public function getPrivileges(){
        return $this->database->table(self::TABLE_NAME)->fetchPairs('idPrivileges', 'Privilege');
    }
    
}

