<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Model\ProjectManager;
use App\Model\UsersManager;

final class EditProjectFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var User */
    private $user;
    private $projectM;
    private $project;
    private $userM;

    public function __construct(FormFactory $factory, User $user, ProjectManager $projectM, UsersManager $userm) {
        $this->factory = $factory;
        $this->user = $user;
        $this->projectM = $projectM;
        $this->userM = $userm;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $id) {
        $this->project = $this->projectM->getProjectsWhereId($id)->fetch();
        $form = $this->factory->create();

        $form->addTextArea('desc', 'Uprav popis projektu:')
                ->setEmptyValue((isset($this->project->Desc)) ? $this->project->Desc : '')
                ->addRule($form::MAX_LENGTH, 'Maximální velikost textu je 1000 znaků', 1000);
        $form->addText('url', 'Zadej url tvého projektu:')->setHtmlType('url')
                ->setEmptyValue((isset($this->project->Url)) ? $this->project->Url : '');
        $form->addHidden('nwm')->setDefaultValue($id);

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $res = $this->projectM->updateProject($values->nwm, array('Desc' => $values->desc, 'Url' => $values->url));
            if ($res) {
                $onSuccess('Projekt byl úspěšně upraven.', 'success', $values->nwm);
            } else {
                $onSuccess('Projekt nebyl upraven.', 'danger', $values->nwm);
            }
        };

        return $form;
    }

    public function adminCreate(callable $onSuccess, $id) {
        $this->project = $this->projectM->getProjectsWhereId($id)->fetch();
        $students = $this->userM->getStudents();
        $consultants = ['' => '---'] + $this->userM->getConsultants();
        $oponents = ['' => '---'] + $this->userM->getOponents();
        $form = $this->factory->create();



        $form->addText('name', 'Jméno:')
                ->setEmptyValue((isset($this->project->Name)) ? $this->project->Name : '')
                ->addRule($form::MAX_LENGTH, 'Maximální velikost textu je %d znaků', 255);

        $form->addTextArea('desc', 'Popis projektu:')
                ->setEmptyValue((isset($this->project->Desc)) ? $this->project->Desc : '')
                ->addRule($form::MAX_LENGTH, 'Maximální velikost textu je %d znaků', 1000);

        $form->addSelect('user', 'Vyber studenta:', $students)->setValue((isset($this->project->User)) ? $this->project->User : null)->setRequired();

        $form->addSelect('consultant', 'Vyber konzultanta:', $consultants)->setValue((isset($this->project->Consultant)) ? $this->project->Consultant : null);

        $form->addSelect('oponent', 'Vyber oponenta', $oponents)->setValue((isset($this->project->Oponent)) ? $this->project->Oponent : null);




        $form->addText('url', 'Zadej url tvého projektu:')->setHtmlType('url')
                ->setEmptyValue((isset($this->project->Url)) ? $this->project->Url : '');

        $form->addHidden('nwm')->setDefaultValue($id);

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $res = $this->projectM->updateProject($values->nwm, array(
                'Name' => $values->name,
                'User' => $values->user,
                'Desc' => $values->desc,
                'Url' => $values->url
            ));
            if($values->consultant != ''){
                $this->projectM->updateProject($values->nwm, array(
                    'Consultant' => $values->consultant
                ));
            }
            if($values->oponent != ''){
                $this->projectM->updateProject($values->nwm, array(
                    'Oponent' => $values->oponent
                ));
            }
            if ($res) {
                $onSuccess('Projekt byl úspěšně upraven.', 'success');
            } else {
                $onSuccess('Projekt nebyl upraven.', 'danger');
            }
        };




        return $form;
    }

}
