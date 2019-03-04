<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class ChangePassFormFactory {

    use Nette\SmartObject;

    const PASSWORD_MIN_LENGTH = 7;

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
        $form->addPassword('old_password', 'Zadej staré heslo:')
                ->setRequired('Prosím zadej staré heslo');


        $form->addPassword('new_password', 'Vytvoř si heslo:')
                ->setOption('description', sprintf('zadej minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
                ->setRequired('Prosím zadej nové heslo')
                ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);


        $form->addPassword('rep_password', 'Vytvoř si heslo:')
                ->setOption('description', sprintf('zadej minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
                ->setRequired('Prosím zadej znovu nové heslo')
                ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH)
                ->addRule($form::EQUAL, 'Špatně znovu zadané heslo', $form['new_password']);

        $form->addSubmit('send', 'Změnit');
        $form->onValidate[] = function (Form $form, $values) {
            if (!$this->checkPassword($values->old_password)) {
                $form['old_password']->addError('Špatně zadané staré heslo');
            }
            if($values->new_password !== $values->rep_password){
                $form['rep_password']->addError('Hesla se neshodují');
            }
        };
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess){
            
            $this->database->table('m_users')->get($this->user->getIdentity()->getId())->update([
                'Password' => \Nette\Security\Passwords::hash($values->new_password)
            ]);
            
            
            $onSuccess('Úspěšně jsi změnil své heslo.');
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
