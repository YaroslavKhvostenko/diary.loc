<?php
class DiaryController extends AbstractController
{
    /**
     * sends logged user to tasks page with his tasks, if tasks empty returns `empty msg'
     * @param null $data
     */
    public function tasksAction($data = null)
    {
        if ($this->isLogged()) {
            $opt = [
                'title'     => 'Diary',
                'content'   => 'diary.phtml'//
            ];
            $diaryModel = new DiaryModel();
            $data['tasks'] = $diaryModel->getLoggedUserTasks($this->loggedUser['id']);
            if (empty($data['tasks'])) {
                $data['empty'] = 'You dont have any tasks yet!';
            }
            $this->indexAction($opt, $data);

        } else {
            $this->redirectLogin();
        }
    }

    /**
     * updating task execution status , if OK returns 'success msg'
     * if not OK , receive 'error msg' and returns it to user
     */
    public function updateAction()
    {
        if ($this->isLogged()) {
            if ($this->isPost()) {
                $diaryModel = new DiaryModel();
                $data = [
                    'task_id' => $_POST['task_id'],
                    'id'      => $this->loggedUser['id']
                ];
                $result = $diaryModel->updateTaskStatus($data, 'doneUpdate');
                if ($result['result'] == true) {
                    $result['success'] = 'Good Job!Finally you finished! ;)';
                }
                $this->tasksAction($result);
            } else {
                $this->redirect('/diary/tasks/');
            }
        } else {
            $this->redirectLogin();
        }
    }

    /**
     * sends user to phtml page 'diary_add.phtml' with form for uploading file
     * @param null $data
     */
    public function addAction($data = null)
    {
        if ($this->isLogged()) {
            $opt = [
                'title'     => 'Diary',
                'content'   => 'diary_add.phtml'//
            ];
            $this->indexAction($opt, $data);
        } else {
            $this->redirectLogin();
        }
    }

    /**
     * receives file from 'dairy_add.phtml' form, try to parse it and insert into DB
     * if OK returns 'success msg'
     * if not OK receive 'error msg' and returns it to user
     */
    public function uploadAction()
    {
        if ($this->isLogged()) {
            if ($this->isPost()) {
                $diaryModel = new DiaryModel();
                $result = $diaryModel->uploadTasks();
                if (in_array(false, $result)) {
                    $this->addAction($result);
                } else {
                    $result['success'] = 'File with tasks has been added!';
                    $this->addAction($result);
                }
            } else {
                $this->redirect('/diary/add/');
            }
        } else {
            $this->redirectLogin();
        }
    }

    /**
     * sends user to 'diary_distribute.phtml' where he can distribute tasks between family members
     * @param null $data can be null , or a data with 'success msg' or 'error msg'
     */
    public function distributeAction($data = null)
    {
        if ($this->isLogged()) {
            $opt = [
                'title'     => 'Diary',
                'content'   => 'diary_distribute.phtml'//
            ];

            $diaryModel = new DiaryModel();
            $data['tasks'] = $diaryModel->getAllTasks();
            if (empty($data['tasks'])) {
                $data['empty'] = 'You dont have any tasks yet to distribute!';
                $this->indexAction($opt, $data);
            }
            $userModel = new UserModel();
            $data['usersData'] = $userModel->getAllUsersData();
            $this->indexAction($opt, $data);
        } else {
            $this->redirectLogin();
        }
    }

    /**
     * user signs another family member to none done task
     * if OK returns 'success msgs'
     * if not OK receive 'error msg' and returns it
     */
    public function signAction()
    {
        if ($this->isLogged()) {
            if ($this->isPost()) {
                $idData = explode('/', $_POST['id']);
                $taskId = (int)$idData[0];
                $userId = (int)$idData[1];
                $data = [
                    'task_id' => $taskId,
                    'id'      => $userId
                ];
                $diaryModel = new DiaryModel();
                $result = $diaryModel->updateTaskStatus($data, 'signUpdate');
                if ($result['result'] == true) {
                    $result['success'] = 'Task has been successfully distributed! ;)';
                }
                $this->distributeAction($result);
            } else {
                $this->redirect('/diary/distribute/');
            }
        } else {
            $this->redirectLogin();
        }
    }

}