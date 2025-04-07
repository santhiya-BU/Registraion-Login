<?php
// src/Controller/UsersController.php

declare(strict_types=1);

namespace App\Controller;

use Authentication\Controller\Component\AuthenticationComponent;
use Cake\Http\Cookie\Cookie;
use DateTime;

class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->addUnauthenticatedActions(['login', 'register']);
        $this->loadModel('LoginHistories');
    }

    public function register()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success('Registered successfully.');
                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('Registration failed.');
        }
        $this->set(compact('user'));
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
   
        if ($result->isValid()) {
            $user = $this->request->getAttribute('identity');
            print_r($user->id);
            $this->_trackLogin($user->id);

            // Set secure cookie (replacing deprecated CookieComponent)
            $cookie = new Cookie(
                'user_id',
                (string)$user->id,
                new DateTime('+1 day'), // 👈 expires must be DateTime
                '/',
                '',
                false,
                true, // httpOnly
                null,
                'Lax'
            );
            $this->response = $this->response->withCookie($cookie);

            $this->request->getSession()->write('user_id', $user->id);

            return $this->redirect(['action' => 'dashboard'])->withCookie($cookie);;
        }

        if ($this->request->is('post')) {
            $this->Flash->error('Invalid credentials');
        }
    }

    private function _trackLogin($userId)
    {
        $login = $this->LoginHistories->newEntity([
            'user_id' => $userId,
            'ip_address' => $this->request->clientIp(),
        ]);
        $this->LoginHistories->save($login);
    }

    public function dashboard()
    {
        $user = $this->request->getAttribute('identity');
        $loginHistory = $this->LoginHistories->find()
            ->where(['user_id' => $user->id])
            ->order(['created' => 'DESC'])
            ->all();

        $this->set(compact('loginHistory', 'user'));
    }
}
