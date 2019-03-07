<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use Nette\Security\User;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;
use App\Model\FilesManager;

class AddFilePresenter extends BasePresenter {

    private $projectManager;
    private $AddFileFormFactory;
    private $id;
    private $filesM;
    private $project;
    private $fileHelper;

    public function __construct(\App\Helpers\FileHelper $fileHelper, ProjectManager $projectm, Forms\AddFileFormFactory $addfileformfactory, FilesManager $filesM) {
        parent::__construct();
        $this->projectManager = $projectm;
        $this->AddFileFormFactory = $addfileformfactory;
        $this->filesM = $filesM;
        $this->fileHelper = $fileHelper;
    }

    public function actionAddfile($id) {
        $project = $this->projectManager->getProjectsWhereId($id);
        if(!$project){
            throw new \Nette\Application\BadRequestException();
        }else{
            $this->id = $id;
        }
    }

    public function renderAddfile() {
    }
    protected function createComponentAddFileFormFactory() {

        return $this->AddFileFormFactory->create(function ($message = '', $type = 'info') {
                    $this->flashMessage($message, $type);
                    $this->redirect('Project:detail', $this->id);
                }, $this->id);
    }

}
