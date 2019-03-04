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

    public function addProject($values) {
        $consultant = ($values->consultant != '0') ? $values->consultant : NULL;
        $oponent =($values->oponent != '0') ? $values->oponent : NULL;
        $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_NAME => $values->name,
            self::COLUMN_USER => $values->user,
            self::COLUMN_CONSULTANT => $consultant,
            self::COLUMN_OPONENT => $oponent,
            self::COLUMN_YEAR => date('Y'),
        ]);
        return true;
    }

    public function getProjects() {
        return $this->database->table(self::TABLE_NAME);
    }

    public function getProjectsWhereId($param) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $param);
    }

    public function showYear($year) {
        if ($year !== '' && $year !== '---') {
            return $this->project->where(self::COLUMN_YEAR, $year);
        } else {
            return $this->project;
        }
    }

    /**
     * shows visible or invisible projects
     * @param boolean $bool show visible = true, show invisible = false
     * @return context
     */
    public function showPublic($bool = true) {
        $this->project = $this->getProjects();
        if ($bool) {
            $this->project = $this->project->where(self::COLUMN_PUBLIC, '1');
        } else {
            $this->project = $this->project;
        }
        return $this;
    }

    public function getYears() {
        return $this->database->table(self::TABLE_NAME)->group(self::COLUMN_YEAR);
    }

    public function get() {
        return $this->project;
    }

    public function deleteProject($id) {
        $project = $this->database->table(self::TABLE_NAME)->get($id);
        if ($project !== '') {
            $project->delete();
            return true;
        } else {
            return false;
        }
        return false;
    }

    public function updateProject($id, array $args) {
        $project = $this->database->table(self::TABLE_NAME)->get($id);
        if ($project !== '') {
            $project->update($args);
            return true;
        } else {
            return false;
        }
    }

    public function getProjectsByUser($userId) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_USER, $userId);
    }

    public function deleteProjectOnUser($idUser) {
        $res = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_USER, $idUser)->delete();
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }

}
