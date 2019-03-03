<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;

final class SignPresenter extends BasePresenter {

    /**
     * @var Forms\SignInFormFactory
     */
    private $signInFactory;
    private $user;

    public function __construct(Forms\SignInFormFactory $sign, \Nette\Security\User $user) {
        $this->signInFactory = $sign;
        $this->user = $user;
    }

    public function renderIn() {
        
    }

    public function renderOut() {
        
    }

    protected function createComponentSignUpForm() {
        return $this->signInFactory->create(function ($message = '') {
                    $this->flashMessage($message,'success');
                    $this->redirect('Homepage:');
                });
    }

    public function actionOut() {
        $this->user->logout();
        $this->flashMessage('Byl úspěšně odhlášen.');
        $this->redirect('Homepage:');
    }

}
