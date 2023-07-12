<?php
class MySqlDbWorkModel
{
    /**
     * connection to DB
     * @var
     */
    private $pdo;
/*    private $currentSql;
    private $currentStmt;*/

    /**
     * MySqlDbWorkModel constructor.
     */
    public function __construct()
    {
        $params = require "config/db_params.php";
        $opt = [ PDO::ATTR_ERRMODE              => PDO::ERRMODE_WARNING,
                 PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC ];
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']};charset={$params['charset']}";
        try {
            $this->pdo = new PDO($dsn, $params['user'], $params['password'], $opt);
            /*return $this->pdo;*/
        }
        catch (PDOException $e){
            echo "Mistake. Our apologises. <br>
                            <a href='/'>Main</a>";
            file_put_contents("errors/error_db.log",
                "Ошибка при подключении к базе данных.\n" .
                "Ошибка: "      . $e->getMessage() .
                "\nФайл: "      . $e->getFile() .
                "\nСтрока: "    . $e->getLine() .
                "\n\n", FILE_APPEND | FILE_USE_INCLUDE_PATH);
        }
    }


    /**
     * @param $data
     * @param $condition
     * @param array $field
     * @param $connector
     * @return mixed
     */
/*    public function selectData($data, $condition, array $field, $connector = '=')
    {
        $sql = "SELECT ";
        $i = 1;
        $count = count($field);
        foreach ($field as $value) {
            //do it without last array value. cause we have ',' in sql request
            if ($i == $count)
                break;
            $sql .= "`{$value}`, ";
            $i++;
        }
        //get last value from array and cont our sql request
        $sql .= '`' . array_pop($field) . '`' . " FROM `users`";
        if (is_array($condition)) {
            $sql .= " WHERE ";
            $i = 1;
            foreach ($condition as $key => $value) {
                if ($i == count($condition)){
                    $sql .= "`{$key}` {$value} ?";
                    break;
                }
                $sql .= "`{$key}` {$value} ? {$connector} ";
                $i++;
            }
        } else {
            $sql .= " WHERE `{$condition}` {$connector}?";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            //without bind
            !is_array($data) ? $stmt->execute(array($data)) : $stmt->execute($data);
            return !is_array($data) ? $stmt->fetch() : $stmt->fetchAll();
        } catch (PDOException $e){
            return 'Error!' . $e->getMessage();
        }
    }*/

    /**

     * @param array $data
     * @return int|mixed
     */
    public function insertData(array $data)
    {
        $insert = "INSERT INTO `users` (";
        $values = "VALUES (";
        $i = 1;
        $count = count($data);
        //returns values without named indexes
        foreach ($data as $field => $value) {
            if ($i == $count) {
                $insert .= "`{$field}`) ";
                $values .= ":{$field})";
                break;
            }
            $insert .= "`{$field}`, ";
            $values .= ":{$field}, ";
            $i++;
        }
        $sql = $insert . $values;
        //trying to insert
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            return $e->getCode();
        }
    }

    /**
     * returns last inserted id
     * @return string
     */
    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }





/*    public function selectFamilyRolesData()
    {
        $sql = 'SELECT `role_id`, `role_name` FROM `family_roles`';
        $sql = $this->select('family_roles', array('role_id', 'role_name'));
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
        return $this->selectFetchAll('family_roles', array('role_id', 'role_name'));
    }*/

/*    public function selectRoleData($roleId, $roleName)
    {
        $sql = "SELECT `role_id` FROM `family_roles`
                WHERE `role_id` = $roleId AND `role_name` = '$roleName'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();
    }*/

/*    public function selectOneTaskData($taskId)
    {
        $sql = "SELECT `task_id` FROM `tasks` WHERE `task_id` = $taskId";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();
    }*/

/*    public function updateTaskStatus($taskId)
    {
         return $this->updateData('tasks', 'exec_status', 'task_id', $taskId);
    }*/

/*    public function updateTaskResponseStatus($taskId, $id)
    {
        return $this->updateData('tasks', 'user_id', 'task_id', $taskId, $id);
    }*/


    /**
     * Change needed param in DB
     * @param $table
     * @param $field
     * @param $fieldCondition
     * @param $id
     * @param int $data
     * @return mixed
     */
    public function updateData($table, $field, $fieldCondition,  $id, $data = 1)
    {
        $sql = "UPDATE `{$table}` SET `{$field}`='" . $data . "' WHERE `{$fieldCondition}`=?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(array($id));
        } catch (PDOException $e) {
            return $e->getCode();
        }
    }


    /**
     * insert tasks into DB, table `tasks`
     * @param $tasks
     * @return int|mixed
     */
    public function insertTasks($tasks)
    {

        foreach ($tasks as $value) {
            $sql ="INSERT INTO `tasks` (`task_desc`) VALUES ('$value')";
            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                return $e->getCode();
            }
        }
    }






/*    public function selectLoggedUserTasks($userId)
    {
        $sql = "SELECT `task_id`, `task_desc`, `exec_status` FROM `tasks`
                WHERE `user_id` = $userId ORDER BY `task_id` DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }*/

    /**
     * @param $fromTable
     * @param $joinTable
     * @param $joinCondition
     * @param $joinType
     * @param array|null $tableFields
     * @param null $whereCondition
     * @param null $whereConditionData
     * @param string $connector
     * @param null $orderBy
     * @return mixed
     */
    public function selectJoinDataFetch($fromTable, $joinTable, $joinCondition , $joinType, array $tableFields = null,
                                        $whereCondition = null, $whereConditionData = null,
                                        $connector = '=', $orderBy = null)
    {
        $stmt = $this->selectJoinData($fromTable, $joinTable, $joinCondition , $joinType, $tableFields, $whereCondition,
                                        $whereConditionData, $connector, $orderBy);
        return $stmt->fetch();
    }

    /**
     * @param $fromTable
     * @param $joinTable
     * @param $joinCondition
     * @param $joinType
     * @param array|null $tableFields
     * @param null $whereCondition
     * @param null $whereConditionData
     * @param string $connector
     * @param null $orderBy
     * @return array
     */
    public function selectJoinDataFetchAll($fromTable, $joinTable, $joinCondition , $joinType, array $tableFields = null,
                                           $whereCondition = null, $whereConditionData = null,
                                           $connector = '=', $orderBy = null)
    {
        $stmt = $this->selectJoinData($fromTable, $joinTable, $joinCondition , $joinType, $tableFields, $whereCondition,
                                        $whereConditionData, $connector, $orderBy);
        return $stmt->fetchAll();
    }

    /**
     * @return array
     */
    public function selectAllUsersData()
    {
        /*$sql = 'SELECT `users`.`id` as `id`, `users`.`name` as `name`,
                `family_roles`.`role_name` as `user_family_role`
                FROM `users` JOIN `family_roles`
                ON `family_roles`.`role_id` = `users`.`family_role`';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();*/
        $tableFields = [
            '`users`.`id`' => 'id',
            '`users`.`name`' => 'name',
            '`family_roles`.`role_name`' => 'user_family_role'
        ];
        $joinCondition = ['`family_roles`.`role_id`' => '`users`.`family_role`'];
        return $this->selectJoinDataFetchAll('users', 'family_roles', $joinCondition, 'JOIN',
        $tableFields);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function selectUserData($userId)
    {
        /*$sql = "SELECT `users`.`id` as `id`, `users`.`name` as `name`,
                `family_roles`.`role_name` as `family_role`,
                `family_roles`.`distribute_tasks` as `distribute_tasks`,
                `family_roles`.`upload_tasks` as `upload_tasks`
                FROM `users` JOIN `family_roles`
                ON `family_roles`.`role_id` = `users`.`family_role` WHERE `users`.`id` = $userId";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();*/
        $tableFields = [
            '`users`.`id`' => 'id',
            '`users`.`name`' => 'name',
            '`family_roles`.`role_name`' => 'family_role',
            '`family_roles`.`distribute_tasks`' => 'distribute_tasks',
            '`family_roles`.`upload_tasks`' => 'upload_tasks'
        ];
        $joinCondition = ['`family_roles`.`role_id`' => '`users`.`family_role`'];
        return $this->selectJoinDataFetch('users', 'family_roles', $joinCondition, 'JOIN',
        $tableFields, '`users`.`id`', array($userId));
    }

    /**
     * @return array
     */
    public function selectAllTasks()
    {
        /*$sql = 'SELECT `tasks`.`task_id` as `task_id`, `tasks`.`task_desc` as `task_desc`, `tasks`.`user_id` as `user_id`,
       `tasks`.`exec_status`,`users`.`name` as `responsible_name`
                FROM `tasks` LEFT JOIN `users`
                ON `users`.`id` = `tasks`.`user_id`
                WHERE `tasks`.`exec_status` IS null ORDER BY `tasks`.`task_id` DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();*/
        $tableFields = [
            '`tasks`.`task_id`' => 'task_id',
            '`tasks`.`task_desc`' => 'task_desc',
            '`tasks`.`user_id`' => 'user_id',
            '`tasks`.`exec_status`' => 'exec_status',
            '`users`.`name`' => 'responsible_name'
        ];
        $joinCondition = ['`users`.`id`' => '`tasks`.`user_id`'];
        $orderBy = ['ORDER BY `tasks`.`task_id`' => 'DESC'];

        return $this->selectJoinDataFetchAll('tasks', 'users', $joinCondition,'LEFT JOIN',
        $tableFields, '`tasks`.`exec_status`', array(null), 'IS', $orderBy);
    }

    /**
     * @param $fromTable
     * @param $joinTable
     * @param $joinCondition
     * @param $joinType
     * @param $tableFields
     * @param $whereCondition
     * @param $whereConditionData
     * @param $connector
     * @param $orderBy
     * @return false|PDOStatement|string
     */
    private function selectJoinData($fromTable, $joinTable, $joinCondition, $joinType, $tableFields, $whereCondition,
                                    $whereConditionData, $connector, $orderBy)
    {
        $sql = 'SELECT ';
        $i = 1;
        $count = count($tableFields);
        /*foreach ($tableFields as $tableField => $alias) {
            //do it without last array value. cause we have ',' in sql request
            if ($i == $count)
                break;
            $sql .= "{$tableField} AS `{$alias}`, ";
            $i++;
        }
        //get last value from array and cont our sql request
        $sql .= '`' . array_pop($fields) . '`' . " FROM `{$fromTable}`";*/
        foreach ($tableFields as $tableField => $alias) {
            if ($i == $count) {
                $sql .= "{$tableField} AS `{$alias}` ";;
            } else {
                $sql .= "{$tableField} AS `{$alias}`, ";
            }
            $i++;
        }

        $sql .= "FROM `{$fromTable}` {$joinType} `{$joinTable}` ON ";

        foreach ($joinCondition as $key => $value) {
            $sql .= "{$key} = {$value}";
        }


        if ($whereCondition != null && $whereConditionData != null) {
            return $this->where($sql, $whereCondition, $whereConditionData, $connector, $orderBy);
        } else {
            $stmt = $this->pdo->query($sql);
            return $stmt;
        }
    }

    /**
     * @param $tableName
     * @param array|null $fields
     * @param null $condition
     * @param null $conditionData
     * @param string $connector
     * @param null $orderBy
     * @return mixed
     */
    public function selectDataFetch($tableName, array $fields = null, $condition = null,
                                                $conditionData = null, $connector = '=', $orderBy = null)
    {
        $stmt = $this->selectData($tableName, $fields, $condition, $conditionData, $connector, $orderBy);
        return $stmt->fetch();
    }

    /**
     * @param $tableName
     * @param array|null $fields
     * @param null $condition
     * @param null $conditionData
     * @param string $connector
     * @param null $orderBy
     * @return array
     */
    public function selectDataFetchAll($tableName, array $fields = null, $condition = null,
                                                $conditionData = null, $connector = '=', $orderBy = null)
    {
        $stmt = $this->selectData($tableName, $fields, $condition, $conditionData, $connector, $orderBy);
        return $stmt->fetchAll();
    }

    /*private function select($tableName, array $fields = null)
    {
        $sql = 'SELECT ';
        if ($fields != null) {
            $i = 1;
            $count = count($fields);
            foreach ($fields as $field) {
                //do it without last array value. cause we have ',' in sql request
                if ($i == $count)
                    break;
                $sql .= "`{$field}`, ";
                $i++;
            }
            //get last value from array and cont our sql request
            $sql .= '`' . array_pop($fields) . '`' . " FROM `{$tableName}`";
        } else {
            $sql .= "* FROM `{$tableName}`";
        }
        return $sql;
    }*/

    /**
     * @param $tableName
     * @param $fields
     * @param $condition
     * @param $conditionData
     * @param $connector
     * @param $orderBy
     * @return false|PDOStatement|string
     */
    private function selectData($tableName, $fields, $condition, $conditionData, $connector, $orderBy)
    {
        $sql = 'SELECT ';
        if ($fields != null) {
            $i = 1;
            $count = count($fields);
            foreach ($fields as $field) {
                //do it without last array value. cause we have ',' in sql request
                if ($i == $count)
                    break;
                $sql .= "`{$field}`, ";
                $i++;
            }
            //get last value from array and cont our sql request
            $sql .= '`' . array_pop($fields) . '`' . " FROM `{$tableName}`";
        } else {
            $sql .= "* FROM `{$tableName}`";
        }
        if ($condition != null && $conditionData != null) {
            return $this->where($sql, $condition, $conditionData, $connector, $orderBy);
        } else {
            $stmt = $this->pdo->query($sql);
            return $stmt;
        }
    }

    /**
     * @param $sql
     * @param $condition
     * @param $conditionData
     * @param $connector
     * @param $orderBy
     * @return false|PDOStatement|string
     */
    private function where($sql, $condition, $conditionData, $connector, $orderBy)
    {
        if (is_array($condition)) {
            $sql .= " WHERE ";
            $i = 1;
            foreach ($condition as $conditionColumn => $conditionConnector) {
                if ($i == count($condition)){
                    $sql .= "`{$conditionColumn}` {$conditionConnector} ?";
                    break;
                }
                $sql .= "`{$conditionColumn}` {$conditionConnector} ? {$connector} ";
                $i++;
            }
        } else {
            $sql .= " WHERE {$condition} {$connector} ?";
        }

        if($orderBy != null) {
            foreach ($orderBy as $orderCondition => $orderType) {
                $sql .= " {$orderCondition} $orderType";
            }
        }

        return $this->prepare($sql, $conditionData);
    }

    /**
     * @param $sql
     * @param $conditionData
     * @return false|PDOStatement|string
     */
    private function prepare($sql, $conditionData)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            //without bind
            !is_array($conditionData) ? $stmt->execute(array($conditionData)) : $stmt->execute($conditionData);
            return $stmt;
        } catch (PDOException $e){
            return 'Error!' . $e->getMessage();
        }
    }
}