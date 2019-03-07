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
    private $editProjectFormFactory;
    private $id;

    public function __construct(User $user, ProjectManager $projectM, Forms\EditProjectFormFactory $editprojectformfactory) {
        $this->user = $user;
        $this->projectM = $projectM;
        $this->editProjectFormFactory = $editprojectformfactory;
    }

    public function renderProjects() {
        //$this->template->projects = $this->projectM->getProjects();
        if (!isset($this->template->projects)) {
            $this->template->projects = $this->projectM->getProjects()->order('Year DESC');
            //$this->template->projects = $this->projectM->getProjects()->select('*');
        }
        if (!isset($this->template->years)) {
            $this->template->years = $this->projectM->getYears()->select('*');
        }
    }

    public function renderEdit($id = '') {
        if ($id !== '') {
            $this->id = $id;
        } else {
            $this->flashMessage('Projekt momentálně nelze upravit.');
            $this->redirect('Homepage');
        }
    }

    protected function createComponentEditProjectForm() {
        if ($this->user->isAllowed('project', 'edit')) {
            return $this->editProjectFormFactory->adminCreate(function ($message = '', $type = 'success') {
                        $this->flashMessage($message, $type);
                        $this->redirect('Manageprojects:projects');
                    }, $this->id);
        } else {
            throw \Nette\Security\AuthenticationException;
        }
    }

    //handlers
    public function handleLock($id, $lock, $year = '') {
        if (is_numeric($id) && $id > -1) {
            $res = $this->projectM->getProjectsWhereId($id);
        } elseif ($id == -1) {
            $res = $this->projectM->getProjects($year);
        } else {
            return;
        }
        if ($res && $this->user->isAllowed('project', 'unlocklock')) {
            $res->update([
                'Lock' => ($lock == 0) ? '1' : '0'
            ]);
            if ($id == -1) {
                $this->flashMessage(($lock == 0) ? 'Projekty byly uzamčeny' : 'Projekty byly odemčeny', 'success');
                $this->redrawControl('projects');
            } else {
                $this->flashMessage(($lock == 0) ? 'Projekt byl uzamčen' : 'Projekt byl odemčen', 'success');
                $this->redrawControl('project' . $id);
            }
        } else {
            if ($id == -1) {
                $this->flashMessage('Projekty nebyly uzamčeny ani odemčeny', 'danger');
            } else {
                $this->flashMessage('Projekt nebyl uzamčen ani odemčen', 'danger');
            }
            $this->redrawControl('projects');
        }
    }

    public function handlePublic($id, $public, $year = '') {
        if (is_numeric($id) && $id > -1) {
            $res = $this->projectM->getProjectsWhereId($id);
        } elseif ($id == -1) {
            $res = $this->projectM->getProjects($year);
        } else {
            return;
        }
        if ($res->count('*') > 0 && $this->user->isAllowed('project', 'visibility')) {
            $res->update([
                'Public' => ($public == 0) ? '1' : '0'
            ]);
            if ($id == -1) {
                $this->flashMessage(($public == 0) ? 'Projekty byly zveřejněny' : 'Projekty byly skryty', 'success');
                $this->redrawControl('projects');
            } else {
                $this->flashMessage(($public == 0) ? 'Projekt byl zveřejněn' : 'Projekt byl skryt', 'success');
                $this->redrawControl('project' . $id);
            }
        } else {
            if ($id == -1) {
                $this->flashMessage('Projekty nebyly zveřejněny ani skryty', 'danger');
            } else {
                $this->flashMessage('Projekt nebyl zveřejněn ani skryt', 'danger');
            }
            $this->redrawControl('projects');
        }
    }

    public function handleDelete($id = -1) {
        if ($this->user->isLoggedIn() && $this->user->isAllowed('project', 'delete') && $id > -1) {
            if ($this->projectM->deleteProject($id)) {
                $this->flashMessage('Projekt byl úspěšně smazán.', 'success');
                $this->redrawControl('projects');
            } else {
                $this->flashMessage('Projekt nebyl smazán.', 'danger');
            }
        } else {
            $this->flashMessage('momentálně nelze smazat projekt', 'danger');
        }
    }

    public function handleYear($year = '---') {
        //$this->template->projects = $this->roleH->GetProjectsByRoleAndVisible($year);
        $this->template->projects = $this->projectM->getProjects($year)->order('Year DESC');
        $this->template->curyear = $year;
        $this->redrawControl('projects');
    }

}
