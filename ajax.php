<?php
include_once("database.php");
$DB = Database::getInstance();
$DB->getConnection();

$response = '';
// Обработка добавления элемента или раздела
if ($_REQUEST['action'] == 'add') {
    $date_create = date('Y-m-d');
    $date_modify = '';
    if ($_REQUEST['type'] == 'section') {
        $name = $_REQUEST['name'];
        $parent_id = $_REQUEST['parent_id'];
        $description = $_REQUEST['description'];

        if (!intval($parent_id))
            $parent_id = ''; 
        
        if ($name) {
            $rows = '`Name`, `Date_create`, `Date_modify`, `Description`, `Parent_ID`';
            $values = [$name, $date_create, $date_modify, $description, $parent_id];
            $req = $DB->insert('cat_section',$values, $rows);
            if ($req) 
                $response = 'Раздел успешно добавлен';
            
        } else {
            $response = 'Не заполнено имя';
        }
    } elseif ($_REQUEST['type'] == 'element') {
        $name = $_REQUEST['name'];
        $section_id = $_REQUEST['section_id'];
        $item_type = $_REQUEST['item_type'];
        $description = $_REQUEST['description'];

        if ($section_id){
            if ($name) {
                $rows = '`Section_ID`, `Name`, `Date_create`, `Date_modify`, `Type`, `Description`';
                $values = [$section_id, $name, $date_create, $date_modify, $item_type, $description];
                $req = $DB->insert('cat_element',$values, $rows);
                if ($req) 
                    $response = 'Элемент успешно добавлен';
                
            } else {
                $response = 'Не заполнено имя';
            }
        } else {
            $response = 'Необходимо указать раздел';
        }
    }
// Обработка редактирования элемента или раздела
} elseif($_REQUEST['action'] == 'edit') {
    $date_modify = date('Y-m-d');
    $updateRows = [];
    
    if ($_REQUEST['type'] == 'section') {
        if (isset($_REQUEST['name']) && $_REQUEST['name'] != false)
            $updateRows['Name'] = $_REQUEST['name'];
        if (isset($_REQUEST['description']) && $_REQUEST['description'] != false)
            $updateRows['Description'] = $_REQUEST['description'];
        if (isset($_REQUEST['parent_id']) && (intval($_REQUEST['parent_id']) != false)) {
            $updateRows['Parent_ID'] = $_REQUEST['parent_id'];

        $updateRows['Date_modify'] = $date_modify;
            // отдельная обработка, если хотим убрать родителя у раздела
            if($updateRows === 0 )
            $updateRows['Parent_ID'] = 'NULL';
        }
        
        if(count($updateRows)) {
            if(isset($_REQUEST['section_id']) && $_REQUEST['section_id'] === '') {
                $response = 'Необходимо указать раздел';
            } else {
                $updateWhere = ['ID' => $_REQUEST['section_id']];
                $req = $DB->update('cat_section', $updateRows, $updateWhere);
                if ($req) 
                    $response = 'Раздел успешно изменен';
            }
        }
        
    } elseif ($_REQUEST['type'] == 'element') {
        if (isset($_REQUEST['name']) && $_REQUEST['name'] != false)
            $updateRows['Name'] = $_REQUEST['name'];
        if (isset($_REQUEST['section_id']) && $_REQUEST['section_id'] != false)
            $updateRows['Section_ID'] = $_REQUEST['section_id'];
        if (isset($_REQUEST['item_type']) && $_REQUEST['item_type'] != false)
            $updateRows['Type'] = $_REQUEST['item_type'];
        if (isset($_REQUEST['description']) && $_REQUEST['description'] != false)
            $updateRows['Description'] = $_REQUEST['description'];

        $updateRows['Date_modify'] = $date_modify;

        if(count($updateRows)) {
            if(isset($_REQUEST['element_id']) && $_REQUEST['element_id'] === '') {
                $response = 'Необходимо выбрать элемент';
            } else {
                $updateWhere = ['ID' => $_REQUEST['element_id']];
                $req = $DB->update('cat_element', $updateRows, $updateWhere);
                if ($req) 
                    $response = 'Элемент успешно изменен';
            }
        }
    }
// Обработка удаления раздела или элемента
} elseif($_REQUEST['action'] == 'delete') {
    $response = 'Ошибка удаления элемента';
    
    if (isset($_REQUEST['id']) && $_REQUEST['id'] != false)
        $where = 'WHERE `ID`= ' .  $_REQUEST['id'];
    if (isset($_REQUEST['table']) && $_REQUEST['table'] != false)
        $table = $_REQUEST['table'];

    if (isset($table) && isset($where)) {
        $req = $DB->delete($table, $where);
        if ($req) 
            $response = 'Элемент удалён';
    }
}


echo $response;
