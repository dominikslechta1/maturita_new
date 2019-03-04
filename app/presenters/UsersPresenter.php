<?php

namespace App\Presenters;

use App\Forms;
use \Nette\Security\User;
use Tracy\Debugger;
use App\Model\UsersManager;

class UsersPresenter extends BasePresenter {

    private $user;
    private $ChangePassFormFactory;
    private $userM;
    private $ChangeUsernameFormFactory;

    public function __construct(User $user, Forms\ChangePassFormFactory $changepassformfactory, UsersManager $userM, Forms\ChangeUsernameFormFactory $changeusernameformfactory) {
        $this->user = $user;
        $this->ChangePassFormFactory = $changepassformfactory;
        $this->userM = $userM;
        $this->ChangeUsernameFormFactory = $changeusernameformfactory;
    }

    public function renderDefault() {
        if ($this->user->isAllowed('users', 'view')) {
            $this->template->users = $this->userM->getUsers();
        } else {
            $this->flashMessage('Nemáš oprávnění vidět uživatele.', 'danger');
            $this->redirect('Homepage');
        }
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
