<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use Nette\Application\UI\Form;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;
use App\Helpers\RoleHelper;

class HomepagePresenter extends BasePresenter {

    /**
     * @var Forms\SignInFormFactory
     */
    private $signInFactory;

    /** @var user */
    private $user;
    private $projectM;
    private $privilege;
    private $roleH;

    public function __construct(RoleHelper $roles, Forms\SignInFormFactory $sign, User $user, UserPrivilegesManager $privilege, ProjectManager $projectm) {
        $this->signInFactory = $sign;
        $this->user = $user;
        $this->privilege = $privilege;
        $this->projectM = $projectm;
        $this->roleH = $roles;
    }

    public function renderDefault() {
        if (!isset($this->template->projects)) {
            $this->template->projects = $this->roleH->GetProjectsByRoleAndVisible();
            //$this->template->projects = $this->projectM->getProjects()->select('*');
        }
        if (!isset($this->template->years)) {
            $this->template->years = $this->projectM->getYears()->select('*');
        }
    }
    public function renderUserprojects(){
        if($this->user->isLoggedIn()){
            $this->template->projects = $this->projectM->getProjectsByUser($this->user->getId());
        }else{
            $this->flashMessage('Nejsi přihlášen', 'danger');
        }
        
    }

    protected function createComponentSignUpForm() {
        return $this->signInFactory->create(function ($message,$type) {
                    $this->flashMessage($message, $type);
                    $this->redirect('Homepage:');
                });
    }

    public function handleLogout() {
        $this->user->logout();
        $message = 'Byl jsi odhlášen.';
        $type = 'danger';
        $this->flashMessage($message, $type);
        $this->redirect('Homepage:');
    }

    public function handleYear($year = '---') {
        $this->template->projects = $this->roleH->GetProjectsByRoleAndVisible($year);
        $this->template->curyear = $year;
        $this->redrawControl('projects');
    }

}
