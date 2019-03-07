<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UsersManager;
use Nette\Mail\Message;
use Nette\Security\Passwords;
use Latte\Engine;
use Nette\Bridges\ApplicationLatte\UIMacros;

class GetEmailFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    private $usersManager;
    private $mailer;
    
    public function __construct(FormFactory $factory, UsersManager $usersManager, \Nette\Mail\IMailer $mailer) {
        $this->factory = $factory;
        $this->usersManager = $usersManager;
        $this->mailer = $mailer;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = $this->factory->create();
        $form->addEmail('email', 'Email:')
                ->setRequired('Please enter your username.');

        $form->addSubmit('send', 'odeslat');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $user = $this->usersManager->getUserByEmail($values->email)->fetch();

            if ($user) {
                //user exists then send mail
                $now = new Nette\Utils\DateTime();
                $hash = Passwords::hash($now . $values->email);


                $message = new Message();
                $params = [
                    'username' => $user->Username,
                    'token' => $hash,
                    'sitename' => 'localhost',
                ];
                $latte = new Engine();
                $latte->addProvider("uiControl", $this);
                $latte->addProvider("uiPresenter", $this);
                UIMacros::install($latte->getCompiler());
                $message->setFrom('noreply@Maturitniprojekty.com')
                        ->addTo('jezancz.22@gmail.com')
                        ->setSubject('Zapomenuté heslo')
                        ->setHtmlBody($latte->renderToString(__DIR__ . '/rememberMail.latte', $params, null));
                
                
                $this->mailer->secure = 'TSL';
                $mail->Port = 587;
                $this->mailer->send($message);
                $onSuccess('Uspěšně odesláno', 'success');
            } else {
                $form['email']->addError('Tento email není v databázi');
                return;
            }
        };


        return $form;
    }

}
