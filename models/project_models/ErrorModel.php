<?php
class ErrorModel
{
    /**
     * Check have we got an errors from array data
     * @param array $data
     * @return array|null
     */
    public function getErrors(array $data)
    {
        $errors = [];

        foreach ($data as $key => $value) {
            if ($value != false)
                continue;
            switch ($key) {
                case 'task_id':
                    $errors['task_id'] = 'Wrong type of user identifier!Stop trying to hack our diary!';
                    break;
                case 'id':
                    $errors['id'] = 'Wrong type of user identifier!Stop trying to hack our diary!';
                    break;
                case 'name':
                    $errors['name'] = 'Your name has to be from 2 to 30 symbols! Numbers also allowed';
                    break;
                case 'password':
                    $errors['password'] = 'Your password has to be from 6 to 32 symbols! Numbers also allowed';
                    break;
                case 'role_id':
                    $errors['role_id'] = 'Wrong type of family role identifier!Stop trying to hack our diary!';
                    break;
                case 'role_name':
                    $errors['role_name'] = 'Wrong type of family role name!Stop trying to hack our diary!';
                    break;
                case 'result':
                    $errors['result'] = 'Sorry we have some problems! Our bad!';
                    break;
                case 'file':
                    $errors['file'] = 'Wrong type of file!Only txt files!';
                    break;
                case 'bigFile':
                    $errors['bigFile'] = 'Your file is too big!';
                    break;
                case 'downloadProblem':
                    $errors['downloadProblem'] = 'Sorry we have some problems to download your file!';
                    break;
                case 'wrongFilled':
                    $errors['wrongFilled'] = "File hasn't been uploaded, because you didnt follow our instructions!";
                    break;
                default:
                    break;
            }
        }
        return empty($errors) ? null : $errors;
    }

    /**
     * check do we have an errors on the basis of our type
     * @param string $type
     * @return array|null
     */
    public function isSameData($type = '')
    {
        $errors = [];

        switch ($type) {
            case 'sameRole'://user family role
                $errors['sameRole'] = 'User with such family role already exists!';
                break;
            case 'sameName'://user name
                $errors['sameName'] = 'User with such name already exists!';
                break;
            case 'wrongNameOrPass'://wrong name
                $errors['wrongNameOrPass'] = 'You wrote the wrong name or password!';
                break;
            case 'wrongRole'://wrong family role
                $errors['wrongRole'] = 'We dont have such role!Stop trying to hack our diary!';
                break;
            case 'wrongUserId'://no user exists bu such id
                $errors['wrongId'] = 'We dont have such user!Stop trying to hack our diary!';
                break;
            case 'wrongTaskId'://wrong family role
                $errors['wrongTaskId'] = 'We dont have such task!Stop trying to hack our diary!';
                break;
        }

        return (!empty($errors) && is_array($errors)) ? $errors :null;
    }

    /**
     * check if user forgot to choose data before submit
     * @param string $type
     * @return array|null
     */
    public function getForgotMsg($type = '')
    {
        $errors = [];

        switch ($type) {
            case 'forgotMarkDone'://user forgot to choose task before submit it
                $errors['forgotMarkDone'] = 'Before submit , mark task you want submit!';
                break;
            case 'noFile'://user forgot to choose file before uploading this
                $errors['forgotMarkDone'] = 'Before upload , you have to choose file!';
                break;

        }

        return (!empty($errors) && is_array($errors)) ? $errors :null;
    }

    /**
     * check do we have an errors on the basis of our type
     * @param string $type
     * @return array|null
     */
    /*public function getEmptyMsg($type = '')
    {

        $msg = [];
        switch ($type) {
            case 'noTasks'://user don't have any tasks
                $msg['noTasks'] = "You don't have any tasks yet!";
                break;
        }

        return $msg ;
    }*/
}