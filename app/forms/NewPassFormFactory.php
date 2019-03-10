<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewPassFormFactory {

    use Nette\SmartObject;

    const PASSWORD_MIN_LENGTH = 7;

    /** @var FormFactory */
    private $factory;

    /** @var Model\UsersManager */
    private $usersManager;
    private $user;

    public function __construct(FormFactory $factory, Model\UsersManager $usersManager, \Nette\Security\User $user, Nette\Database\Context $database) {
        $this->factory = $factory;
        $this->usersManager = $usersManager;
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $id) {
        $form = $this->factory->create();
        $form->getElementPrototype()->autocomplete = 'off';

        $form->addPassword('new_password', 'Vytvoř si heslo:')
                ->setOption('description', sprintf('zadej minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
                ->setRequired('Prosím zadej nové heslo')
                ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);


        $form->addPassword('rep_password', 'Zadej znovu heslo:')
                ->setRequired('Prosím zadej znovu nové heslo')
                ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH)
                ->addRule($form::EQUAL, 'Špatně znovu zadané heslo', $form['new_password']);
        
        $form->addHidden('nwm', $id);

        $form->addSubmit('send', 'Změnit');
        
        
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess){
            
            if($values->new_password == $values->rep_password){
                $row = $this->usersManager->updatePass(Nette\Security\Passwords::hash($values->new_password), $values->nwm);
                if($row){
                    $onSuccess('Heslo bylo změněno.','success');
                }else{
                    $onSuccess('Heslo nebylo změněno','danger');
                }
            }else{
                $form->addError('Hesla se neshodují');
            }
            
            
            
        };

        return $form;
    }

}
