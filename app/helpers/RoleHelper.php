<?php

namespace App\Helpers;

use Nette;
use Nette\Database\Context;
use Nette\Security\User;
use App\Model\ProjectManager;

final class RoleHelper {

    use Nette\SmartObject;

    /** @var database */
    private $database;

    /** @var user */
    private $user;

    /** @var projectmanager */
    private $projectM;

    public function __construct(Context $database, User $user, ProjectManager $projectM) {
        $this->database = $database;
        $this->user = $user;
        $this->projectM = $projectM;
    }

    /**
     * gets all project that belongs to current user roles and visibility of projects
     * $return $project returns project database object
     */
    public function GetProjectsByVisible($year = '') {
        if ($this->user->isAllowed('projects', 'private')) {
            return $this->projectM->showPublic(false)->showYear($year);
        } else {
            return $this->projectM->showPublic(true)->showYear($year);
        }
    }

}
