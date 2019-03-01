<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use Nette\Application\UI\Form;
use App\Model\UserPrivilegesManager;
use App\Model\ProjectManager;

class HomepagePresenter extends BasePresenter {

    /**
     * @var Forms\SignInFormFactory
     */
    private $signInFactory;

    /** @var user */
    private $user;
    private $projectM;
    private $privilege;

    public function __construct(Forms\SignInFormFactory $sign, User $user, UserPrivilegesManager $privilege, ProjectManager $projectm) {
        $this->signInFactory = $sign;
        $this->user = $user;
        $this->privilege = $privilege;
        $this->projectM = $projectm;
    }

    public function renderDefault() {
        if (!isset($this->template->projects)) {
            $this->template->projects = $this->projectM->getProjects()->select('*');
        }
        if (!isset($this->template->years)) {
            $this->template->years = $this->projectM->getYears()->select('*');
        }
    }

    

    protected function createComponentSignUpForm() {
        return $this->signInFactory->create(function () {
                    $this->flashMessage('Byl Jsi úspěšně přihlášen.');
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
        $this->template->projects = $this->projectM->showYear(($year !== '---') ? $year : '')->select('*');
        $this->template->curyear = $year;
        $this->redrawControl('projects');
    }

}
