<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UsersManager;
use App\Model\FileTypeManager;
use App\Model\FilesManager;
use Nette\Security\User;

final class AddFileFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    private $userM;
    private $fileTypeManager;
    private $fileManager;
    private $user;

    public function __construct(User $user, FormFactory $factory, UsersManager $userM, FileTypeManager $fileTypeManager, FilesManager $filesM) {
        $this->factory = $factory;
        $this->userM = $userM;
        $this->fileTypeManager = $fileTypeManager;
        $this->fileManager = $filesM;
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $projectId) {
        $form = $this->factory->create();

        $acex = $this->fileManager->acceptedExtension();
        $acexstring = implode(",", $acex);


        $form->addText('name', 'Zadej název souboru:')
                ->setRequired('Prosím zadej název souboru.');

        $form->addTextArea('desc', 'Přidej popis:')
                ->addRule($form::MAX_LENGTH, 'Maximální počet je %d znaků', 255);

        $form->addUpload('file', 'Přidej soubor:')
                ->setAttribute('accept', $acexstring)
                ->setRequired('Prosím přidej soubor.')
                ->setAttribute('onchange', ' getFileData(this)')
                ->addCondition(Form::FILLED);


        $form->addSubmit('send', 'Přidat soubor');




        $form->addHidden('nwm', $projectId);


        $form->onValidate[] = function(Form $form, $values) {
            $file_ext;
            if ($values->file->isOk()) {
                $file_ext = strtolower(
                        mb_substr(
                                $values->file->getSanitizedName(), strrpos(
                                        $values->file->getSanitizedName(), "."
                                )
                        )
                );
            }
            $field = $this->fileManager->acceptedExtension();
            if (isset($values->file) && !in_array($file_ext, $field)) {
                $form['file']->addError('Soubor obsahuje neplatné přípony ' . $file_ext);
            }
        };



        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            if ($this->user->isAllowed('files', 'add')) {
                if ($values->file->isOk()) {
                    //extension
                    $file_ext = strtolower(
                            mb_substr(
                                    $values->file->getSanitizedName(), strrpos(
                                            $values->file->getSanitizedName(), "."
                                    )
                            )
                    );
                }
                //new name with rnd name
                $uniqueName = uniqid(rand(0, 20), TRUE);

                $values->file->move('files/' . $uniqueName . $file_ext);

                $arr = [
                    'hash' => $uniqueName,
                    'fileextid' => $this->fileTypeManager->getFileExtId($file_ext),
                    'name' => $values->name,
                    'desc' => $values->desc,
                    'project' => $values->nwm,
                    'user' => $this->user->getIdentity()->getId()
                ];
                
                if (!$arr) {
                    throw new \UnexpectedValueException();
                }
                if($this->fileManager->insertFile($arr)){
                    $onSuccess('Projekt byl úspěšně uložen.', 'success');
                } else {
                    $onSuccess('Projekt nebyl uložen.', 'danger');
                }
                

                
            }
        };




        return $form;
    }

}
