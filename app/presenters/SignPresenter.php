<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use Nette\Mail\IMailer;

final class SignPresenter extends BasePresenter {

    /**
     * @var Forms\SignInFormFactory
     */
    private $signInFactory;
    private $user;
    private $getEmailFormFactory;
    private $sender;

    public function __construct(IMailer $sender, Forms\SignInFormFactory $sign, \Nette\Security\User $user, Forms\GetEmailFormFactory $getEmailFormFactory) {
        $this->signInFactory = $sign;
        $this->user = $user;
        $this->getEmailFormFactory = $getEmailFormFactory;
        $this->sender = $sender;
    }

    public function renderIn() {
        
    }

    public function renderOut() {
        
    }

    public function renderRenewpass() {
        
    }

    protected function createComponentSignUpForm() {
        return $this->signInFactory->create(function ($message = '') {
                    $this->flashMessage($message, 'success');
                    $this->redirect('Homepage:');
                });
    }

    protected function createComponentRenewPassForm() {
        return $this->getEmailFormFactory->create(function ($message = '', $type = 'info') {
                    $this->flashMessage($message, $type);
                    //$this->redirect('Homepage:');
                }, $this->sender
        );
    }

    public function actionOut() {
        $this->user->logout();
        $this->flashMessage('Byl úspěšně odhlášen.');
        $this->redirect('Homepage:');
    }

}
