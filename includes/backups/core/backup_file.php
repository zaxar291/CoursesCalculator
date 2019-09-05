<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 8/9/2018
 * Time: 5:37 PM
 */

class backup_file
{
    private $connect;
    private $outputDb;
    private $backup_floder = 'includes/backups/core/';
    private $db_name = 'calculator';
    private $tables = array('classes', 'courses', 'metatexts', 'tables_teplate', 'types', 'users', 'years');
    private $rows_count = 0;
    private $detail_report = array();
    private $lastJsonfile;
    private $dbData;

    public function __construct()
    {

    }

    public function CreateDbBackup()
    {
        if(!$this->connect)
        {
            $this->CreateConnect();
            foreach ($this->tables as $key => $value)
            {
                $this->outputDb[$value] = array("table_name" => $value, 'content' => $this->GetTable($value));
            }
        }
        $this->lastJsonfile = $this->backup_floder."storage/backup-database-".$this->db_name."-".mktime().'.json';
        $file = fopen($this->lastJsonfile, "a+");
        file_put_contents($this->lastJsonfile, json_encode($this->outputDb));
        file_put_contents($this->backup_floder."temp/log-database-".$this->db_name.'.json', json_encode($this->detail_report));
        $this->UpdateJsonFile();

    }

    private function CreateConnect()
    {
        $this->dbData = get_database_file()->parametrs;
        if(!$this->connect)
        {
            $this->connect = mysqli_connect($this->dbData->db_host, $this->dbData->db_login, $this->dbData->db_password, $this->dbData->db_name);
            if (!$this->connect) {
                die("Connection failed: " . mysqli_connect_error());
            }
            mysqli_set_charset($this->connect, "utf8");
            $this->connect->query("set names utf8");
            return 1;
        }else{
            return 0;
        }
    }

    private function GetTable($table_name)
    {
        return $this->Get("SELECT * FROM ".$table_name.' WHERE 1', $table_name);
    }

    private function Get($sql, $table, $output = array())
    {
        $this->rows_count = 1;
        $result = $this->connect->query($sql);
        if($result->num_rows > 0)
        {
            while($res = $result->fetch_assoc())
            {
                $output[] = $res;
                $this->detail_report[$table][$res[$this->getElementFromArray('1', $res)]] = array("table" => $table, 'message' => 'Импортирую строку таблицы, номер строки: '.$this->rows_count.'. Импортировано');
                $this->rows_count++;
            }
        }
        return $output;
    }

    public function RollbackLastDbVersion()
    {
        $file_name = $this->GetLatestFileWithDbVersion();
        if(!$file_name)
        {
            return array("message_type" => "server_response", "response_type" => "restore_bd", "is_success" => false, "message" => "Файл ".$file_name." не найден! Попробуйте загрузить файл на сервер вручную!");
        }
        $file = json_decode(file_get_contents($file_name));
        $values = array();
        foreach ($file as $key => $value)
        {
            $values[$value->table_name] = $this->CollectData($value->table_name, $value->content);
        }
        $this->CreateConnect();
        $this->ClearTables();
        $this->CreateTables();
        foreach($values as $key => $value)
        {
             $this->connect->query("INSERT INTO ".$key.$this->TablesValues($key, $value));
        }
        return array("message_type" => "server_response", "is_success" => true);
    }

    private function TablesValues($table, $array, $value_list = '')
    {

       foreach ($array as $key)
       {
           if($value_list == "")
           {
               $value_list = $this->CollectValues($key).'VALUES';
           }
           if(!next($array))
           {
               $value_list .= '('.$this->CollectProps($key).')';
           }else{
               $value_list .= '('.$this->CollectProps($key).'),';
           }

       }
       return $value_list;
    }

    private function CollectProps($array)
    {
        $output = '';
        foreach($array as $key => $value)
        {
            $output .= "'".$value."',";
        }
        return substr($output, 0, -1);
    }

    private function CollectValues($array)
    {
        $output = '(';
        foreach ($array as $key => $value) {
            if(!next($array))
            {
                $output .= $key;
            }else{
                $output .= $key.',';
            }
        }
        return $output.') ';
    }

    private function ClearTables()
    {
        $this->connect->query("DROP TABLE `classes`, `courses`, `metatexts`, `tables_teplate`, `types`, `users`, `years`");
    }

    private function CreateTables()
    {
        $this->connect->query("CREATE TABLE `classes` (`id` int(11) NOT NULL,`content` varchar(30) NOT NULL,`status` tinyint(1) NOT NULL)");
        $this->connect->query("CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `year` int(20) NOT NULL,
  `content` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` varchar(1000) NOT NULL,
  `total` int(255) NOT NULL,
  `minimum` int(25) NOT NULL,
  `maximum` int(25) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `hoocked_table_id` int(11) NOT NULL,
  `metatext_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
)");
        $this->connect->query("CREATE TABLE `metatexts` (
  `id` int(11) NOT NULL,
  `metatext_content` text NOT NULL,
  `metatext_description` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
)");
        $this->connect->query("CREATE TABLE `tables_teplate` (
  `table_id` int(11) NOT NULL,
  `table_content` longtext NOT NULL,
  `table_description` varchar(255) NOT NULL,
  `table_status` tinyint(1) NOT NULL
)");
        $this->connect->query("CREATE TABLE `types` (
  `type_id` int(11) NOT NULL,
  `type_content` varchar(255) NOT NULL,
  `type_description` varchar(255) NOT NULL
)");
        $this->connect->query("CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(15) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `role` varchar(15) NOT NULL,
  `user_checked_token` text NOT NULL,
  `token_set_time` text NOT NULL
)");
        $this->connect->query("CREATE TABLE `years` (
  `id` int(11) NOT NULL,
  `year_content` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
)");
        $this->connect->query('ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`)');
        $this->connect->query('ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`)');
        $this->connect->query('ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`)');
        $this->connect->query('ALTER TABLE `metatexts`
  ADD PRIMARY KEY (`id`)');
        $this->connect->query('ALTER TABLE `tables_teplate`
  ADD PRIMARY KEY (`table_id`)');
        $this->connect->query('ALTER TABLE `types`
  ADD PRIMARY KEY (`type_id`)');
        $this->connect->query('ALTER TABLE `users`
  ADD PRIMARY KEY (`id`)');
        $this->connect->query('ALTER TABLE `years`
  ADD PRIMARY KEY (`id`)');
        $this->connect->query('ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12');
        $this->connect->query('ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48');
        $this->connect->query('ALTER TABLE `metatexts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3');
        $this->connect->query('ALTER TABLE `tables_teplate`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3');
        $this->connect->query('ALTER TABLE `types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3');
        $this->connect->query('ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2');
        $this->connect->query('ALTER TABLE `years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3');
        return 1;
    }

    public function GetReport()
    {
        return $this->detail_report;
    }

    private function getElementFromArray($element_count, $array)
    {
        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $this->getElementFromArray($element_count, $value);
            }else{
                return $key;
            }
        }
    }

    private function CollectData($table_name, $array)
    {
        switch($table_name)
        {
            case 'courses': return $this->ConcatCoursesTable($table_name, $array);
            case 'classes': return $this->ConcatClassesTable($table_name, $array);
            case 'metatexts': return $this->ConcatMetatextsTable($table_name, $array);
            case 'tables_teplate': return $this->ConcatTablesTeplate($table_name, $array);
            case 'types': return $this->ConcatTypesTable($table_name, $array);
            case 'users': return $this->ConcatUsersTable($table_name, $array);
            case 'years': return $this->ConcatYearsTable($table_name, $array);
        }
    }

    private function ConcatCoursesTable($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('id' => $value->id, 'year' => $value->year, 'content' => $value->content, 'type' => $value->type, 'price' => $value->price, 'total' => $value->total, 'minimum' => $value->minimum, 'maximum' => $value->maximum, 'discount' => $value->discount, 'status' => $value->status, 'hoocked_table_id' => $value->hoocked_table_id, 'metatext_id' => $value->metatext_id, 'group_id' => $value->group_id));
        }
        return $output;
    }

    private function ConcatClassesTable($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('id' => $value->id, 'content' => $value->content, 'status' => $value->status));
        }
        return $output;
    }

    private function ConcatMetatextsTable($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('id' => $value->id, 'metatext_content' => $value->metatext_content, 'metatext_description' => $value->metatext_description, 'status' => $value->status));
        }
        return $output;
    }

    private function ConcatTablesTeplate($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('table_id' => $value->table_id, 'table_content' => $value->table_content, 'table_description' => $value->table_description, 'table_status' => $value->table_status));
        }
        return $output;
    }

    private function ConcatTypesTable($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('type_id' => $value->type_id, 'type_content' => $value->type_content, 'type_description' => $value->type_description));
        }
        return $output;
    }

    private  function ConcatUsersTable($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('id' => $value->id, 'login' => $value->login, 'pass' => $value->pass, 'role' => $value->role, 'user_checked_token' => $value->user_checked_token, 'token_set_time' => $value->token_set_time));
        }
        return $output;
    }

    private  function ConcatYearsTable($table_name, $array)
    {
        $output = array();
        foreach($array as $key => $value)
        {
            array_push($output, array('id' => $value->id, 'year_content' => $value->year_content, 'status' => $value->status));
        }
        return $output;
    }

    private function GetLatestFileWithDbVersion()
    {
        $jsonFile = json_decode(file_get_contents('includes/backups/core/configs/admin/database/package.json'));
        if(is_file($jsonFile->last_change_file))
        {
            return $jsonFile->last_change_file;
        }else{
            return 0;
        }
    }

    private function UpdateJsonFile()
    {
        $jsonFile = json_decode(file_get_contents('includes/backups/core/configs/admin/database/package.json'));
        file_put_contents("includes/backups/core/configs/admin/database/package.json", json_encode(array(
            "name" => "DataBaseAuthInfo",
            "version" => $jsonFile->version++,
            "parametrs" => array(
                "db_host" => "127.0.0.1",
                "db_login" => "root",
                "db_password" => "",
                "db_name" => "calculator"
            ),
            "provide" => "includes/backups/core/backup_file.php",
            "last_modify" => date("l d F Y h:i:s A"),
            "user_modify" => (isset($_SESSION["login"])) ? $_SESSION["login"] : "Неизвестный пользователь! Внимание!",
            "system" => array(
                "is_success" => "true",
                "tables" => $this->GetTablesList()
            ),
            "last_change_file" => $this->lastJsonfile
        )));
    }

    private function GetTablesList()
    {
        $output = '';
        foreach($this->tables as $key => $value)
        {
            $output .= $value.' ';
        }
    }

    public function GetSettingsFile()
    {
        return json_decode(file_get_contents('includes/backups/core/configs/admin/database/package.json'));
    }
}