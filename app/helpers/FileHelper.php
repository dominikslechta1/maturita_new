<?php

namespace App\Helpers;

use Nette;
use Nette\Database\Context;
use Nette\Security\User;
use App\Model\ProjectManager;
use App\Model\FilesManager;

final class FileHelper {

    use Nette\SmartObject;

    /** @var database */
    private $database;

    /** @var user */
    private $user;

    /** @var projectmanager */
    private $projectM;
    
    private $filesManager;

    public function __construct(Context $database, User $user, ProjectManager $projectM, FilesManager $filem) {
        $this->database = $database;
        $this->user = $user;
        $this->projectM = $projectM;
        $this->filesManager = $filem;
    }

    
    public function delete($id){
        if($this->filesManager->checkId($id)){
            $this->filesManager->delete($id);
            return true;
        }else{
            return false;
        }
    }
    public function getFullname($id){
        $file = $this->filesManager->whereId($id)->fetch();
        return $file->Hash . $file->ref('m_filetypes', 'Filetype')->TileType;
    }
}