<?php
// src/Model/Table/UsersTable.php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->hasMany('LoginHistories');
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->notEmptyString('username')
            ->notEmptyString('email')
            ->email('email')
            ->notEmptyString('password')
            ->notEmptyString('name');
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isDirty('password')) {
            $hasher = new DefaultPasswordHasher();
            $entity->password = $hasher->hash($entity->password);
        }
    }
}