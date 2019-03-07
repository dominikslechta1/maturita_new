<?php

namespace App\Model;

use Nette;

/**
 * Privilege management.
 */
class FilesManager {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'm_files',
            COLUMN_ID = 'idFiles',
            COLUMN_HASH = 'Hash',
            COLUMN_PROJECT = 'Project',
            COLUMN_FILETYPE = 'Filetype',
            COLUMN_DESC = 'Desc',
            COLUMN_NAME = 'Name',
            COLUMN_USER = 'User';

    /** @var Nette\Database\Context */
    private $database;
    private $file;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function checkId($id) {

        if ($this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->count('*') > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkProjectId($projectId) {

        if ($this->database->table(self::TABLE_NAME)->where(self::COLUMN_PROJECT, $projectId)->count('*') > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getFiles() {
        return $this->database->table(self::TABLE_NAME);
    }

    public function whereId($id) {
        return $this->database->table(self::TABLE_NAME)->where($id);
    }
    
    public function delete($id) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->delete();
    }

    /**
     * gets all possible files extensions
     * @return array
     */
    public function acceptedExtension() {
        $n = $this->database->table('m_filetypes')->fetchAll();
        $field = array();
        foreach ($n as $id => $item) {
            array_push($field, $item->TileType);
        }
        return $field;
    }

    /**
     * insert files to database
     * @param array $values
     */
    public function insertFile($values) {
        $count = $this->database->table(self::TABLE_NAME)->insert([
            'Hash' => $values['hash'],
            'Filetype' => $values['fileextid'],
            'Name' => $values['name'],
            'Desc' => $values['desc'],
            'Project' => $values['project'],
            'User' => $values['user']
        ]);
        if ($count) {
            return true;
        } else {
            return false;
        }
    }
    public function getFilesWhereUser($user){
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_USER, $user);
    }
}
