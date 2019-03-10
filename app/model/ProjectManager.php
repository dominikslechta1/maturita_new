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
            COLUMN_RQFILE = 'Rqfile',
            COLUMN_RQFILEPDF = 'RqfilePDF',
            COLUMN_SCORE = 'Score';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function addProject($values) {
        $id = $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_NAME => $values->name,
            self::COLUMN_USER => $values->user,
            self::COLUMN_YEAR => date('Y'),
        ]);
        $update = $this->database->table(self::TABLE_NAME)->get($id);
        if ($values->consultant != '') {
            $update->update(['Consultant' => $values->consultant]);
        }if ($values->oponent != '') {
            $update->update(['Oponent' => $values->oponent]);
        }
        return true;
    }

    public function getProjects($year = '') {
        if ($year == '' || !is_numeric($year)) {
            return $this->database->table(self::TABLE_NAME);
        } else {
            return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_YEAR, $year);
        }
    }

    /**
     * gets project id and return un-fetched database request
     * @param int $id
     * @return context
     */
    public function getProjectsWhereId($id) {
        return $this->database->table(self::TABLE_NAME)->get($id);
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
        if ($project->idProject !== '') {
            $project->delete();
            return true;
        } else {
            return false;
        }
        return false;
    }

    public function updateProject($id, array $args) {
        $project = $this->database->table(self::TABLE_NAME)->get($id);
        if ($project) {
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

    public function userIsInProject($userId) {
        if ($this->database->table(self::TABLE_NAME)
                        ->whereOr([self::COLUMN_USER => $userId,
                            self::COLUMN_CONSULTANT => $userId,
                            self::COLUMN_OPONENT => $userId
                        ])->count('*') > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateScore($id, $score) {
        $count = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->update([
            'Score' => $score
        ]);
        if ($count) {
            return $count;
        } else {
            return $count;
        }
    }

    /**
     * remove all required files and in pdf from array
     * @param id id of project
     * @param array $arr
     */
    public function RemoveReqFilesFromArr($id, array $arr) {
        $row = $this->database->table(self::TABLE_NAME)->get($id);
        foreach ($arr as $idk => $key) {
            if ($row->Rqfile == $idk) {
                unset($arr[$idk]);
            } elseif ($row->Rqfilepdf == $key->idFiles) {
                unset($arr[$idk]);
            }
        }
        return $arr;
    }

    public function getRqFiles($id) {
        return $this->database->table(self::TABLE_NAME)->select(self::COLUMN_RQFILE . ', ' . self::COLUMN_RQFILEPDF)->get($id);
    }

    public function nullRqFileId($id) {
        $row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_RQFILE, $id);
        if ($row) {
            $row->update([
                self::COLUMN_RQFILE => 'NULL'
            ]);
        } else {
            $row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_RQFILEPDF, $id);
            if($row){
                $row->update([
                self::COLUMN_RQFILEPDF => 'NULL'
                ]);
            }
        }
    }

}
