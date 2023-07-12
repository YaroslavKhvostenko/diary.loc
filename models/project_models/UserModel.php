<?php
class UserModel extends AbstractIndexModel
{
    /**
     * @param $id
     * @return mixed
     */
    public function getUserInfo($loggedUserId)
    {
/*        $this->_db = $this->DbConnection();
        $result = $this->selectData($this->_db,
            $id,
            'id',
            array("id",
                "login",
                "email",
                "group",
                'is_head',
                'is_admin',
                'is_accessible'
            )
        );*/
        return $this->db->selectUserData($loggedUserId);
        /*return $result;*/
    }

    public function getAllUsersData()
    {
        return $this->db->selectAllUsersData();
    }
}