<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class ChangeUsernameFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var Model\UserManager */
    private $userManager;
    private $user;
    private $database;

    public function __construct(FormFactory $factory, Model\UserManager $userManager, \Nette\Security\User $user, Nette\Database\Context $database) {
        $this->factory = $factory;
        $this->userManager = $userManager;
        $this->user = $user;
        $this->database = $database;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = $this->factory->create();
        $form->getElementPrototype()->autocomplete = 'off';
        
        $form->addText('username', 'Zadej nové uživatelské jméno: ')
                ->setRequired('Prosím zadej nové uživatelské jméno')
                ->addRule($form::MAX_LENGTH, 'délka', 254);

        $form->addSubmit('send', 'Změnit');
        $form->onValidate[] = function (Form $form, $values) {
            if($values->username > 254){
                $form['username']->addError('Prekročil jsi maximální počet znaků');
            }
        };
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess){
            
            $this->database->table('m_users')->get($this->user->getIdentity()->getId())->update([
                'Username' => $values->username
            ]);
            
            $this->user->getIdentity()->username = $values->username;
            
            
            $onSuccess('Úspěšně jsi změnil své uživatelské jméno.');
        };


        return $form;
    }

    public function checkPassword($pass) {
        if ($this->user->isLoggedIn()) {
            $res = $this->database->table('m_users')->get($this->user->getIdentity()->getId());
            if (password_verify($pass, $res->Password)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
