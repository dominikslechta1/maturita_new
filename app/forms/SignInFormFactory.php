<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Model;

final class SignInFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var User */
    private $user;
    private $message = '';

    public function __construct(FormFactory $factory, User $user) {
        $this->factory = $factory;
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = $this->factory->create();
        $form->addEmail('email', 'Username:')
                ->setRequired('Please enter your username.');

        $form->addPassword('password', 'Password:')
                ->setRequired('Please enter your password.');

        $form->addCheckbox('remember', 'Keep me signed in');

        $form->addSubmit('send', 'Sign in');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {

            try {
                $this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
                $this->user->login($values->email, $values->password);
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Špatné jméno nebo heslo. ');
                $this->message = $e;

                return;
            }
            $onSuccess('Byl Jsi úspěšně přihlášen');
        };

        return $form;
    }

}
