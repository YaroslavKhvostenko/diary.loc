<?php
class DiaryModel extends AbstractIndexModel
{
    /**
     * @param $loggedUserId
     * @return array
     */
    public function getLoggedUserTasks($loggedUserId)
    {
        /*        $sql = "SELECT `task_id`, `task_desc`, `exec_status` FROM `tasks`
                WHERE `user_id` = $userId ORDER BY `task_id` DESC";*/
//        return $this->db->selectLoggedUserTasks($userId);
        return $this->db->selectDataFetchAll('tasks',
                                                    ['task_id',
                                                        'task_desc',
                                                        'exec_status'],
                                                'user_id',
                                                    $loggedUserId,
                                                    '=',
                                                    ['ORDER BY `task_id`' => 'DESC']
        );
    }

    /**
     * @param array $data
     * @param null $typeUpdate on this param depends which method to use
     * @return array
     */
    public function updateTaskStatus(array $data, $typeUpdate = null)
    {
        if ($data['task_id'] == null) {
            $data['errors'] = $this->errorModel->getForgotMsg('forgotMarkDone');
            unset($data['task_id'], $data['id']);
            return $data;
        }

        $data['task_id'] = $this->validationModel->validateData($data['task_id'], 'id');
        $data['id']      = $this->validationModel->validateData($data['id'], 'id');

        if (!in_array(false, $data)) {
            //checking if user exists by id
            /*$userIdResult = $this->db->selectData($data['id'], 'id', array('id'));*/
            $userIdResult = $this->db->selectDataFetch('users',array('id'), 'id', $data['id']);
            if(!$userIdResult) {
                $data['id'] = false;
                $data['errors'] = $this->errorModel->isSameData('wrongUserId');
                return $data;
            }

            //checking if user exists by id
            /*$taskIdResult = $this->db->selectOneTaskData($data['task_id']);*/
            $taskIdResult = $this->db->selectDataFetch('tasks',
                                                                array('task_id'),
                                                                'task_id',
                                                                $data['task_id']);
            if(!$taskIdResult) {
                $data['task_id'] = false;
                $data['errors'] = $this->errorModel->isSameData('wrongUserId');
                return $data;
            }
            if($typeUpdate == 'doneUpdate') {
                $result['result'] = $this->db->updateData('tasks',
                                                                'exec_status',
                                                                'task_id',
                                                                $data['task_id'])/*updateTaskStatus($data['task_id'])*/;
            } elseif ($typeUpdate == 'signUpdate') {
                $result['result'] = $this->db->updateData('tasks',
                                                                'user_id',
                                                                'task_id',
                                                                $data['task_id'],
                                                                $data['id'])/*updateTaskResponseStatus($data['task_id'], $data['id'])*/;
            } else {
                $result['result'] = false;
            }

            if ($result['result'] == false) {
                $data['errors'] = $this->errorModel->getErrors($result);
            }
            return $result;
        } else {
        $data['errors'] = $this->errorModel->getErrors($data);
        return $data;
        }
    }

    /**
     * receiving file ,parsing it and if OK insert into DB returning success msg
     * if not OK returns error msg
     * @return array|int|mixed
     */
    public function uploadTasks()
    {
        $file = $_FILES['taskFile'];
        if ($file['error'] == 4) {
            $error['errors'] = $this->errorModel->getForgotMsg('noFile');
            return $error;
        } elseif ($file['error'] == 2) {
            $error['bigFile'] = false;
            $error['errors'] = $this->errorModel->getErrors($error);
            return $error;
        } elseif ($file['type'] != 'text/plain') {
            $error['file'] = false;
            $error['errors'] = $this->errorModel->getErrors($error);
            return $error;
        } elseif ($file['size'] > 20000) {
            $error['bigFile'] = false;
            $error['errors'] = $this->errorModel->getErrors($error);
            return $error;
        } else {
            $fileName = (string)$file['name'];
            $tmpName = (string)$file['tmp_name'];
            $filePath = "uploaded_files/" . $fileName;

            if (!move_uploaded_file($tmpName, $filePath)) {
                $error['downloadProblem'] = false;
                $error['errors'] = $this->errorModel->getErrors($error);
                return $error;
            } else {
                $fileData = file_get_contents($filePath);
                $tasks = $this->validationModel->validateData($fileData,'tasks');
                if ($tasks == false) {
                    $error['wrongFilled'] = false;
                    $error['errors'] = $this->errorModel->getErrors($error);
                    return $error;
                } else {
                    return $this->db->insertTasks($tasks);
                    /*if ($result != false) {
                        $success['success'] = 'File with tasks has been added!';
                        return $success;
                    }*/
                }
            }
        }
    }

    /**
     * getting all tasks from DB table 'tasks'
     * @return array
     */
    public function getAllTasks()
    {
        return $this->db->selectAllTasks();
    }
}