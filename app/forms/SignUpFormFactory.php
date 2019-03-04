<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use App\Model\PrivilegeManager;

final class SignUpFormFactory {

    use Nette\SmartObject;

    const PASSWORD_MIN_LENGTH = 7;

    /** @var FormFactory */
    private $factory;

    /** @var Model\UserManager */
    private $userManager;

    /*     * @var PrivilegeManager */
    private $privilege;

    public function __construct(FormFactory $factory, Model\UserManager $userManager, PrivilegeManager $privilege) {
        $this->factory = $factory;
        $this->userManager = $userManager;
        $this->privilege = $privilege;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = $this->factory->create();
        $form->getElementPrototype()->autocomplete = 'off';
        
        $form->addText('firstname', 'Zadej křestní jméno:')
                ->setRequired('Prosím zadej křestní jméno.');

        $form->addText('surname', 'Zadej příjmení:')
                ->setRequired('Prosím zadej příjmení.');

        $form->addEmail('email', 'Zadej e-mail:')
                ->setRequired('Prosím zadej svůj e-mail.');

        $form->addPassword('password', 'Vytvoř heslo:')
                ->setOption('description', sprintf('zadej minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
                ->setRequired('Prosím zadej heslo')
                ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);

        $form->addMultiSelect('roles', 'vyber role uživatele', $this->privilege->getPrivileges());

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            try {
                $this->userManager->add($values->firstname, $values->firstname, $values->email, $values->password, $values->roles);
            } catch (\Nette\Database\UniqueConstraintViolationException $e) {
                $form['email']->addError('Tento e-mail již existuje.');
                return;
            }
            $onSuccess();
        };

        return $form;
    }

}
