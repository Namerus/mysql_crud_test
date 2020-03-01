<?php 
include_once("header.php");
include_once("database.php");

$DB = Database::getInstance();
$DB->getConnection();

$cursection = $sectionError = $elementId = ''; 
if (isset($_REQUEST["section"])) {
    $cursection = $_REQUEST["section"];
} elseif(isset($_REQUEST["edit_section"])) {
    $cursection = $_REQUEST["edit_section"];
    if($cursection == false)
        $sectionError = 'Необходимо выбрать раздел в левом меню';
}
if (isset($_REQUEST["element_id"]))
    $elementId = $_REQUEST["element_id"];

?>

<div class="catalog-wrapper">
    <div class="catalog-left-menu">
        <?=$DB->getCatalogSectionResult();?>
        <br/><br/><br/>
        <?if($cursection):?>
            <a href="?edit_section=<?=$cursection;?>" class="catalog-button">Редактировать Текущий Раздел</a>
        <?endif;?>
        <a href="?add_section" class="catalog-button">Добавить Раздел</a>
        <a href="?add_element" class="catalog-button">Добавить Элемент</a>
    </div>

    <div class="catalog-main-content">
        <?if(isset($_REQUEST['section'])):
            //Вывод разделов каталога
            echo $DB->getCatalogElements($_REQUEST['section']);
        elseif(isset($_REQUEST['add_element'])):?>
            <h2>Добавить Элемент</h2>
            <br/>
            <form class="add_form">
                <input type="text" name="name" value="" placeholder="Название">
                <input type="text" name="section_id" value="" placeholder="Раздел">
                <input type="text" name="item_type" value="" placeholder="Тип">
                <input type="text" name="description" value="" placeholder="Описание">
                <input type="hidden" name="type" value="element">
                <input type="hidden" name="action" value="add">
                <input type="submit" name="submit" value="Добавить">
            </form> 
        <?elseif(isset($_REQUEST['add_section'])):?>
            <h2>Добавить Раздел</h2>
            <br/>
            <form class="add_form">
                <input type="text" name="name" value="" placeholder="Название раздела">
                <input type="text" name="description" value="" placeholder="Описание">
                <input type="text" name="parent_id" value="" placeholder="Родительский раздел">
                <input type="hidden" name="type" value="section">
                <input type="hidden" name="action" value="add">
                <input type="submit" name="submit" value="Добавить">
            </form>
        <?elseif(isset($_REQUEST['edit_section'])): ?>
            <?if($sectionError):
                echo $sectionError;
            else:?>
                <h2>Редактировать Раздел ID = <?=$cursection?></h2>
                <p>Оставьте пустыми поля, которые не нужно изменять</p>
                <br/>
                <form class="add_form">
                    <input type="text" name="name" value="" placeholder="Новое Название">
                    <input type="text" name="description" value="" placeholder="Новое Описание">
                    <input type="text" name="parent_id" value="" placeholder="Новый Родительский раздел">
                    <input type="hidden" name="type" value="section">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="section_id" value="<?=$cursection?>">
                    <input type="submit" name="submit" value="Отправить">
                </form>
            <?endif;?>
        <?elseif(isset($_REQUEST['element_id'])): ?>
            <h2>Редактировать Элемент ID = <?=$elementId?></h2>
            <p>Оставьте пустыми поля, которые не нужно изменять</p>
            <br/>
            <form class="add_form">
                <input type="text" name="name" value="" placeholder="Новое Название">
                <input type="text" name="section_id" value="" placeholder="Новый Раздел">
                <input type="text" name="item_type" value="" placeholder="Новый Тип">
                <input type="text" name="description" value="" placeholder="Новое Описание">
                <input type="hidden" name="type" value="element">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="element_id" value="<?=$elementId?>">
                <input type="submit" name="submit" value="Отправить">
            </form> 
        <?endif?>
        
        <p id="result_form"></p>
    </div>

    
</div>

<br/>
<p>Информация!</p>
<p>Чтобы посмотреть элементы в разделе, нужно кликнуть по разделу.</p>
<p>Со страницы раздела можно перейти во вкладку редактирования раздела.</p>


<?php include_once("footer.php");?>
