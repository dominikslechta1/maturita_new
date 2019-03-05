<?php

namespace App\Model;

use Nette\Security\Permission;

class AuthorizatorFactory extends \Nette\Security\Permission {

    /**
     * @return \Nette\Security\Permission
     */
    public static function create() {
        $acl = new \Nette\Security\Permission;

        // pokud chceme, můžeme role a zdroje načíst z databáze
        $acl->addRole('guest');
        $acl->addRole('student');
        $acl->addRole('consultant', 'student');
        $acl->addRole('oponent', 'consultant');
        $acl->addRole('administrator');

        $acl->addResource('projects');
        $acl->addResource('project');
        $acl->addResource('files');
        $acl->addResource('score');
        $acl->addResource('users');

        $acl->deny(self::ALL, self::ALL, ['manage']);
        $acl->allow('administrator');
        $acl->allow('guest', ['projects', 'project'], ['public', 'view']);
        $acl->allow('guest', ['score'], ['view']);
        $acl->deny('guest', ['files'], ['view']);
        $acl->deny('guest', 'users');
        $acl->deny('guest', ['score', 'files', 'project', 'projects'], ['edit', 'add', 'unlocklock', 'visibility', 'private', 'delete', 'addfile', 'manage']);

        $acl->allow('student', ['projects', 'project'], ['editdesc', 'view', 'addfile']);
        $acl->deny('student', ['projects', 'project'], ['add', 'delete', 'edit', 'unlocklock', 'visibility', 'manage']);
        $acl->allow('student', ['score'], ['view']);
        $acl->deny('student', ['score'], ['edit', 'add', 'delete']);
        $acl->allow('student', ['files'], ['view', 'edit', 'add', 'delete']);
        $acl->deny('student', ['users'], ['add', 'edit', 'view', 'delete']);

        $acl->allow('consultant', ['score'], ['add', 'edit', 'delete']);




        return $acl;
    }

}
