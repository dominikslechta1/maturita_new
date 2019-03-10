<?php

namespace App\Presenters;

use Tracy\Debugger;
use App\Forms;
use \Nette\Security\User;
use Nette\Mail\IMailer;
use App\Model\UsersManager;

final class SignPresenter extends BasePresenter {

    /**
     * @var Forms\SignInFormFactory
     */
    private $signInFactory;
    private $user;
    private $getEmailFormFactory;
    private $sender;
    private $newPassFormFactory;
    private $userManager;
    private $id;

    public function __construct(UsersManager $userManager, Forms\NewPassFormFactory $newPassFormFactory, IMailer $sender, Forms\SignInFormFactory $sign, \Nette\Security\User $user, Forms\GetEmailFormFactory $getEmailFormFactory) {
        $this->signInFactory = $sign;
        $this->user = $user;
        $this->getEmailFormFactory = $getEmailFormFactory;
        $this->sender = $sender;
        $this->newPassFormFactory = $newPassFormFactory;
        $this->userManager = $userManager;
    }

    public function actionNewpass($username,$token){
        $user = $this->userManager->getUserById($username);
        if($user){
            $this->id = $user->idUser;
            $now = new \Nette\Utils\DateTime();
            if($user->Tokken == $token && ($user->Tocdate) > $now){
                $this->template->can = true;
            }else{
                $this->template->can = false;
            }
        }
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
                    $this->redirect('Homepage:');
                }, $this->sender
        );
    }

    protected function createComponentNewPassForm() {
        return $this->newPassFormFactory->create(function ($message = '', $type = 'info') {
                    $this->flashMessage($message, $type);
                    $this->redirect('Homepage:');
                },$this->id);
    }

    public function actionOut() {
        $this->user->logout();
        $this->flashMessage('Byl úspěšně odhlášen.');
        $this->redirect('Homepage:');
    }

}
