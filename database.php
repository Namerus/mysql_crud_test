<?php
class Database {

    // specify your own database credentials
    private static $_instance = null;
    private static $host = "localhost";
    private static $db_name = "test_db";
    private static $username = "phpmyadmin";
    private static $password = "some_pass";
    private static $sectionTree = [];
    private static $sectionRes = '';
    private static $conn = false;


    protected function __construct() { }
    private function __clone () {}
	private function __wakeup () {}

    public static function getInstance()
    {
        if (self::$_instance != null) {
            return self::$_instance;
        }

        return new self;
    }

  
    // get the database connection
    public function getConnection()
    {
        if(!self::$conn) {
            self::$conn = mysqli_connect(self::$host,self::$username,self::$password);
            mysqli_set_charset(self::$conn, "utf8");

            if(self::$conn){
                $selectdb = mysqli_select_db(self::$conn, self::$db_name);
            }
        }
        return self::$conn;
    }

    public function disconnect()
    {
        if(self::$conn) {
            if(mysqli_close(self::$conn)) {
                    self::$conn = false;
                return true;
            }
        }
        return false;
    }

    public function tableExists($table)
    {
        $table = mysqli_real_escape_string(self::$conn, $table);
        $tablesInDb = mysqli_query(self::$conn, 'SHOW TABLES FROM '.self::$db_name.' LIKE "'.$table.'"');
        if($tablesInDb)
        {
            if(mysqli_num_rows($tablesInDb) != false) {
                return true;
            }
        }
        return false;
    }


    public function select($table, $rows = '*', $where = null, $order = null)
    {
        $table = mysqli_real_escape_string(self::$conn, $table);
        $rows = mysqli_real_escape_string(self::$conn, $rows);
        $where = mysqli_real_escape_string(self::$conn, $where);
        $order = mysqli_real_escape_string(self::$conn, $order);

        $result = false;
        $q = 'SELECT '.$rows.' FROM '.$table;
        if($where != null)
            $q .= ' WHERE '.$where;
        if($order != null)
            $q .= ' ORDER BY '.$order;


        $query = mysqli_query(self::$conn, $q);
        if($query)
        {
            $numResults = mysqli_num_rows($query);
            while ($res = mysqli_fetch_array($query))
            {
                $result[$res['ID']] = $res;
            }
        }
        return $result;
    }


    /*
    *   $rows = '`Name`, `Date_create`, `Date_modify`, `Description`, `Parent_ID`';
    *   $values = ['Имя', '2020-01-18', '', 'Описание', '1'];
    *
    */
    public function insert($table, $values, $rows = null)
    {
        $table = mysqli_real_escape_string(self::$conn, $table);
        $rows = mysqli_real_escape_string(self::$conn, $rows);

        if($this->tableExists($table))
        {
            $insert = 'INSERT INTO '.$table;
            if($rows != null)
                $insert .= ' ('.$rows.')';
            
            $insertValues = '';
            foreach($values as $val)
            {
                if(!$val) {
                    $val = 'NULL';
                    $insertValues .= $val.', ';
                } else {
                    $val = mysqli_real_escape_string(self::$conn, $val);
                    $insertValues .= '"'.$val.'", ';
                }

            }
            $insertValues = trim($insertValues, ', ');

            $insert .= ' VALUES ('.$insertValues.')';
            $ins = mysqli_query(self::$conn, $insert);
            
            return $ins;
        }
        return false;
    }

    /*
    * $where = 'WHERE ...';
    */
    public function delete($table, $where = null)
    {
        $table = mysqli_real_escape_string(self::$conn, $table);
        $where = mysqli_real_escape_string(self::$conn, $where);
        
        if($this->tableExists($table)) {
            $delete = "DELETE FROM `" . $table . "` " . $where;
            $del = mysqli_query(self::$conn, $delete);
            return $del;
        }
        return false;
    }


    /* 
    * $set = ['ROW_NAME' => 'VALUE', ...];
    * $where = ['ROW_NAME' => 'VALUE', ...];
    */
    public function update($table, $set, $where)
    {
        $table = mysqli_real_escape_string(self::$conn, $table);
        $set = mysqli_real_escape_string(self::$conn, $set);
        $where = mysqli_real_escape_string(self::$conn, $where);

        if($this->tableExists($table) && is_array($set) && is_array($where)) {
            $setReq = ' SET ';
            $whereReq = ' WHERE ';
            foreach ($set as $key => $value) 
            {
                $val = ($value) ? '"' . $value . '"' : 'NULL';
                $rowName = '`' . $key . '`=';
                $setReq .= $rowName . $val . ', ';
            }
            $setReq = trim($setReq, ', ');
            foreach ($where as $key => $value) 
            {
                $val = ($value) ? '"' . $value . '"' : 'NULL';
                $rowName = '`' . $key . '`=';
                $whereReq .= $rowName . $val;
            }

            $table = '`' . $table . '`';
            $request = 'UPDATE '.$table . $setReq . $whereReq;
            
            $res = mysqli_query(self::$conn, $request);
            return $res;            
        }
        return false;
    }


    /*
    *   $result = $sections = [prop = value..., $subsections = [prop = value, ...] ];
    */
    public function getCatalogSections() 
    {
        self::$sectionTree = $this->select('cat_section');
        $sections = [];

        foreach(self::$sectionTree as $item)
        {
            if($item['Parent_ID'] == 0) { // Запускаем рекурсивную функцию только 0 уровня
                $sections[$item['ID']] = $this->getListTree($item['ID']);
            }
        }
        return $sections;
    }


    public function getListTree($id = 0){
        $result = [];
        foreach(self::$sectionTree as $k => $item)
        {
            if($item['ID'] == $id){
                // Записываем значение в возвращаемый массив
                $result = self::$sectionTree[$k];
                // снова проходимся по нашему плоскому массиву в поисках документов у которых текущий числится родителем
                foreach(self::$sectionTree as $item_1)
                {
                    if($item_1['Parent_ID'] == $id){
                        // если нет массива для для подразделов, то создаем 
                        if(empty($result['subsections'])){
                            $result['subsections'] = [];
                        }
                        // если находим подразделы то запускаем снова
                        $result['subsections'][$item_1['ID']] = $this->getListTree($item_1['ID']);
                    }
                }
            }
        }
        return $result;
    }



    
    /* 
    *   $result = [ SECTION_ID => $items[] ];
    */
    public function getCatalogElements($section_ID = false) 
    {
        $where = ($section_ID) ? 'Section_ID = ' . $section_ID : ''; 
        $elements = $this->select('cat_element', '*', $where);
        $result = 'В разделе нет элементов';

       if ($elements) {
            $result = '<h2>Элементы раздела</h2>
                <div class="catalog-elements-list">';
            foreach ($elements as $element) 
            {
                $result .= '<div class="list-item">
                    <p>Название:&nbsp;' . $element['Name'] . '</p>
                    <p>ID:&nbsp;' . $element['ID'] . '</p>
                    <p>ID раздела:&nbsp;' . $element['Section_ID'] . '</p>
                    <p>Дата создания:&nbsp;' . $element['Date_create'] . '</p>
                    <p>Дата изменения:&nbsp;' . $element['Date_modify'] . '</p>
                    <p>Тип:&nbsp;' . $element['Type'] . '</p>
                    <p>Описание:&nbsp;' . $element['Description'] . '</p>
                    <p><a href="?element_id=' . $element['ID'] . '" class="element-button">Редактировать</a></p>
                    <p><a class="delete_item element-button" href="#" data-req="action=delete&table=cat_element&id=' 
                        . $element['ID'] . '">Удалить</a></p>
                </div>';
            }
            $result .= '</div>';
        }

        return $result;
    }




    public function getCatalogSectionResult()
    {
        $sections = $this->getCatalogSections();
        $result = '';
        
        if ($sections) {
            $result .= '<ul class="first-level-ul">';
            $result .= $this->createCatalogStructure($sections);
            $result .= '</ul>';
        }

        self::$sectionRes = false;
        return $result;
    }

    
    public function createCatalogStructure($section) 
    {
        self::$sectionRes .= '<ul>';
        foreach($section as $sec)
        {
            if(is_array($sec) && !isset($sec['ID'])) {
                $this->createCatalogStructure($sec);
            }
            self::$sectionRes .= '<li><a href="?section=' . $sec['ID'] . '">' . $sec['Name'] . '</a>
                <a class="delete_item element-button" href="#" data-req="action=delete&table=cat_section&id=' 
                    . $sec['ID'] . '">&nbsp;(Удалить)</a>';
            if(isset($sec['subsections']) && !empty($sec['subsections'])){
                $this->createCatalogStructure($sec['subsections']);
            }
            self::$sectionRes .= '</li>';
        }
        self::$sectionRes .= '</ul>';
        
        return self::$sectionRes;
    }

}
