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
        $this->template->projects = $this->projectM->getProjects();
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
        return $this->editProjectFormFactory->adminCreate(function ($message = '', $type = 'success') {
                    $this->flashMessage($message, $type);
                    $this->redirect('Manageprojects:projects');
                }, $this->id);
    }

    public function handleLock($id, $lock) {
        $res = $this->projectM->getProjectsWhereId($id);
        if ($res->count('*') > 0) {
            $res->update([
                'Lock' => ($lock == 0) ? '1' : '0'
            ]);
            $this->flashMessage(($lock == 0) ? 'Projekt byl uzamčen' : 'Projekt byl odemčen', 'success');
            $this->redrawControl('project' . $id);
        } else {
            $this->flashMessage('Projekt nebyl uzamčen ani odemčen', 'danger');
            $this->redrawControl('projects');
        }
    }

    public function handlePublic($id, $public) {
        $res = $this->projectM->getProjectsWhereId($id);
        if ($res->count('*') > 0) {
            $res->update([
                'Public' => ($public == 0) ? '1' : '0'
            ]);
            $this->flashMessage(($public == 0) ? 'Projekt byl zveřejněn' : 'Projekt byl skryt', 'success');
            $this->redrawControl('project' . $id);
        } else {
            $this->flashMessage('Projekt nebyl zveřejněn ani skryt', 'danger');
            $this->redrawControl('projects');
        }
    }

    public function handleDelete($id = -1) {
        if ($this->user->isLoggedIn() && $this->user->isAllowed('project', 'delete') && $id > -1) {
            if ($this->projectM->deleteProject($id)) {
                $this->flashMessage('Projekt byl úspěšně smazán.');
                $this->redrawControl('projects');
            } else {
                $this->flashMessage('Projekt nebyl smazán.');
            }
        } else {
            $this->flashMessage('momentálně nelze smazat projekt');
        }
    }

}
