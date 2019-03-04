<?php

namespace App\Presenters;

use App\Forms;
use \Nette\Security\User;
use Tracy\Debugger;
use App\Model\UserManager;

class AddcomponentPresenter extends BasePresenter {
    
    private $signUpFactory;
    private $AddProjectFormFactory;

    public function __construct(Forms\SignUpFormFactory $signupformfactory, Forms\AddProjectFormFactory $addprojectformfactory) {

        $this->signUpFactory = $signupformfactory;
        $this->AddProjectFormFactory = $addprojectformfactory;
    }
    
    public function renderAdduser(){
        
    }
    public function renderAddproject(){
        
    }


    protected function createComponentSignUpForm() {
        return $this->signUpFactory->create(function ($message = '') {
                    $this->flashMessage($message,'success');
                    $this->redirect('Homepage:');
                });
    }
    protected function createComponentAddProjectForm(){
        return $this->AddProjectFormFactory->create(function ($message = '', $type='info') {
                    $this->flashMessage($message,$type);
                    $this->redirect('Homepage:');
                });
    }
}