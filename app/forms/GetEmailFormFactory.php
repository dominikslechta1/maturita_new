<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UsersManager;
use Nette\Security\Passwords;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\LinkGenerator;

class GetEmailFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    private $usersManager;

    /** @var Nette\Application\UI\ITemplateFactory */
    private $templateFactory;

   /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;
    
    
    public function __construct(LinkGenerator $linkGenerator, FormFactory $factory, UsersManager $usersManager, ITemplateFactory $templateFactory) {
        $this->factory = $factory;
        $this->usersManager = $usersManager;
        $this->templateFactory = $templateFactory;
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $sender) {
        $form = $this->factory->create();
        $form->addEmail('email', 'Email:')
                ->setRequired('Please enter your username.');

        $form->addSubmit('send', 'odeslat');

        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess, $sender) {
            $user = $this->usersManager->getUserByEmail($values->email)->fetch();

            if ($user) {
                //user exists then send mail
                $now = new Nette\Utils\DateTime();
                $hash = Passwords::hash($now . $values->email);
                $new_time = date_add($now, date_interval_create_from_date_string('24 hours'));

                $res = $this->usersManager->insertTokken($user->idUser, $hash, $new_time);

                if ($res) {

                    $template = $this->createTemplate();

                    $template->sitename = gethostname();
                    $template->username = $user->idUser;
                    $template->token = $hash;
                    $template->setFile(__DIR__ . '/rememberMail.latte');




                    $message = new \Nette\Mail\Message;
                    $message->setSubject('Zapomenuté heslo')
                            ->setFrom('Maturitní projekty <maturitni.projekty@noreply.com>')
                            ->addTo($user->Email)
                            ->setHtmlBody($template);
                    try{
                    $sender->send($message);
                    }catch(\Nette\Mail\SendException $e){
                        $onSuccess('Email nebyl odeslán', 'danger');
                    }
                    $onSuccess('Email byl odeslán','success');
                } else {
                    throw new Exception('tokken nebyl uložen');
                }
            } else {
                $form['email']->addError('Tento email není v databázi');
                return;
            }
        };


        return $form;
    }

    protected function createTemplate() {
        $template = $this->templateFactory->createTemplate();
        $template->getLatte()->addProvider('uiControl', $this->linkGenerator);

        return $template;
    }

}
