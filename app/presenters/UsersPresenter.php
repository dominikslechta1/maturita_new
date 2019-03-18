<?php

namespace App\Presenters;

use \Nette\Security\User;
use Tracy\Debugger;
use App\Model\UsersManager;
use App\Forms\EditUserFormFactory;
use App\Model\UserPrivilegesManager;

class UsersPresenter extends BasePresenter {

    private $user;
    private $userM;
    private $editUserFormFactory;
    private $id;
    private $userPrivilegesManager;
    
    public function __construct(User $user, UsersManager $userM, EditUserFormFactory $editUserFormFactory, UserPrivilegesManager $userPrivilegesManager) {
        $this->user = $user;
        $this->userM = $userM;
        $this->editUserFormFactory = $editUserFormFactory;
        $this->userPrivilegesManager = $userPrivilegesManager;
    }
    
    public function actionEdit($id){
        $row = $this->userM->getUserById($id);
        if($row){
            $this->id = $id;
        }else{
            $this->flashMessage('Nastala chyba při editaci uživatele.','danger');
            $this->redirect('Homepage:');
        }
    }


        public function renderDefault() {
        if ($this->user->isAllowed('users', 'view')) {
            $this->template->users = $this->userM->getUsers()->order('Email ASC');
            $this->template->roles = $this->userPrivilegesManager->getAllPrivileges();
        } else {
            $this->flashMessage('Nemáš oprávnění vidět uživatele.', 'danger');
            $this->redirect('Homepage');
        }
    }

    protected function createComponentEditUserForm() {
        return $this->editUserFormFactory->create(function ($message = '', $type = 'info') {
                    $this->flashMessage($message, $type);
                    $this->redirect('Users:');
                }, $this->id);
    }

    public function handleDeleteUser($id = '') {
        if ($this->user->isAllowed('users', 'delete') && $id !== '') {
            if ($id != $this->user->getId()) {
                $res = $this->userM->deleteUser($id);

                $this->flashMessage(($res) ? 'Uživatel byl úspěšně smazán.' : 'Uživatel nebyl smazán.', ($res) ? 'success' : 'danger');
            } else {
                $this->flashMessage('Nemůžeš smazat sám sebe.', 'warning');
            }
            $this->redirect('Users:default');
        } else {
            $this->flashMessage('Nemáš oprávnění smazat uživatele.', 'danger');
            $this->redirect('Homepage');
        }
    }

}
