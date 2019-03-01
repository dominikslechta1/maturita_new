<?php

namespace App\Model;

use Nette;

/**
 * Privilege management.
 */
class ProjectManager {

    use Nette\SmartObject;

    private $project;

    const
            TABLE_NAME = 'm_projects',
            COLUMN_ID = 'idProject',
            COLUMN_NAME = 'Name',
            COLUMN_USER = 'User',
            COLUMN_CONSULTANT = 'Consultant',
            COLUMN_OPONENT = 'Oponent',
            COLUMN_YEAR = 'Year',
            COLUMN_PUBLIC = 'Public',
            COLUMN_DESC = 'Desc',
            COLUMN_LOCK = 'Lock',
            COLUMN_SCORE = 'Score';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function getProjects() {
        return $this->database->table(self::TABLE_NAME);
    }
    public function getProjectsWhereId($param) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID,$param);
    }
    public function showYear($year) {
        if ($year !== '') {
            return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_YEAR, $year);
        }
        else{
            return $this->database->table(self::TABLE_NAME);
        }
    }
    
    public function getYears(){
        return $this->database->table(self::TABLE_NAME)->group(self::COLUMN_YEAR);
    }

}
