<?php
class IndexModel extends AbstractIndexModel
{
    /**
     * Form an array and register user
     * @param array $data
     * @return array
     */
    public function newUser(array $data)
    {
        $data['name']          = $this->validationModel->validateData($data['name'], "name");
        $data['password']       = $this->validationModel->validateData($data['password'], "password");
        $data['role_id']          = $this->validationModel->validateData($data['role_id'], "id");
        $data['role_name']          = $this->validationModel->validateData($data['role_name'], "role_name");
        //check do we have false from validData in our array
        if (!in_array(false, $data)) {
            /*selectFetch($tableName, array $fields = null, $condition = null, $conditionData = null, $connector = '=')*/
            //check by name users with the same name in DB table 'users'
//            $nameResult = $this->db->selectData($data['name'], 'name', array('id'));
            $nameResult = $this->db->selectDataFetch('users', array('id'), 'name', $data['name']);
            if ($nameResult) {
                $data['name'] = false;
                $data['errors'] = $this->errorModel->isSameData('sameName');
                return $data;
            }
            //check by role_id and role_name for existing family role in DB table 'family_roles'
//            $roleResult = $this->db->selectRoleData($data['role_id'], $data['role_name']);
            /*        $sql = "SELECT `role_id` FROM `family_roles`
                WHERE `role_id` = $roleId AND `role_name` = '$roleName'";*/
            $roleResult = $this->db->selectDataFetch(
                'family_roles',
                array('role_id'),
                array('role_id' => '=',
                    'role_name' => '='),
                array($data['role_id'],
                    $data['role_name']),
                'AND'
            );
            if(!$roleResult) {
                $data['role'] = false;
                $data['errors'] = $this->errorModel->isSameData('wrongRole');
                return $data;
            } else {
                $data['family_role'] = $data['role_id'];
            }
            //check users, by family_role 'Mother' or 'Father' in DB table users
            if ($data['role_name'] == 'Mother' || $data['role_name'] == 'Father') {
//                $userRoleResult = $this->db->selectData($data['family_role'], 'family_role', array('id'));
                $userRoleResult = $this->db->selectDataFetch('users', array('id'), 'family_role', $data['family_role']);
                if ($userRoleResult) {
                    $data['family_role'] = false;
                    $data['errors'] = $this->errorModel->isSameData('sameRole');
                    return $data;
                }
            }
            unset($data['role_id'], $data['role_name']);

            if (!in_array(false, $data)) {
                $this->db->insertData($data);
                //if inserted get id
                $data['id']     = $this->db->getLastInsertedId();
                //if everything is ok
                $this->userSession(array('id' => $data['id']));
            }
            return $data;
        } else {
            $data['errors'] = $this->errorModel->getErrors($data);
            return $data;
        }
    }

    /**
     * Check is user already registered.
     * @param array $data
     * @return array|bool
     */
    public function authUser(array $data)
    {
        $data['name']      = $this->validationModel->validateData($data['name'], "name");
        $data['password']   = $this->validationModel->validateData($data['password'], "password");
        if (!in_array(false, $data)) {
            $result = $this->db->selectDataFetch(
                                                'users',
                                                ["id",
                                                    "name",
                                                    "password"],
                                                'name',
                                                $data['name']);
/*            $result = $this->db->selectData(
                $data['name'],
                "name",
                    [ "id",
                     "name",
                     "password"]);*/

            if ($data['name']      == $result['name'] && $data['password']   == $result['password']) {
                unset($result['password'], $data['password']);
                $this->userSession(array('id' => $result['id']));
                return true;
            } else {
                unset($data['password']);
                $data['errors'] = $this->errorModel->isSameData('wrongNameOrPass');
                return $data;
            }
        } else {
            $data['errors'] = $this->errorModel->getErrors($data);
            return $data;
        }
    }

    /**
     * receive family roles and return them
     * @return array
     */
    public function getFamilyRoles()
    {
        return $this->db->selectDataFetchAll('family_roles', array('role_id', 'role_name'));
    }

    /**
     * insert user params into session
     * @param $data
     */
    public function userSession($data){
        $sessionArr = array();
        foreach ($data as $key => $value) {
            $sessionArr[$key] = $value;
        }
        $_SESSION[md5(session_id())] = $sessionArr;
    }
}