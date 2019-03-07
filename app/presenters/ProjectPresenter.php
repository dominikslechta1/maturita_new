<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use Nette\Security\User;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;
use App\Model\FilesManager;
use Nette\Utils\FileSystem;

class ProjectPresenter extends BasePresenter {

    private $projectManager;
    private $EditProjectFormFactory;
    private $id;
    private $filesM;
    private $project;
    private $fileHelper;

    public function __construct(\App\Helpers\FileHelper $fileHelper, ProjectManager $projectm, Forms\EditProjectFormFactory $editprojectformfactory, FilesManager $filesM) {
        parent::__construct();
        $this->projectManager = $projectm;
        $this->EditProjectFormFactory = $editprojectformfactory;
        $this->filesM = $filesM;
        $this->fileHelper = $fileHelper;
    }

    public function actionDetail($id) {
        $this->project = $project = $this->projectManager->getProjectsWhereId($id);
        if (!$project) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    public function renderDetail($id) {

        $this->template->project = $this->project;

        if ($this->filesM->checkProjectId($id) && $this->user->isAllowed('files', 'view')) {
            if ($this->project->Lock == 1) {
                $this->template->files = $this->filesM->getFiles();
            }elseif($this->project->Lock == 0){
                $this->template->files = $this->filesM->getFilesWhereUser($this->user->getId());
            }
        }
    }

    public function actionEdit($id) {
        $project = $this->projectManager->getProjectsWhereId($id);
        if (!$project) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    public function renderEdit($id) {
        $this->id = $id;
    }

    public function handleDelete($id = -1) {
        if ($this->user->isLoggedIn() && $this->user->isAllowed('project', 'delete') && $id > -1) {
            if ($this->projectManager->deleteProject($id)) {
                $this->flashMessage('Projekt byl úspěšně smazán.');
                $this->redirect('Homepage:default');
            } else {
                $this->flashMessage('Projekt nebyl smazán.');
            }
        } else {
            $this->flashMessage('momentálně nelze smazat projekt');
        }
    }

    protected function createComponentEditProjectForm() {

        return $this->EditProjectFormFactory->create(function ($message = '', $type = 'info', $id) {
                    $this->flashMessage($message, $type);
                    $this->redirect('Project:project', $id);
                }, $this->id);
    }

    public function handleDeleteFile($id, $idFile) {
        if (is_numeric($idFile) && $this->user->isAllowed('files','delete')) {
            $fileFullName = $this->fileHelper->getFullname($idFile);
            if ($this->fileHelper->delete($idFile)) {

                try {
                    FileSystem::delete(__DIR__ . '\\..\\..\\www\\files\\' . $fileFullName);
                } catch (Nette\IOException $e) {
                    throw new $e;
                }

                $this->flashMessage('Úspěšně smazáno', 'success');
                $this->redirect('Project:detail', $id);
            } else {
                $this->flashMessage('Nesmazáno', 'danger');
                $this->redirect('Homepage:');
            }
        } else {
            $this->flashMessage('Monemtálně není dostupné', 'danger');
            $this->redirect('Homepage:');
        }
    }

}
