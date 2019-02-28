<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;

class ProjectPresenter extends BasePresenter {
    
    public function __construct() {
        parent::__construct();
        
    }

    public function renderProject($idProject = '') {
        if ($idProject !== '') {
            //when has project id
            
            
            
        } else {
            throw new \Nette\InvalidArgumentException('$idProject needs id');
        }
    }

}
