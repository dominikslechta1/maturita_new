<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UsersManager;
use Nette\Security\User;
use App\Model\ProjectManager;

final class AddProjectFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    private $userM;
    
    private  $projectM;


    public function __construct(FormFactory $factory, UsersManager $userM, ProjectManager $projectM) {
        $this->factory = $factory;
        $this->userM = $userM;
        $this->projectM = $projectM;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = $this->factory->create();
        $students = $this->userM->getStudents();
        $consultants = $this->userM->getConsultants();
        $oponents = $this->userM->getOponents();
        
        $form->addText('name', 'Zadej název projektu:')
                ->setRequired();
        
        $form->addSelect('user', 'Vyber studenta:',$students)->setRequired();
        
        $form->addSelect('consultant', 'Vyber konzultanta:',array_merge([-1=>'---'], $consultants));
        
        $form->addSelect('oponent', 'Vyber oponenta',array_merge([-1=>'---'], $oponents));

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $res = $this->projectM->addProject($values);
            if($res){
                $onSuccess('Projekt byl úspěšně uložen.','success');
            }else{
                $onSuccess('Projekt nebyl uložen', 'danger');
            }
        };

        return $form;
    }

}
