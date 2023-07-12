<?php

class ValidationModel
{
    /**
     * Check have we an errors from form
     * @param $data
     * @param string $type
     * @return bool|string
     */
    public function validateData($data, $type = ""){
        switch ($type){
            case "name"://user name. no russian 4-30 characters
                preg_match('/[A-Za-z0-9\)\(_\-]{2,30}/u', $data, $arr);
                return strlen($data) == strlen($arr[0]) ? $arr[0]
                    : false;
                break;
            case "password": //user=password from 6 to 16 symbols
                preg_match('/[\w]{6,16}/u', $data, $arr);
                return strlen($data) == strlen($arr[0]) ? md5($arr[0])
                    : false;
                break;
            case "id": // cheking different data by id (`users`.`id`, `tasks`.`task_id`, `family_roles`.`role_id`
                preg_match('/^[0-9]+$/u', $data, $arr);
                return (int)$data == (int)$arr[0] ? (int)$arr[0]
                    : false;
                break;
            //checking for correct incoming family_roles data from the phtml form in 'register.phtml'
            case "role_name": if ($data === 'Mother') {
                                    return $data;
                              } elseif ($data === 'Father') {
                                    return $data;
                              } elseif ($data === 'Child') {
                                    return $data;
                              } else {
                                    return false;
                              }
              break;
            case "tasks": // checking file for being correctly filled like in our example
                preg_match_all('/([A-Z0-9]+[\.\)]([A-Z0-9]*[\.\)])*)(.+)/Su', $data, $arr);
                $tasks = array_pop($arr);
                if (empty($tasks)) {
                    return false;
                } else {
                    return $tasks;
                }
                break;
            default:
                return false;
                break;
        }
    }
}