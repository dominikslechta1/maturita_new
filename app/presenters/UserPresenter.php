<?php

namespace App\Presenters;

use App\Forms;
use \Nette\Security\User;
use Tracy\Debugger;
use App\Model\UserManager;

class UserPresenter extends BasePresenter {

    private $user;
    private $ChangePassFormFactory;
    private $userM;
    private $ChangeUsernameFormFactory;

    public function __construct(User $user, Forms\ChangePassFormFactory $changepassformfactory, UserManager $userM, Forms\ChangeUsernameFormFactory $changeusernameformfactory) {
        $this->user = $user;
        $this->ChangePassFormFactory = $changepassformfactory;
        $this->userM = $userM;
        $this->ChangeUsernameFormFactory = $changeusernameformfactory;
    }

    public function renderOverview() {
        $this->template->userview = $this->userM->getUser($this->user->getId());
        $this->template->userroles = $this->user->getRoles();
    }

    protected function createComponentChangePassForm() {
        return $this->ChangePassFormFactory->create(function ($message = '') {
                    $this->flashMessage($message, 'success');
                    $this->redirect('this');
                });
    }
    protected function createComponentChangeUsernameForm(){
        return $this->ChangeUsernameFormFactory->create(function ($message = '') {
                    $this->flashMessage($message, 'success');
                    $this->redirect('this');
                });
    }

}
