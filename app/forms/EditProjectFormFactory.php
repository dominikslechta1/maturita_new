<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Model\ProjectManager;

final class EditProjectFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var User */
    private $user;
    private $message = '';
    private $projectM;
    private $project;

    public function __construct(FormFactory $factory, User $user, ProjectManager $projectM) {
        $this->factory = $factory;
        $this->user = $user;
        $this->projectM = $projectM;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $id) {
        $this->project = $this->projectM->getProjectsWhereId($id)->fetch();
        $form = $this->factory->create();
        
        $form->addTextArea('desc', 'Uprav popis projektu:')
                ->setEmptyValue((isset($this->project->Desc))?$this->project->Desc:'')
                ->addRule($form::MAX_LENGTH, 'Maximální velikost textu je 1000 znaků', 1000);
        $form->addText('url', 'Zadej url tvého projektu:')->setHtmlType('url')
                ->setEmptyValue((isset($this->project->Url))?$this->project->Url:'');
        $form->addHidden('nwm')->setDefaultValue($id);
        
        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $res = $this->projectM->updateProject($values->nwm, array('Desc'=> $values->desc, 'Url' => $values->url));
            if($res){
                $onSuccess('Projekt byl úspěšně upraven.', 'success', $values->nwm);
            }else{
                $onSuccess('Projekt nebyl upraven.', 'danger', $values->nwm);
            }
            
        };

        return $form;
    }

}
