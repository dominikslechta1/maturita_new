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
    
    public function __construct(User $user, ProjectManager $projectm) {
        parent::__construct();
        $this->user = $user;
        $this->projectm = $projectm;
        
    }

    public function renderProject($idProject = '') {
        if ($idProject !== '') {
            //when has project id
            $this->template->project = $this->projectm->getProjectsWhereId($idProject)->select('*')->fetch();
            
        } else {
            throw new \Nette\InvalidArgumentException('$idProject needs id');
        }
    }

}
