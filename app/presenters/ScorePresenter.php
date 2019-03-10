<?php

namespace App\Presenters;

use App\Model\ProjectManager;

final class ScorePresenter extends BasePresenter {

    private $projectManager;

    public function __construct(ProjectManager $projectManager) {
        $this->projectManager = $projectManager;
    }

    public function actionAdd($id, $score) {
        if ($this->projectManager->getProjectsWhereId($id)) {
            if ($this->projectManager->updateScore($id, $score)) {
                $this->flashMessage('Hodnocení bylo uloženo.','success');
                $this->redirect('Project:detail', $id);
            }else{
                $this->flashMessage('Hodnocení nebylo uloženo.','danger');
                $this->redirect('Project:detail', $id);
            }
        }
    }

    public function renderAdd() {
        
    }

}
