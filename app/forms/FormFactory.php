<?php

namespace App\Forms;

use Nette;
use Czubehead\BootstrapForms\BootstrapForm;

final class FormFactory {

    use Nette\SmartObject;

    /**
     * @return BootstrapForm
     */
    public function create() {
        $form = new BootstrapForm();
        return $form;
    }

}
