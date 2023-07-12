<?php

abstract class AbstractController
{
    /**
     * default views
     * @var ViewModel
     */
    protected $view;

    /**
     * @var mixed|null
     */
    protected $session;

    /**
     * @var mixed|null
     */
    protected $loggedUser;

    /**
     * AbstractController constructor.
     */
    public function __construct()
    {
        $this->view     = new ViewModel();
        $this->session  = &$_SESSION[md5(session_id())] ? $_SESSION[md5(session_id())] : null;
        $this->loggedUser     = $this->session ? $this->getUserData($this->session['id']) : null;
    }

    /**
     * @param null $opt
     * @param null $data
     */
    public function indexAction($opt = null, $data = null)
    {
        if ($this->loggedUser && !$opt)//indexaction without params return content usermain
            $opt = [
                'title'     => 'Main',
                'content'   => 'user_main.phtml'//it means we have logged user
            ];

        $opt = $this->view->getPageOptions($opt);
        $this->view->render($opt, $this->loggedUser, $this->session, $data);
    }

    /**
     * method that calls phtml error page 'error404.phtml'
     */
    public function notFoundAction()
    {
        $this->indexAction($this->getNotFoundOpt());
    }

    /**
     * check is user logged in
     * @return bool
     */
    protected function isLogged()
    {
        return $this->loggedUser ? true : false;
/*        if (isset($this->_user))
            return true;

        $this->redirect("/user/login/");*/
    }


 /*   protected function isSigned()
    {
        if ($this->user){
            $this->redirect('/');
            return true;
        }
        return false;
    }*/

    /**
     * receive logged user data according to his id
     * @param $loggedUserId
     * @return mixed
     */
    protected function getUserData($loggedUserId)
    {
        $model = new UserModel();
        return $model->getUserInfo($loggedUserId);
    }

    /**
     * @return string[]
     */
    protected function getNotFoundOpt()
    {
        return [
            'title'   => 'Page not found!',
            'content' => 'error404.phtml'
        ];
    }

    /**
     * @param $url
     */
    protected function redirect($url)
    {
        header("Location: " . $url);
    }

    /**
     * redirect non logged users to login page
     */
    protected function redirectLogin()
    {
        header("Location: /index/login/");
    }

    /**
     * checking for receiving data being sent by POST
     * @return bool
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}
