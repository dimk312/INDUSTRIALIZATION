<?php
//------------------ ЦЕХ ----------------------------------
function get_workshop() {
    global $mylink;
    $cur_date = date('Y-m-d H:i:s');
    $sql="SELECT dce_workshop.CODE_WORKSHOP, dce_workshop.NAME
FROM dce_workshop
WHERE (((dce_workshop.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_workshop.DATE_B)<='".$cur_date."') AND ((dce_workshop.DATE_E) Is Null)) 
OR (((dce_workshop.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_workshop.DATE_B)<='".$cur_date."') AND ((dce_workshop.DATE_E)>'".$cur_date ."'))
GROUP BY dce_workshop.CODE_WORKSHOP, dce_workshop.NAME
ORDER BY dce_workshop.CODE_WORKSHOP;";
$quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника workshop<br>".mysqli_error($mylink['link']));
while($assoc = mysqli_fetch_assoc($quer)){
    $key = $assoc['CODE_WORKSHOP'];
    $value =$assoc['NAME'];
    $workshop[$key] = $value;}
return($workshop);}

//------------------ ВСЕ РАБОЧИЕ ЗОНЫ ----------------------------------
function get_area() {
    global $mylink;
    $cur_date = date('Y-m-d H:i:s');
    $sql="SELECT dcе_workshop_area.CODE_WORKSHOP, dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
FROM dcе_workshop_area
WHERE (((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E) Is Null)) 
OR (((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E)>'".$cur_date ."'))
GROUP BY dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
ORDER BY dcе_workshop_area.CODE_AREA;";
$quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника workshop_area<br>".mysqli_error($mylink['link']));
while($assoc = mysqli_fetch_assoc($quer)){
    $workshop=$assoc['CODE_WORKSHOP'];
    $key = $assoc['CODE_AREA'];
    $value =$assoc['NAME'];
    $workshop_area[$key] = $value;}
return($workshop_area);}




// ------------------ ВРЕМЕННАЯ ФУКЦИЯ ПОЕА НЕ БУДЕТ ДОРАБОТАНО РЕДАКТИРОВАНИЕ ЗАДАЧ -------------------------------------
//------------------ ВСЕ РАБОЧИЕ ЗОНЫ ----------------------------------
function get_area_et() {
    global $mylink;
    $cur_date = date('Y-m-d H:i:s');
    $sql="SELECT dcе_workshop_area.CODE_WORKSHOP, dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
FROM dcе_workshop_area
WHERE (((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E) Is Null)) 
OR (((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E)>'".$cur_date ."'))
GROUP BY dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
ORDER BY dcе_workshop_area.CODE_AREA;";
$quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника workshop_area<br>".mysqli_error($mylink['link']));
while($assoc = mysqli_fetch_assoc($quer)){
    $workshop=$assoc['CODE_WORKSHOP'];
    $key = $assoc['CODE_AREA'];
    $value =$assoc['NAME'];
    $workshop_area[$key] = '('.$workshop.') - '. $value;}
return($workshop_area);}




//----------------- РАБОЧАЯ ЗОНА -----------------------
function get_workshop_area() {
    global $mylink;
    $cur_date = date('Y-m-d H:i:s');
    if (isset($_POST['WORKSHOP']) AND $_POST['WORKSHOP']!='ALL'){
$sql="SELECT dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
FROM dcе_workshop_area
WHERE (((dcе_workshop_area.CODE_WORKSHOP)='".$_POST['WORKSHOP']."') AND ((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E) Is Null)) 
OR (((dcе_workshop_area.CODE_WORKSHOP)='".$_POST['WORKSHOP']."') AND ((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E)>'".$cur_date ."'))
GROUP BY dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
ORDER BY dcе_workshop_area.CODE_AREA;";
    } else {
    $sql="SELECT dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
FROM dcе_workshop_area
WHERE (((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E) Is Null)) 
OR (((dcе_workshop_area.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E)>'".$cur_date ."'))
GROUP BY dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
ORDER BY dcе_workshop_area.CODE_AREA;";
    }
$quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника workshop_area<br>".mysqli_error($mylink['link']));
while($assoc = mysqli_fetch_assoc($quer)){
    $key = $assoc['CODE_AREA'];
    $value =$assoc['NAME'];
    $workshop_area[$key] = $value;}
return($workshop_area);}


//-----------  СОРТИРОВКА ----------------------
function get_sorting() {
    global $mylink;
    $cur_date = date('Y-m-d H:i:s');
    $sql = "SELECT dce_task_sorting.CODE_SORTING, dce_task_sorting.NAME
FROM dce_task_sorting
WHERE (((dce_task_sorting.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_task_sorting.DATE_B)<='".$cur_date."') AND ((dce_task_sorting.DATE_E) Is Null)) 
OR (((dce_task_sorting.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_task_sorting.DATE_B)<='".$cur_date."') AND ((dce_task_sorting.DATE_E)>'".$cur_date ."'))
GROUP BY dce_task_sorting.CODE_SORTING, dce_task_sorting.NAME
ORDER BY dce_task_sorting.CODE_SORTING;";
        $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника сортиpовки<br>".mysqli_error($mylink['link']));
        while($assoc = mysqli_fetch_assoc($quer)){
            $key = $assoc['CODE_SORTING'];
            $value =$assoc['NAME'];
            $sort[$key] = $value;}
        return($sort);}


//------------- ПРИОРИТЕТ -----------------------
function get_prior() {
    $cur_date = date('Y-m-d H:i:s');
    global $mylink;
    $sql = "SELECT dc_priority.CODE_PRIORITY, dc_priority.NAME
FROM dc_priority
WHERE (((dc_priority.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dc_priority.DATE_B)<='".$cur_date."') AND ((dc_priority.DATE_E) Is Null)) 
OR (((dc_priority.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dc_priority.DATE_B)<='".$cur_date."') AND ((dc_priority.DATE_E)>'".$cur_date ."'))
GROUP BY dc_priority.CODE_PRIORITY, dc_priority.NAME
ORDER BY dc_priority.CODE_PRIORITY;";
    $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника приоритета<br>".mysqli_error($mylink['link']));
    while($assoc = mysqli_fetch_assoc($quer)){
        $key = $assoc['CODE_PRIORITY'];
        $value =$assoc['NAME'];
        $priority[$key] = $value;}
    return($priority);}


//------------- СТАТУС -----------------------
function get_status() {
    global $mylink;
    $cur_date = date('Y-m-d H:i:s');
    $sql = "SELECT dce_task_status.CODE_STATUS, dce_task_status.NAME
FROM dce_task_status
WHERE (((dce_task_status.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_task_status.DATE_B)<='".$cur_date."') AND ((dce_task_status.DATE_E) Is Null)) 
OR (((dce_task_status.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_task_status.DATE_B)<='".$cur_date."') AND ((dce_task_status.DATE_E)>'".$cur_date ."'))
GROUP BY dce_task_status.CODE_STATUS, dce_task_status.NAME
ORDER BY dce_task_status.CODE_STATUS DESC;";
    $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника статуса<br>".mysqli_error($mylink['link']));
    while($assoc = mysqli_fetch_assoc($quer)){
        $key = $assoc['CODE_STATUS'];
        $value =$assoc['NAME'];
        $status[$key] = $value;}
    return($status);}

//----------------- Ответственный для плана-----------------------
function get_responsable() {
    global $mylink;
//  $cur_date = date('Y-m-d H:i:s');
    if (isset($_POST['STATUS']) AND $_POST['STATUS']=='W'){
        $sql="SELECT dce_task_responsable.PERSON_ID, dce_task_responsable.FIO
FROM dce_task_responsable
WHERE (((dce_task_responsable.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_task_responsable.WORK_STATUS)='W'))
GROUP BY dce_task_responsable.PERSON_ID, dce_task_responsable.FIO
ORDER BY dce_task_responsable.FIO;";
    } else {
        $sql="SELECT dce_task_responsable.PERSON_ID, dce_task_responsable.FIO
FROM dce_task_responsable
WHERE (((dce_task_responsable.LANGUAGE)='".$_SESSION['LANGUAGE']."'))
GROUP BY dce_task_responsable.PERSON_ID, dce_task_responsable.FIO
ORDER BY dce_task_responsable.FIO;";
    }
    $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника responsable<br>".mysqli_error($mylink['link']));
    while($assoc = mysqli_fetch_assoc($quer)){
        $key = $assoc['PERSON_ID'];
        $value =$assoc['FIO'];
        $responsable[$key] = $value;}
    return($responsable);}


//----------------- Ответственный -----------------------
function get_respons() {
    global $mylink;
//  $cur_date = date('Y-m-d H:i:s');
 
        $sql="SELECT dce_task_responsable.PERSON_ID, dce_task_responsable.FIO
FROM dce_task_responsable
WHERE (((dce_task_responsable.LANGUAGE)='".$_SESSION['LANGUAGE']."') AND ((dce_task_responsable.WORK_STATUS)='W'))
GROUP BY dce_task_responsable.PERSON_ID, dce_task_responsable.FIO
ORDER BY dce_task_responsable.FIO;";

    $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника responsable<br>".mysqli_error($mylink['link']));
    while($assoc = mysqli_fetch_assoc($quer)){
        $key = $assoc['PERSON_ID'];
        $value =$assoc['FIO'];
        $responsable[$key] = $value;}
    return($responsable);}

//----------------- Постановщик -----------------------
function get_setter() {
    global $mylink;
//  $cur_date = date('Y-m-d H:i:s');
 
        $sql="SELECT dce_task_setter.PERSON_ID, dce_task_setter.FIO
FROM dce_task_setter
WHERE ((dce_task_setter.LANGUAGE)='".$_SESSION['LANGUAGE']."') 
GROUP BY dce_task_setter.PERSON_ID, dce_task_setter.FIO
ORDER BY dce_task_setter.FIO;";

    $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника setter<br>".mysqli_error($mylink['link']));
    while($assoc = mysqli_fetch_assoc($quer)){
        $key = $assoc['PERSON_ID'];
        $value =$assoc['FIO'];
        $setter[$key] = $value;}
    return($setter);}


//----------- ДЛЯ ОТОБРАЖЕНИЯ ПРИОРИТЕТА ЗАДАЧИ --------------------------------------
function prior_name($PRIORITY) {
    global $mylink;
    $sql = "SELECT dc_priority.CODE_PRIORITY, dc_priority.LANGUAGE, dc_priority.NAME
FROM dc_priority
WHERE (((dc_priority.CODE_PRIORITY)='".$PRIORITY."') AND ((dc_priority.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
    $quer = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки справочника приоритета<br>".mysqli_error($mylink['link']));
    $assoc = mysqli_fetch_assoc($quer);
    return($assoc['NAME']);}

function prior_color($PRIORITY) {
    $color="textHY";
    if ($PRIORITY=='P0'){$color="backR_textW";}
    if ($PRIORITY=='P1'){$color="backO_textW";}
    if ($PRIORITY=='P2'){$color="textHB";}
    if ($PRIORITY=='P3'){$color="textHG";}
    return($color);}


//----------- ДЛЯ ОТОБРАЖЕНИЯ СТАТУСА --------------------------------------
function status_name($STATUS) {
    global $mylink;
    if (($STATUS)==''){return('');
    }else{
        $sql = "SELECT dce_task_status.CODE_STATUS, dce_task_status.LANGUAGE, dce_task_status.NAME
FROM dce_task_status
WHERE (((dce_task_status.CODE_STATUS)='".$STATUS."') AND ((dce_task_status.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
        $quer = mysqli_query($mylink['link'], $sql) or die("Ошибка загрузки справочника статуса<br>".mysqli_error($mylink['link']));
        $assoc = mysqli_fetch_assoc($quer);
        return($assoc['NAME']);
    }
}
//---------------------------------------------------------------------------------
    function status_color($STATUS) {
        $color="textH";
        if ($STATUS=='A'){$color="backR_textW";}
        if ($STATUS=='C'){$color="backO_textW";}
        if ($STATUS=='W'){$color="textHB";}
        if ($STATUS=='E'){$color="textHG";}
        if ($STATUS=='P'){$color="textHY";}
        return($color);}    

// A - В АРХИВЕ
// C - CANCELED
// E - ОЖИДАЕТ
// P - PAUSE
// W -В РАБОТЕ


//--- Обновление и перевод записей TASK
function update_task_language($TASK_ID, $SUB_TASK) {
    global $mylink;

//---- ЯЗЫКИ ДЛЯ ДОБАВЛЕНИЯ --------------------------------
$sql_lang="SELECT dc_language_work.LANGUAGE
FROM (SELECT enterprise.task_language.LANGUAGE
FROM enterprise.task_language
WHERE (((task_language.TASK_ID)='".$TASK_ID."') AND ((task_language.SUB_TASK)='".$SUB_TASK."'))) 
AS SELECT_IDL RIGHT JOIN enterprise.dc_language_work ON SELECT_IDL.LANGUAGE = enterprise.dc_language_work.LANGUAGE
WHERE ((SELECT_IDL.LANGUAGE) Is Null);";
$quer_lang = mysqli_query($mylink['link'], $sql_lang);
while ($add_lang = mysqli_fetch_assoc($quer_lang)){
// -------- ДОБАВЛЕНИЕ ЯЗЫКОВЫХ СТРОК ------------------
$sql_add="INSERT INTO enterprise.task_language (TASK_ID, SUB_TASK, LANGUAGE, LANG_STATUS) 
VALUES ('".$TASK_ID."',  '".$SUB_TASK."', '".$add_lang['LANGUAGE']."', 'A');";
mysqli_query($mylink['link'], $sql_add);
}
//-----------------------------------------------------------


//--------------ВЫБОР ЯЗЫКА И ОСНОВЫ - $lang_from ----------------------
$sql_sel_original="SELECT task_language.TASK_ID, task_language.SUB_TASK, task_language.LANGUAGE, task_language.LANG_STATUS, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task_language
WHERE (((task_language.TASK_ID)='".$TASK_ID."') AND ((task_language.LANG_STATUS)='U') AND ((task_language.SUB_TASK)='".$SUB_TASK."'));";
$quer_original = mysqli_query($mylink['link'], $sql_sel_original);
$original = mysqli_fetch_assoc($quer_original);
if ($original['LANGUAGE']=='RUS'){$lang_from='ru';}
if ($original['LANGUAGE']=='UKR'){$lang_from='uk';}
if ($original['LANGUAGE']=='ENG'){$lang_from='en';}
if ($original['LANGUAGE']=='FRA'){$lang_from='fr';}
//---------------------------------------------------------------------


//--------------ВЫБОР ЗАПИСЕЙ ДЛЯ ОБНОВЛЕНИЯ-----------------
$sql_up="SELECT task_language.LANGUAGE
FROM task_language
WHERE (((task_language.TASK_ID)='".$TASK_ID."') AND ((task_language.SUB_TASK)='".$SUB_TASK."') AND ((task_language.LANG_STATUS)='A'));";
$quer_up = mysqli_query($mylink['link'], $sql_up);
while ($mod_lang = mysqli_fetch_assoc($quer_up)){
//-------------------------------------------------------------

//------------------ $lang_to ---------------------------------
if ($mod_lang['LANGUAGE']=='RUS'){$lang_to='ru';}
if ($mod_lang['LANGUAGE']=='UKR'){$lang_to='uk';}
if ($mod_lang['LANGUAGE']=='ENG'){$lang_to='en';}
if ($mod_lang['LANGUAGE']=='FRA'){$lang_to='fr';}

//-------------- ОБНОВЛЕНИЕ ----------------------------------
$UP_TASK_NAME=mysqli_real_escape_string($mylink['link'], gtranslate($original['TASK_NAME'], $lang_from, $lang_to));
$UP_TASK_DESCRIPTION=mysqli_real_escape_string($mylink['link'], gtranslate($original['TASK_DESCRIPTION'], $lang_from, $lang_to));
$sql_up="UPDATE enterprise.task_language SET TASK_NAME = '".$UP_TASK_NAME."', TASK_DESCRIPTION = '".$UP_TASK_DESCRIPTION."',  USER_M ='".$_SESSION['logged_user']."'
 WHERE (TASK_ID = '".$TASK_ID."') and (SUB_TASK = '".$SUB_TASK."') and (LANGUAGE = '".$mod_lang['LANGUAGE']."');";
mysqli_query($mylink['link'], $sql_up);
//------------------------------------------------------------
}//--------------ВЫБОР ЗАПИСЕЙ ДЛЯ ОБНОВЛЕНИЯ-----------------
}


function get_task_foto_max($TASK_ID, $SUB_TASK, $doc_cod){
    global $mylink;
    $sql="SELECT Max(task_biblioteka.DOC_ID) AS Max_DOC_NUM
    FROM enterprise.task_biblioteka
    WHERE (((task_biblioteka.TASK_ID)='".$TASK_ID."') AND ((task_biblioteka.SUB_TASK)='".$SUB_TASK."') AND ((task_biblioteka.CODE_DOC_TYPE)='".$doc_cod."'));";   
    $query = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки счётчика документов <br>".mysqli_error($mylink['link']));
    $assoc = mysqli_fetch_assoc($query);
    if (isset($assoc['Max_DOC_NUM'])){$DOC_ID= $assoc['Max_DOC_NUM'];} else {$DOC_ID=0;}
return ($DOC_ID);
}

function get_BIBLIOTEKA_ID(){
    global $mylink;
    $sql="SELECT Max(biblioteka.BIBLIOTEKA_ID) AS Max_BIBLIOTEKA_ID
    FROM biblioteka;";
    $query = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки счётчика документов BIBLIOTEKA_ID <br>".mysqli_error($mylink['link']));
    $assoc = mysqli_fetch_assoc($query);
    if (isset($assoc['Max_BIBLIOTEKA_ID'])){$DOC_ID= $assoc['Max_BIBLIOTEKA_ID'];} else {$DOC_ID=0;}
return ($DOC_ID);
}



?>

