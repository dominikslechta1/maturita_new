<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Model\UsersManager;
use App\Model\PrivilegeManager;

final class EditUserFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var User */
    private $user;
    
    private $userM;
    private $userPrivilegesManager;
    private $privilegeManager;

    public function __construct(PrivilegeManager $privilegeManager,FormFactory $factory, User $user, UsersManager $userm, \App\Model\UserPrivilegesManager $userPrivilegesManager) {
        $this->factory = $factory;
        $this->user = $user;
        $this->userM = $userm;
        $this->userPrivilegesManager = $userPrivilegesManager;
        $this->privilegeManager = $privilegeManager;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $id) {
        $user = $this->userM->getUserById($id);
        $privileges = $this->privilegeManager->getPrivileges();
        $userPrivileges = $this->userPrivilegesManager->getUserPrivilegeIds($id);
        $form = $this->factory->create();

        $form->addText('username','Uživatelské jméno:')->setDefaultValue($user->Username);
        $form->addText('name','Křestní jméno:')->setDefaultValue($user->Firstname);
        $form->addText('surname', 'Příjmení:')->setDefaultValue($user->Surname);
        $form->addEmail('email','Email:')->setDefaultValue($user->Email);
        $form->addMultiSelect('roles','Oprávnění:',$privileges)->setDefaultValue($userPrivileges);
        
        
        
        
        $form->addHidden('nwm',$id);

        $form->addSubmit('send', 'Uložit');
        
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $res = $this->userM->update($values->nwm, [
                'Username' => $values->username,
                'Firstname' => $values->name,
                'Surname' => $values->surname,
                'Email' => $values->email
            ]);
            
            if($this->userPrivilegesManager->deletePrivilegeOnUser($values->nwm)){
                $res = $this->userPrivilegesManager->insertPrivilege($values->nwm, $values->roles);
            }
            if($res){
                $onSuccess('Uživatel byl upraven', 'success');
            }else{
                $onSuccess('Uživatel nebyl upraven', 'danger');
            }
        };


        return $form;
    }


}
