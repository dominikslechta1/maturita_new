<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UsersManager;
use App\Model\FileTypeManager;
use App\Model\FilesManager;
use Nette\Security\User;
use App\Model\ProjectManager;

final class AddFileFormFactory {

    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    private $fileTypeManager;
    private $fileManager;
    private $user;
    private $projectManager;

    public function __construct(ProjectManager $projectManager, User $user, FormFactory $factory, UsersManager $userM, FileTypeManager $fileTypeManager, FilesManager $filesM) {
        $this->factory = $factory;
        $this->fileTypeManager = $fileTypeManager;
        $this->fileManager = $filesM;
        $this->user = $user;
        $this->projectManager = $projectManager;
    }

    /**
     * @return Form
     */
    public function create(callable $onSuccess, $projectId) {
        $form = $this->factory->create();

        $acex = $this->fileTypeManager->acceptedExtension();
        $acexstring = implode(",", $acex);



        $form->addText('name', 'Zadej název souboru:')
                ->setRequired('Prosím zadej název souboru.');

        $form->addTextArea('desc', 'Přidej popis:')
                ->addRule($form::MAX_LENGTH, 'Maximální počet je %d znaků', 255);

        $form->addUpload('file', 'Přidej soubor:')
                ->setAttribute('accept', $acexstring)
                ->setRequired('Prosím přidej soubor.')
                ->setAttribute('onchange', ' getFileData(this)');

        $form->addCheckbox('reqfile', 'Přidat jako požadovaný soubor')
                ->setHtmlAttribute('onchange', ' ChboxDisable(this, 1)');

        $form->addCheckbox('reqfilepdf', 'Přidat jako požadovaný soubor v pdf');

        $form->addSubmit('send', 'Přidat soubor');




        $form->addHidden('nwm', $projectId);


        $form->onValidate[] = function(Form $form, $values) {
            $file_ext = "";
            if ($values->file->isOk()) {
                $file_ext = strtolower(
                        mb_substr(
                                $values->file->getSanitizedName(), strrpos(
                                        $values->file->getSanitizedName(), "."
                                )
                        )
                );
            }
            $field = $this->fileTypeManager->acceptedExtension();
            if (isset($values->file) && !in_array($file_ext, $field)) {
                $form['file']->addError('Soubor obsahuje neplatné přípony ' . $file_ext);
            }
            if ($values->reqfile && $values->reqfilepdf) {
                $form['reqfile']->addError('Nesmějí být zaškrtlá obě políčka najednou.');

                return;
            }

            if ($values->reqfile) {
                $field = $this->fileTypeManager->getExtByGroup('word');
                if (isset($values->file) && !in_array($file_ext, $field)) {
                    $form['file']->addError('Povinný soubor obsahuje neplatné přípony ' . $file_ext);
                }
            } elseif ($values->reqfilepdf) {
                $field = $this->fileTypeManager->getExtByGroup('pdf');
                if (isset($values->file) && !in_array($file_ext, $field)) {
                    $form['file']->addError('Povinný soubor v pdf obsahuje neplatné přípony ' . $file_ext);
                }
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

                $this->fileManager->insertFile($arr);
                $row = $this->fileManager->getMaxId();
                if ($values->reqfile) {
                    $this->projectManager->updateProject($values->nwm, [
                        'Rqfile' => $row
                    ]);
                } elseif ($values->reqfilepdf) {
                    $this->projectManager->updateProject($values->nwm, [
                        'Rqfilepdf' => $row
                    ]);
                }

                if ($row) {
                    $onSuccess('Projekt byl úspěšně uložen.', 'success');
                } else {
                    $onSuccess('Projekt nebyl uložen.', 'danger');
                }
            }
        };




        return $form;
    }

}
