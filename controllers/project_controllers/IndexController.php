<?php

class IndexController extends AbstractController
{
    /** this method sends users to 'login.phtml' if they are not logged
     * @param null $data
     */
    public function loginAction($data = null)
    {
        if (!$this->isLogged()) {
            $opt = [
                'title'      => 'Login',
                'content'    => 'login.phtml'
            ];

            $this->indexAction($opt, $data);
        } else {
            $this->redirect('/');
        }
    }

    /**
     * his method sends users to 'register.phtml' if they are not logged
     * @param null $data
     */
    public function registerAction($data = null){
        if (!$this->isLogged()) {
            $opt = [
                'title'     => 'Registration',
                'content'   => 'register.phtml'
            ];
            $indexModel = new IndexModel();
            $data['roles'] = $indexModel->getFamilyRoles();
            $this->indexAction($opt, $data);
        } else {
            $this->redirect('/');
        }
    }

    /**
     * this method receives POST data from the form in 'register.phtml'
     * if everything is Ok redirect user to 'user_main.phtml' page
     * if not returns errors msgs
     */
    public function newAction()
    {
        if(!$this->isLogged()) {
            if ($this->isPost()) {
                $familyRole = explode('/', $_POST['family_role']);
                $roleId = (int)$familyRole[0];
                $roleName = ucfirst($familyRole[1]);
                $data = [
                    'name' => $_POST['userName'],
                    'password' => $_POST['userPassword'],
                    'role_id' => $roleId,
                    'role_name' => $roleName
                ];
                $model = new IndexModel();
                $result = $model->newUser($data);
                if (in_array(false, $result)) {
                    $this->registerAction($result);
                } else {
                    $this->redirect("/");
                }
            } else {
                $this->redirect('/index/register/');
            }
        } else {
            $this->redirect('/');
        }
    }

    /**
     *  this method receives POST data from the form in 'login.phtml'
     * if everything is Ok redirect user to 'user_main.phtml' page
     * if not returns errors msgs
     */
    public function authAction()
    {
        if (!$this->isLogged()) {
            if ($this->isPost()) {
                $data = [
                    'name' => $_POST['userName'],
                    'password' => $_POST['userPassword']
                ];
                $model = new IndexModel();
                $result = $model->authUser($data);
                if ($result === true) {
                    $this->redirect("/");
                } else {
                    $this->loginAction($result);
                }
            } else {
                $this->redirectLogin();
            }
        } else {
            $this->redirect("/");
        }
    }
}