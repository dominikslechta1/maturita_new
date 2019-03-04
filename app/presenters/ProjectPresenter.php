<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;

class ProjectPresenter extends BasePresenter {

    private $user;
    private $projectm;
    private $EditProjectFormFactory;
    private $id;

    public function __construct(User $user, ProjectManager $projectm, Forms\EditProjectFormFactory $editprojectformfactory) {
        parent::__construct();
        $this->user = $user;
        $this->projectm = $projectm;
        $this->EditProjectFormFactory = $editprojectformfactory;
    }

    public function renderProject($idProject = '') {
        if ($idProject !== '') {
            //when has project id
            $this->template->project = $this->projectm->getProjectsWhereId($idProject)->select('*')->fetch();
        } else {
            throw new \Nette\InvalidArgumentException('$idProject needs id');
        }
    }
    
    public function renderEdit($id = ''){
        if($id !== ''){
            $this->id = $id;
        }else{
            $this->flashMessage('Projekt momentálně nelze upravit.');
            $this->redirect('Homepage');
        }
    }
    

    public function handleDelete($id = -1) {
        if ($this->user->isLoggedIn() && $this->user->isAllowed('project', 'delete') && $id > -1) {
            if ($this->projectm->deleteProject($id)) {
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
        
        return $this->EditProjectFormFactory->create(function ($message = '', $type= 'info', $id) {
                    $this->flashMessage($message, $type);
                    $this->redirect('Project:project', $id);
                }, $this->id);
    }

}
