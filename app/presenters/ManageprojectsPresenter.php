<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use App\Model\ProjectManager;
use Nette\Application\UI\Form;

class ManageprojectsPresenter extends BasePresenter {

    /** @var user */
    private $user;
    private $projectM;

    public function __construct(User $user, ProjectManager $projectM) {
        $this->user = $user;
        $this->projectM = $projectM;
    }

    public function renderProjects() {
        $this->template->projects = $this->projectM->getProjects();
    }
    
    public function handleLock($id, $lock){
        $res = $this->projectM->getProjectsWhereId($id);
        if($res->count('*') > 0){
            $res->update([
                'Lock' => ($lock == 0)? '1': '0'
            ]);
            //$this->flashMessage('Projekt byl uzamčen','success');
            $this->redrawControl('project-'.$id);
        } else {
            $this->flashMessage('Projekt nebyl uzamčen','danger');
            $this->redrawControl('projects');
        }
    }

}
