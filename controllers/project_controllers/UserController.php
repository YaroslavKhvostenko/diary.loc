<?php
class UserController extends AbstractController
{
    /**
     * destroys session and redirect to main page
     */
    public function logoutAction()
    {
        if ($this->isLogged()) {
            session_destroy();
            $this->redirect("/");
        } else {
            $this->redirectLogin();
        }
    }

    /**
     * if user logged sends him to 'diary_main.phtml'
     * if not logged , redirect to login page
     */
    public function diaryAction()
    {
        if ($this->isLogged()) {
            $opt = [
                'title' => 'Diary',
                'content' => 'diary_main.phtml'//it means we have logged user
            ];
            $this->indexAction($opt);
        } else {
            $this->redirectLogin();
        }
    }
}