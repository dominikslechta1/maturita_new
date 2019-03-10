<?php

namespace App\Model;

use Nette;

/**
 * Privilege management.
 */
class FileTypeManager {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'm_filetypes',
            COLUMN_ID = 'idFileType',
            COLUMN_TILETYPE = 'TileType',
            COLUMN_GROUP = 'Group';

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

    public function getFileTypes() {
        return $this->database->table(self::TABLE_NAME);
    }

    public function getFileExtId($ext) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_TILETYPE, $ext)->fetchField('idFileType');
    }

    /**
     * gets all possible files extensions
     * @return array
     */
    public function acceptedExtension() {
        $n = $this->database->table(self::TABLE_NAME)->fetchAll();
        $field = array();
        foreach ($n as $id => $item) {
            array_push($field, $item->TileType);
        }
        return $field;
    }

    /**
     * img
     * word
     * pdf
     * rar
     */
    public function getExtByGroup($group) {
        $n = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_GROUP, $group);
        $field = array();
        foreach ($n as $id => $item) {
            array_push($field, $item->TileType);
        }
        return $field;
    }

}
