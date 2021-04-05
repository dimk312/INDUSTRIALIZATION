<?php
// ДЕЙСТВИЯ
// VIEW, EDIT, NEW_TASK
require ("../../inc/base_con.inc");
require ("../../inc/date.inc");
require ("../../inc/translate.inc");
require ("../../login/access.inc");
require ("../../DICTION/PERSON/person.inc");
require ("it_task.inc");
session_start();
global  $mylink;
global  $access;
$mylink=con_my();
// $mylink['base'];
// $mylink['host'];
$SCRIPT=basename($_SERVER['SCRIPT_NAME']);
$cur_date=curdate_ms();
$access=ACCESS();
//------------------------------ Загрузка языкового интерфейса ------------------------------------------------------------------------------------------------------
$sql_lang_interface="SELECT interface_language.SCRIPT, interface_language.LANGUAGE, interface_language.ID, interface_language.TEXT
FROM interface_language
WHERE (((interface_language.SCRIPT)='".$SCRIPT."') AND ((interface_language.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
//echo '<br> sql_lang_interface - '.$sql_lang_interface;
$quer_lang_interface=mysqli_query($mylink['link'], $sql_lang_interface) or die ("Ошибка загрузки языковой схемы интерфейса.<br>".mysqli_error($mylink['link']));
while ($assoc_lang_interface = mysqli_fetch_assoc($quer_lang_interface)){
    $id_i=$assoc_lang_interface['ID'];
//  echo '<br> ID = '.$assoc_lang_interface['ID'].'  -  '.$assoc_lang_interface['TEXT'];
    $lang_interface[$id_i]=$assoc_lang_interface['TEXT'];
}//-----------------------------------------------------
// -- Язык для языка HTML <html lang="ru"> -----------
if ($_SESSION['LANGUAGE']=='RUS'){$html_lang='ru';}
if ($_SESSION['LANGUAGE']=='UKR'){$html_lang='uk';}
if ($_SESSION['LANGUAGE']=='ENG'){$html_lang='en';}
if ($_SESSION['LANGUAGE']=='FRA'){$html_lang='fr';}
$foto_cod='0100'; // 0100 - Код Фото
$otladchik='dimk312';
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------




echo '<!DOCTYPE html>';
echo '<html lang="'.$html_lang.'">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>Task</title>';
echo '<link rel="stylesheet" href="../../css/'.$_SESSION['COLOR_SCHEME'].'.css"  type="text/css"/>';
echo '</head>';
echo '<body>';
echo '<main>';


//-------------------------------------------------------------------------------------------------------------------

if (($_SESSION['logged_user']) == $otladchik) {
    echo '<br>';
    echo '<br> $SCRIPT = ' . $SCRIPT;
    print "<pre>";
    echo '<br> $_POST<br>';
    print_r($_POST);
    echo '<br> $_SESSION<br>';
    print_r($_SESSION);
    echo '<br>  $_FILES<br>';
    print_r($_FILES);
    print_r($access);
    print "</pre>";

}

//Загрузка справочников
//$workshop = get_workshop(); //Массив цехов
//$area=get_area();



if (isset($_POST['action'])) {


//----------------------------------  Сохранение изменений в подпунктах задачи   ----------------------------------------
    if (($_POST['action'])=='SUB_TASK_SAVE') { 
$sql_set="UPDATE enterprise.task SET TASK_STATUS = '".$_POST['TASK_STATUS']."'";
if ($_POST['SUB_TASK_START']!=''){$sql_set=$sql_set.", TASK_START = '".$_POST['SUB_TASK_START']."'";}      
if ($_POST['SUB_TASK_PLAN_END']!=''){$sql_set=$sql_set.", TASK_PLAN_END = '".$_POST['SUB_TASK_PLAN_END']."'";}   
if ($_POST['SUB_TASK_END']!=''){$sql_set=$sql_set.", TASK_END = '".$_POST['SUB_TASK_END']."'";} 
        
$up_sql=$sql_set." WHERE (`TASK_ID` = '".$_POST['TASK_ID']."') and (`SUB_TASK` = '".$_POST['SUB_TASK']."');";
mysqli_query($mylink['link'], $up_sql) or die ("Ошибка записи изменений в подзадаче<br>".mysqli_error($mylink['link']));

        $_POST['TASK_EDIT']='';
    echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
}
//------------------------------------------------------------------------------------------------------------------------    


    if (($_POST['action'])=='rec_image') {  // Добавление изображения
        $_POST['TASK_EDIT']='';
        echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
//----------- ЗАПИСЬ ИЗОБРАЖЕНИЯ ---------------------------------------------------------------
// Если в $_FILES существует "image" и она не NULL
 // Записываем файл в базу MYSQL 
 if (isset($_FILES['image']) and ($_FILES['image']['name'] != '')) {
    // Получаем нужные элементы массива "image"
    $fileTmpName = $_FILES['image']['tmp_name'];
    $errorCode = $_FILES['image']['error'];
    
    
    // Проверим на ошибки
    if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
        // Массив с названиями ошибок
        $errorMessages = [
          UPLOAD_ERR_INI_SIZE   =>  $lang_interface['id_err_filesize'],
          UPLOAD_ERR_FORM_SIZE  =>  $lang_interface['id_err_form_size'], 
          UPLOAD_ERR_PARTIAL    =>  $lang_interface['id_err_partial'],
          UPLOAD_ERR_NO_FILE    =>  $lang_interface['id_err_no_file'],
          UPLOAD_ERR_NO_TMP_DIR =>  $lang_interface['id_err_no_tmp_dir'],
          UPLOAD_ERR_CANT_WRITE =>  $lang_interface['id_err_cant_write'],
          UPLOAD_ERR_EXTENSION  =>  $lang_interface['id_err_extension'],
        ];
        // Зададим неизвестную ошибку
        $unknownMessage =  $lang_interface['id_err_unknown'];
        // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
        $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
        // Выведем название ошибки
        die($outputMessage);
     //   die('<div class="message warning info shadow cirkle center msgw600"><H2>'.$outputMessage.'</H2></div>');
    } else {
        // Создадим ресурс FileInfo
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        // Получим MIME-тип
        $mime = (string) finfo_file($fi, $fileTmpName);
    //echo '<BR> mime = '.$mime;
        // Проверим ключевое слово image (image/jpeg, image/png и т. д.)
        if (strpos($mime, 'image') === false) die ($lang_interface['id_err_image_only']);
    
    
    $filesize = filesize($fileTmpName);
    
        // Результат функции запишем в переменную
        $imagesize = getimagesize($fileTmpName);
    
        // Зададим ограничения для картинок
        $limitBytes  = 4096 * 4096 * 5;
        $limitWidth  = 12000;
        $limitHeight = 12000;
    
        // Проверим нужные параметры
        if ((filesize($fileTmpName) > $limitBytes))     die($lang_interface['id_err_more80mb']);  // больше 80 мегабайт
        if ($imagesize[1] > $limitHeight)             die($lang_interface['id_err_limit_height']); // Высота >12000
        if ($imagesize[0] > $limitWidth)              die($lang_interface['id_err_limit_width']); // ширина >12000
    
        // Сгенерируем расширение файла на основе типа картинки
        $extension = image_type_to_extension($imagesize[2]);
    //  echo '<br> $extension = '.$extension;
    
      //-- Содержимое файла
      $image_data=file_get_contents($_FILES['image']['tmp_name']);
    
    // ---- ШИФРОВАНИЕ И ОБРАБОТКА ФАЙЛА ПЕРЕД ЗАПИСЬЮ В MYSQL -------------------------------------------------------------------------------
      // Сгенерируем новое имя файла через функцию getRandomFileName()
        // $name = getRandomFileName($fileTmpName);
    
        // Сократим .jpeg до .jpg
       // $format = str_replace('jpeg', 'jpg', $extension);
    
     $image_data = mysqli_real_escape_string($mylink['link'], $image_data);
    
    //----------------------------------------------------------------------------------------------------------------------------------------
    
    $file_name=mysqli_real_escape_string($mylink['link'], $_FILES['image']['name']);
    $file_type=mysqli_real_escape_string($mylink['link'], $_FILES['image']['type']);

    $extension = substr(strrchr($file_name,'.'), 1);
   // $extension = substr($file_name, strrpos($file_name, '.') + 1);
    // echo '<br> file_name = '.$file_name;
    // echo '<br> file_type = '.$file_type;
    
    $doc_cod='0100'; // 0100 - Код Фото
    $DOC_ID=get_task_foto_max($_POST['TASK_ID'], $_POST['SUB_TASK'], $doc_cod);
    $DOC_ID++;
    $BIBLIOTEKA_ID=get_BIBLIOTEKA_ID();
    $BIBLIOTEKA_ID++;

    $file_size=filesize($fileTmpName);
 //   if (($_SESSION['logged_user']) == $otladchik) { echo '<BR>file_size - '.$file_size;}
    // Запись изображения в библиотеку
$sql_write_bib="INSERT INTO enterprise.biblioteka (BIBLIOTEKA_ID, FILE, FILE_NAME, FILE_EXTENSION, FILE_SIZE, USER_ADD, USER_MOD) 
VALUES ('".$BIBLIOTEKA_ID."', '".$image_data."', '".$file_name."', '".$extension."', '".$file_size."', '".$_SESSION['logged_user']."', '".$_SESSION['logged_user']."');";
mysqli_query($mylink['link'], $sql_write_bib) or die ("Ошибка записи в BIBLIOTEKA<br>".mysqli_error($mylink['link']));


// Запись таблици привязки документов biblioteka_task
$sql_write_task_id="INSERT INTO enterprise.task_biblioteka (BIBLIOTEKA_ID, TASK_ID, SUB_TASK, DOC_ID, FILE_TYPE, CODE_DOC_TYPE, FILE_NAME, USER_M)
 VALUES ('".$BIBLIOTEKA_ID."', '".$_POST['TASK_ID']."', '".$_POST['SUB_TASK']."', '".$DOC_ID."', '".$file_type."', '".$doc_cod."', '".$file_name."', '".$_SESSION['logged_user']."');";
mysqli_query($mylink['link'], $sql_write_task_id) or die ("Ошибка записи в BIBLIOTEKA<br>".mysqli_error($mylink['link']));



// Потом проработать извлечение документов
/*
    $sql_rec="INSERT INTO enterprise.person_file (PERSON_ID, DOC_ID, FILE_TYPE, FILE, DOC_NAME, CODE_DOC_TYPE) 
    VALUES ('".$_POST['PERSON_ID']."', ".$FOTO_DOC_ID.", '".$file_type."', '".$image_data."', '".$file_name."', '".$foto_cod."');";
    mysqli_query($mylink['link'], $sql_rec) or die ("Ошибка записи изображения в базу <br>".mysqli_error($mylink['link']));
 */   
    //    echo '<BR> Файл успешно записан в базу MYSQL !';
      }
     }
    //----------- КОНЕЦ ЗАПИСЬ ИЗОБРАЖЕНИЯ ---------------------------------------------------------------





    }


    if (($_POST['action'])=='rec_sub_task') {  // Добавление нового пункта задачи
        $sql_get_sub_num="SELECT Max(task.SUB_TASK) AS Max_SUB_TASK
        FROM task
        GROUP BY task.TASK_ID
        HAVING (((task.TASK_ID)='".$_POST['TASK_ID']."'));";
        $quer_get_sub_num=mysqli_query($mylink['link'], $sql_get_sub_num) or die ("Ошибка sql_get_sub_num.<br>".mysqli_error($mylink['link']));
        $get_sub_num = mysqli_fetch_assoc($quer_get_sub_num);  
        $SUB_NUMBER=$get_sub_num['Max_SUB_TASK']+1;
    if (($_SESSION['logged_user']) == $otladchik) {
        echo '$sql_get_sub_num ='.$sql_get_sub_num.'<BR>';
        echo '$SUB_NUMBER ='.$SUB_NUMBER.'<BR>';}
        //Эскапирование текстовых полей


        $TASK_NAME = mysqli_real_escape_string($mylink['link'], $_POST['TASK_NAME']);  //Экранируем спецсимволы
        $TASK_DESCRIPTION = mysqli_real_escape_string($mylink['link'], $_POST['TASK_DESCRIPTION']);  //Экранируем спецсимволы

        // Запись 
        $sql_task_add_sub="INSERT INTO enterprise.task (TASK_ID, SUB_TASK) VALUES ('".$_POST['TASK_ID']."', '".$SUB_NUMBER."');";
        $sql_sub_lang="INSERT INTO enterprise.task_language (TASK_ID, SUB_TASK, LANGUAGE, LANG_STATUS, TASK_NAME, TASK_DESCRIPTION)
         VALUES ('".$_POST['TASK_ID']."', '".$SUB_NUMBER."', '".$_SESSION['LANGUAGE']."', 'U', '".$TASK_NAME."', '".$TASK_DESCRIPTION."');";

mysqli_query($mylink['link'], $sql_task_add_sub) or die ("Ошибка записи sub task.<br>".mysqli_error($mylink['link']));
mysqli_query($mylink['link'], $sql_sub_lang) or die ("Ошибка записи sub task lang.<br>".mysqli_error($mylink['link']));

update_task_language($_POST['TASK_ID'], $SUB_NUMBER);

    }


    if (($_POST['action'])=='add_sub_task') {  // Открываем страницу для ввода новой подзадачи
        echo '<div align="center">';
        echo '<table border width="100%">';
        echo '<tr>';
        echo '<td colspan=2 class="TH1" valign="middle">&nbsp;'.$lang_interface['id_taskN'].'&nbsp;'.$_POST['TASK_ID'].'&nbsp</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan=2 class="TH3" valign="middle">&nbsp;'.$lang_interface['id_add_step'].'&nbsp</td>';
        echo '</tr>';
/*
        echo '<tr>';
        echo '<th width="5%" class="textHB" width="220px" class="textH FSB14" align="center">&nbsp;';
        echo '</th>';
        echo '<th class="textHB" width="95%" class="textH FSB14" align="center" >&nbsp;Название и описание &nbsp';
        echo '</th>';
        echo '</tr>';
*/
        echo '<form action="it_task.php" target="TASK" enctype="multipart/form-data" method="post">';

        echo '<tr>';
        echo '<td nowarp width="5% class="FS12" align="right">&nbsp;'.$lang_interface['id_name_point'].'&nbsp;';
        echo '<td class="FS12" align="left">';
        echo '<input type="test" name="TASK_NAME" id="long" value="">';
        echo '</tr>';

        echo '<tr>';
        echo '<td nowrap width="5% class="FS12" align="right">&nbsp;'.$lang_interface['id_descrip_point'].'&nbsp;';
        echo '<td class="FS12" align="left">';
        echo '<input type="test" name="TASK_DESCRIPTION" id="long" value="">';
        echo '</td>';
        echo '</tr>';

        echo '</table>';
        echo '<br>';
        echo '<div align="center">';
        echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
        echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
        echo '<button class="fly_button" type="submit" name="action" value="rec_sub_task">';
        echo '<table>';
        echo '<tr>';
        echo '<td class="FS16" align="center">';
        echo '<img src="IMG/Sub_task_save.png" width="100px" alt="View task" title="'.$lang_interface['id_rec_point'].'"></td>';
        echo '<td class="FS16">&nbsp;'.$lang_interface['id_rec_point'].'&nbsp;</td>';
        echo '</tr>';
        echo '</table>';
        echo '</button>';
        echo '<form>';
        echo '</div>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
    
        die(); // Остановка дальнейшей загрузки страницы

    }


    if (($_POST['action'])=='NEW_REC') {
        if ($access['ITЕТ']== 'E') { //Есть право редактировать задачу
            $_POST['TASK_EDIT']='';
        echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
        }
        if (($_POST['task_name'])=='') {$errors[] = $lang_interface['id_no_name_task'];}
        if (($_POST['task_opis'])=='') {$errors[] = $lang_interface['id_no_desck_task'];}

        if (empty($errors)) {
// Присваиваем номер и записываем задачу
$SQL_getmaxid="SELECT Max(task.TASK_ID) AS Max_TASK_ID
FROM task;";
$query_MAX_ID = mysqli_query($mylink['link'], $SQL_getmaxid) or die ("Ошибка загрузки индекса заданий <br>".mysqli_error($mylink['link']));
$assoc_MAX_ID = mysqli_fetch_assoc($query_MAX_ID);
if (isset($assoc_MAX_ID['Max_TASK_ID'])){$TASK_ID= $assoc_MAX_ID['Max_TASK_ID']+1;} else {$TASK_ID=0;}
$SUB_NUMBER=0; // Пункт плана для основной задачи = 0

// Записываем глвную задачу
$UP_TNAME = mysqli_real_escape_string($mylink['link'], $_POST['task_name']);  //Экранируем спецсимволы
$UP_TOPIS = mysqli_real_escape_string($mylink['link'], $_POST['task_opis']);  //Экранируем спецсимволы
$PERSON_ID=get_pfu($_SESSION['logged_user']);


if (($_POST['WORKSHOP'])=='ALL') {$workshop_rec='';} else {$workshop_rec=$_POST['WORKSHOP'];}
if (($_POST['AREA'])=='ALL') {$area_rec='';} else {$area_rec=$_POST['AREA'];}

$SQL_REC="INSERT INTO enterprise.task (TASK_ID, TASK_STATUS, WORKSHOP, WORKSHOP_AREA, TASK_SETED, PERSON_SET_TASK, TASK_PRIORITY, USER_M)
 VALUES ($TASK_ID, 'E', '".$workshop_rec."', '".$area_rec."', '".$cur_date."', '".$PERSON_ID."', '".$_POST['PRIORITY']."', '".$_SESSION['logged_user']."');";
mysqli_query($mylink['link'], $SQL_REC) or die ("Ошибка записи новой задачи <br>".mysqli_error($mylink['link']));


$NEW_task=mysqli_real_escape_string($mylink['link'], $_POST['task_name']);
$NEW_description=mysqli_real_escape_string($mylink['link'], $_POST['task_opis']);
$SQL_REC_lang="INSERT INTO enterprise.task_language (TASK_ID, LANGUAGE, LANG_STATUS, TASK_NAME, TASK_DESCRIPTION) 
VALUES ($TASK_ID, '".$_SESSION['LANGUAGE']."', 'U', '".$NEW_task."', '".$NEW_description."'); ";
mysqli_query($mylink['link'], $SQL_REC_lang) or die ("Ошибка записи языковой части новой задачи <br>".mysqli_error($mylink['link']));

//Запус языкового обновление
update_task_language($TASK_ID, $SUB_NUMBER);

$_POST['TASK_ID']=$TASK_ID;
        }else {
            // Выводим ошибки и снова форма добавления задачи
            foreach ($errors as $key => $value) {echo '<div class="message warning msgw300 FSB20 center shadow cirkle" align="center">' . $value . '</div><br>';}
        }
    }  //----  action ==NEW_REC

 
// ------------------------- ЗАПИСЬ ИЗМЕНЕНИЙ ----------------------------------------------------
if (($_POST['action'])=='SAVE') {
    $_POST['TASK_EDIT']='';
    echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT

    $sql_up_where = " WHERE (TASK_ID = '".$_POST['TASK_ID']."') and (SUB_TASK = '0')";
    $sql_up_set = '';

 
    if (($_POST['WORKSHOP'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", WORKSHOP = '".$_POST['WORKSHOP']."'";} else {$sql_up_set=" WORKSHOP = '".$_POST['WORKSHOP']."'";}}
    if (($_POST['WORKSHOP_AREA'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", WORKSHOP_AREA = '".$_POST['WORKSHOP_AREA']."'";} else {$sql_up_set=" WORKSHOP_AREA = '".$_POST['WORKSHOP_AREA']."'";}}
    if (($_POST['TASK_PRIORITY'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_PRIORITY = '".$_POST['TASK_PRIORITY']."'";} else {$sql_up_set=" TASK_PRIORITY = '".$_POST['TASK_PRIORITY']."'";}}
    if (($_POST['TASK_STATUS'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_STATUS = '".$_POST['TASK_STATUS']."'";} else {$sql_up_set=" TASK_STATUS = '".$_POST['TASK_STATUS']."'";}}
    
    if (($_POST['work_folder'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_FOLDER = '".mysqli_real_escape_string($mylink['link'], $_POST['work_folder'])."'";} 
    else {$sql_up_set=" TASK_FOLDER = '".mysqli_real_escape_string($mylink['link'], $_POST['work_folder']."'");}}

    if (($_POST['TASK_SETED'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_SETED = '".$_POST['TASK_SETED']."'";} else {$sql_up_set=" TASK_SETED = '".$_POST['TASK_SETED']."'";}}
    if (($_POST['TASK_START'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_START = '".$_POST['TASK_START']."'";} else {$sql_up_set=" TASK_START = '".$_POST['TASK_START']."'";}}
    if (($_POST['TASK_PLAN_END'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_PLAN_END = '".$_POST['TASK_PLAN_END']."'";} else {$sql_up_set=" TASK_PLAN_END = '".$_POST['TASK_PLAN_END']."'";}}
    if (($_POST['TASK_END'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", TASK_END = '".$_POST['TASK_END']."'";} else {$sql_up_set=" TASK_END = '".$_POST['TASK_END']."'";}}

    if (($_POST['PERSON_SET_TASK'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", PERSON_SET_TASK = '".$_POST['PERSON_SET_TASK']."'";} else {$sql_up_set=" PERSON_SET_TASK = '".$_POST['PERSON_SET_TASK']."'";}}
    if (($_POST['RESPONSIBLE'])!=''){if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", PERSON_RESPONSIBLE = '".$_POST['RESPONSIBLE']."'";} else {$sql_up_set=" PERSON_RESPONSIBLE = '".$_POST['RESPONSIBLE']."'";}}

    if (($sql_up_set)!=''){$sql_up_set=$sql_up_set. ", USER_M = '".$_SESSION['logged_user']."'";} else {$sql_up_set=" USER_M = '".$_SESSION['logged_user']."'";}
    
 $sql_up_task='UPDATE enterprise.task SET '.$sql_up_set.$sql_up_where;
 //if (($_SESSION['logged_user']) == $otladchik) {echo 'sql_up_task ='.$sql_up_task.'<BR>'; echo 'SQL_up_lang ='.$SQL_up_lang.'<BR>';}
mysqli_query($mylink['link'], $sql_up_task) or die ("Ошибка обновления задачи.<br>".mysqli_error($mylink['link']));
} //------------------------------ ЗАПИСЬ ИЗМЕНЕНИЙ ------------------------------------------------------------------    









} // ----- isset action



if (isset($_POST['EDIT_LANG'])) { // Открываем форму редактирования языков
    if ($_POST['EDIT_LANG']== 'MOD_SAVE') { // перед открытием обновление
        $TASK_NAME = mysqli_real_escape_string($mylink['link'], $_POST['TASK_NAME']);  //Экранируем спецсимволы
        $TASK_DESCRIPTION = mysqli_real_escape_string($mylink['link'], $_POST['TASK_DESCRIPTION']);  //Экранируем спецсимволы

    $SQL_UP_LANG="UPDATE enterprise.task_language SET TASK_NAME = '".$TASK_NAME."', TASK_DESCRIPTION = '".$TASK_DESCRIPTION."', LANG_STATUS = 'U'
     WHERE (LANGUAGE = '".$_POST['LANGUAGE']."') and (TASK_ID = '".$_POST['TASK_ID']."') and (SUB_TASK = '".$_POST['SUB_TASK']."');";
        mysqli_query($mylink['link'], $SQL_UP_LANG) or die("Ошибка обновления языка задачи.<br>".mysqli_error($mylink['link']));
        //    if (($_SESSION['logged_user']) == $otladchik) {echo 'SQL_UP_LANG ='.$SQL_UP_LANG.'<BR>';}
    }


    if ($access['ITEL']== 'E') {
        echo '<div align="center">';
        echo '<table border width="100%">';
        echo '<tr>';
        echo '<td colspan=5 class="TH1" valign="middle">&nbsp;'.$lang_interface['id_taskN'].'&nbsp;'.$_POST['TASK_ID'].'&nbsp';
        echo '</td>';
        echo '</tr>';


        echo '<tr>';
        echo '<th class="textHB" width="10px" class="textH FSB14" align="center" >&nbsp;'.$lang_interface['id_step'].'&nbsp';
        echo '</th>';
        echo '<th class="textHB" width="10px" class="textH FSB14" align="center" >&nbsp;'.$lang_interface['id_lang'].'&nbsp';
        echo '</th>';
        echo '<th width="90%" class="textHB" class="textH FSB14" align="center">&nbsp;'.$lang_interface['id_name'].'&nbsp';
        echo '</th>';
        //       echo '<th width="60%" class="textHB" class="textH FSB14" align="center">&nbsp;Описание&nbsp';
        //       echo '</th>';
        echo '<th width="1%" class="textHB" width="220px" class="textH FSB14" align="center">&nbsp;';
        echo '</th>';
        echo '</tr>';

        $sql_lang="SELECT task_language.TASK_ID, task_language.SUB_TASK, task_language.LANGUAGE, task_language.LANG_STATUS, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task_language
WHERE (((task_language.TASK_ID)='".$_POST['TASK_ID']."'))
ORDER BY task_language.SUB_TASK;";
        $quer_lang=mysqli_query($mylink['link'], $sql_lang) or die("Ошибка загрузки языковых полей.<br>".mysqli_error($mylink['link']));
        while ($tasklang = mysqli_fetch_assoc($quer_lang)) { // Запуск перебора выбранных языковых полей

            if ($tasklang['LANG_STATUS']=='U') {
                $celcolor='textHG';
            } else {
                $celcolor='textHO';
            }
            echo '<tr>';
            echo '<form enctype="multipart/form-data" method="post">';
            echo '<input type="hidden" name="TASK_ID" value="'.$tasklang['TASK_ID'].'">';
            echo '<input type="hidden" name="SUB_TASK" value="'.$tasklang['SUB_TASK'].'">';
            echo '<input type="hidden" name="LANGUAGE" value="'.$tasklang['LANGUAGE'].'">';
            echo '<td class="'.$celcolor.' FS12" align="center">'.$tasklang['SUB_TASK'].'</td>';
            echo '<td class="'.$celcolor.' FS12" align="center">'.$tasklang['LANGUAGE'].'</td>';
            echo '<td class="'.$celcolor.' FS12" align="left">';
            echo '<input type="test" name="TASK_NAME" id="long" value="'.$tasklang['TASK_NAME'].'">';
            echo '<input type="test" name="TASK_DESCRIPTION" id="long" value="'.$tasklang['TASK_DESCRIPTION'].'">';
            echo '</td>';

            echo '<td class="'.$celcolor.' FS12" align="left">';
            echo '<button value="MOD_SAVE" name="EDIT_LANG"><img src="IMG/save.png"  width="40px" alt="Save" style="vertical-align: middle" title="'.$lang_interface['id_save_modif'].'"></button>';
            echo '</td>';
            echo '</form>';
            echo '</tr>';
        }   // Запуск перебора выбранных языковых полей
    } // Открываем форму редактирования языков

echo '</table>';
    echo '<br>';
    echo '<div align="center">';

    echo '<table>';
    echo '<tr>';
    echo '<td nowarp>';
    echo '&nbsp;';

    echo '<form action="it_task.php" target="TASK" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
    echo '<button class="fly_button" type="submit" name="TASK_VIEW">';
    echo '<table>';
    echo '<tr>';
    echo '<td class="FS16" align="center">';
    echo '<img src="IMG/task_view.png"  width="100px" alt="View task" title="'.$lang_interface['id_close_view'].'">';
    echo '</td>';
    echo '<td class="FS16">&nbsp;'.$lang_interface['id_close_view'].'&nbsp;</td>';
    echo '</tr>';
    echo '</table>';
    echo '</button>';
    echo '</form>';

    echo '&nbsp;';
    echo '</td>';
    echo '<td nowarp>';
    echo '&nbsp;';

    echo '<form action="it_task.php" target="TASK" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
    echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
    echo '<button class="fly_button" type="submit" name="TASK_VIEW">';
    echo '<table>';
    echo '<tr>';
    echo '<td class="FS16" align="center">';
    echo '<img src="IMG/task_edit.png"  width="100px" alt="View task" title="'.$lang_interface['id_close_edit'].'">';
    echo '</td>';
    echo '<td class="FS16">&nbsp;'.$lang_interface['id_close_edit'].'&nbsp;</td>';
    echo '</tr>';
    echo '</table>';
    echo '</button>';
    echo '</form>';

    echo '&nbsp;';
    echo '</td>';
    echo '</tr>';
    echo '</table>';

    echo '</div>';
    echo '<br>';
    echo '<br>';
    echo '<br>';

    die(); // Остановка дальнейшей загрузки страницы
 }
//----------------------------------------------------------------------------------------------------------------------------------------
// Если задан TASK_ID открываем форму редактирования
//Если не задан TASK_ID открываем форму создания новой задаяи
//-----------------------------------------------------------------------------------------------------------------------------------------
if (isset($_POST['TASK_ID'])) {
    echo '<form id="task" target="TASK" enctype="multipart/form-data" method="post">';
  
//Выборка данных для задачи
$SQL_task="SELECT task.TASK_ID, task.SUB_TASK, task_language.LANGUAGE, task.TASK_STATUS, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_SETED, task.PERSON_SET_TASK, task.PERSON_RESPONSIBLE, task.TASK_PRIORITY, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.TASK_FOLDER, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
GROUP BY task.TASK_ID, task.SUB_TASK, task_language.LANGUAGE, task.TASK_STATUS, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_SETED, task.PERSON_SET_TASK, task.PERSON_RESPONSIBLE, task.TASK_PRIORITY, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.TASK_FOLDER, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
HAVING (((task.TASK_ID)=".$_POST['TASK_ID'].") AND ((task.SUB_TASK)=0) AND ((task_language.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
$quer_task=mysqli_query($mylink['link'], $SQL_task) or die ("Ошибка TASK.<br>".mysqli_error($mylink['link']));
while ($taskinfo = mysqli_fetch_assoc($quer_task)) { // Запуск перебора выбранных $taskinfo['TASK_ID']

//    $_POST['action'] == TASK_EDIT
    // ВЫВОДИМ ГОЛОВНУЮ ЧАСТЬ

    echo '<div align="center">';
    echo '<table border width="100%">';
    echo '<tr>';
    echo '<td rowspan="3" width="420px" align="center" valign="top">';

    //---------------------- ЗАГРУЗКА ИЗОБРАЖЕНИЯ ---------------------------------------------------  
    $sql_get_id="SELECT Max(task_biblioteka.DOC_ID) AS Max_DOC_ID
    FROM task_biblioteka
    WHERE (((task_biblioteka.TASK_ID)='".$taskinfo['TASK_ID']."') AND ((task_biblioteka.SUB_TASK)='0') AND ((task_biblioteka.CODE_DOC_TYPE)='".$foto_cod."'));";
    $query_get_id = mysqli_query($mylink['link'], $sql_get_id) or die ("Ошибка загрузки id фото <br>".mysqli_error($mylink['link']));
    $assoc_doc_id=mysqli_fetch_assoc($query_get_id);
    $img_doc_id=$assoc_doc_id['Max_DOC_ID'];
    
    $sql_img="SELECT task_biblioteka.FILE_TYPE, task_biblioteka.FILE_NAME, biblioteka.FILE
    FROM biblioteka INNER JOIN task_biblioteka ON biblioteka.BIBLIOTEKA_ID = task_biblioteka.BIBLIOTEKA_ID
    WHERE (((task_biblioteka.DOC_ID)='".$img_doc_id."') AND ((task_biblioteka.TASK_ID)='".$taskinfo['TASK_ID']."')
     AND ((task_biblioteka.SUB_TASK)='0') AND ((task_biblioteka.CODE_DOC_TYPE)='".$foto_cod."'));";
    $query_img = mysqli_query($mylink['link'], $sql_img) or die ("Ошибка загрузки фото <br>".mysqli_error($mylink['link']));
    $img = mysqli_fetch_assoc($query_img);
          
        if (($img_doc_id) != 0) {
        //    echo '<a href="data:'.$img['FILE_TYPE'].';base64, '.base64_encode($img['FILE']).'" target="_blank">';
    echo '<img src = "data:'.$img['FILE_TYPE'].';base64, '.base64_encode($img['FILE']).'" width = "420px" style="vertical-align: middle"';
 //    echo '</a>'; 
} else {                   
   // echo ' <a href="IMG/task_drawing_board.png" target="_blank">';
echo ' <img src="IMG/task_drawing_board.png" width="420px"  style="vertical-align: middle"';
//        echo '</a>'; 
}
//----------------------------------------------------------------------------------


if (isset($_POST['TASK_EDIT']) and ($access['ITLI']=='A')) {
           echo '<br>';
           echo '<p align="center"><input type="file" name="image" multiple accept="image/*">';
           echo '<input type="submit" name="action" value="rec_image"></p>';
    }
    echo '</td>';

    echo '<td class="TH1"  height="10%" width="120px">&nbsp;&nbsp;'.$taskinfo['TASK_ID'].'&nbsp;';
    echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
    echo '<input type="hidden" name="SUB_TASK" value="'.$taskinfo['SUB_TASK'].'">';
    echo '</td>';


    $tasklen=strlen($taskinfo['TASK_NAME']);
    if ($tasklen<85) {
        $th='"warp Back_H2 FC_W FSB20"';
    } else {
        if ($tasklen<100) {
            $th='"warp Back_H2 FC_W FSB18"';
        } else {
            $th='"warp Back_H2 FC_W FSB16"';
        }
    }
    echo '<td height="10%" class='.$th.' align="left" valign="top">&nbsp;'.$taskinfo['TASK_NAME'].'</td>';

    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="2" height="10%" class="warp Back_H3 FC_W FSB18" align="left" valign="top">';
    echo '&nbsp;'.$taskinfo['TASK_DESCRIPTION'].'&nbsp;';
    echo '</td>';
    echo '</tr>';


    echo '<tr>';
    echo '<td colspan="2" align="left" valign="top">';

    //--------------------------------------------------------------------------
    echo '<table border width="100%">';
    echo '<tr>';

    $workshop = get_workshop(); //Массив цехов
    if (isset($_POST['TASK_EDIT']) and ($access['ITWO']=='E')) {
        echo '<td class="Back_H4 FSB16" align="center" width="320px">';
    echo '<select size="1" class="Back_H4 FSB16" name="WORKSHOP">';
    if ($taskinfo['WORKSHOP']==''){echo '<option class="FS12" selected value="">&nbsp;</option>';} else {echo '<option class="FS12" value="">&nbsp;&nbsp;</option>';}
        foreach ($workshop as $key => $value) {
            if ($taskinfo['WORKSHOP']==$key){echo '<option class="Back_H4 FSB16" align="center" selected value="'.$key.'">('.$key.')-'.$value.'&nbsp;</option>';} else {
                echo '<option class="Back_H4 FSB16"  align="center" value="'.$key.'">('.$key.')-'.$value.'&nbsp;</option>';}}
                echo '</select>';
                echo '</td>';
    } else {
        echo '<td align="center" width="320px" class="Back_H4 FSB16">&nbsp;'.$workshop[$taskinfo['WORKSHOP']].'</td>';
    }

    echo '</td>';

    $area = get_area_et(); //Массив рабочих зон
    if (isset($_POST['TASK_EDIT']) and ($access['ITWA']=='E')) {
    echo '<td colspan="4" class="Back_H4 FSB16" align="left">';
    echo '<select size="1" class="Back_H4 FSB16" name="WORKSHOP_AREA">';
    if ($taskinfo['WORKSHOP_AREA']==''){echo '<option class="FS12" selected value="">&nbsp;</option>';} else {echo '<option class="FS12" value="">&nbsp;&nbsp;</option>';}
        foreach ($area as $key => $value) {
            if ($taskinfo['WORKSHOP_AREA']==$key){echo '<option class="Back_H4 FSB16" align="center" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
                echo '<option class="Back_H4 FSB16"  align="center" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
                echo '</select>';
                echo '</td>';
    } else {
        echo '<td colspan="4" class="Back_H4 FSB16" align="left">&nbsp;'.$area[$taskinfo['WORKSHOP_AREA']].'</td>';
    }


    echo '</td>';
    echo '</tr>';

//    echo '<td rowspan="5" class="FS14" valign="top" align="left">&nbsp;Код главной сборки или детали (деталей):<br><b>&nbsp;UKRC000.000.000.00_главная сборка&nbsp;</b>'; //.$taskinfo['TASK_FOLDER']
//    echo '</td>';

    echo '<tr>';
    echo '<td class="FS14" align="left">&nbsp;'.$lang_interface['id_task_priority'];
    echo '</td>';

    if (isset($_POST['TASK_EDIT']) and ($access['ITTP']=='E')) {
        $priority = get_prior(); //Массив приоритета
        echo '<td align="center" width="140px" class="'.prior_color($taskinfo['TASK_PRIORITY']).'">';
    echo '<select size="1" class="'.prior_color($taskinfo['TASK_PRIORITY']).' FS12" name="TASK_PRIORITY">';
        foreach ($priority as $key => $value) {
            if ($taskinfo['TASK_PRIORITY']==$key){echo '<option class="'.prior_color($key).' FS12" align="center" selected value="'.$key.'">&nbsp;'.$key.' - '.$value.'&nbsp;</option>';} else {
                echo '<option class="'.prior_color($key).' FS12"  align="center" value="'.$key.'">&nbsp;'.$key.' - '.$value.'&nbsp;</option>';}}
                echo '</select>';
                echo '</td>';
    } else {
        echo '<td align="center" width="140px" class="'.prior_color($taskinfo['TASK_PRIORITY']).'">&nbsp;'.$taskinfo['TASK_PRIORITY'].'&nbsp;-&nbsp;'.prior_name($taskinfo['TASK_PRIORITY']).'</td>';
    }
  

    echo '</td>';
    echo '<td rowspan="3" class="textH FS14" align="left" width="240px">&nbsp;'.$lang_interface['id_task_seted'].'<br><b>&nbsp;&nbsp;';

    if (isset($_POST['TASK_EDIT']) and ($access['ITTE']=='E')) {
        $setter = get_setter(); //Массив ответственных
    echo '<select size="1" class="FS12" name="PERSON_SET_TASK">';
    if ($taskinfo['PERSON_SET_TASK']==''){echo '<option class="FS12" selected value="">&nbsp;</option>';} else {echo '<option class="FS12" value="">&nbsp;&nbsp;</option>';}

        foreach ($setter as $key => $value) {
            if ($taskinfo['PERSON_SET_TASK']==$key){echo '<option class="FS12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
                echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
                echo '</select>';
    } else {
        echo get_FIO($taskinfo['PERSON_SET_TASK']).'&nbsp;';
    }

    echo '</b></td>';

    echo '<td rowspan="6" width="420">';
    echo '<table width="100%">
       <tr><td width="100px" align="left"><img src="IMG/work_folder.png"  width="80px" alt="Work folder"></td>
       <td align="Left" valign="top" class="warp"><B>'.$lang_interface['id_work_folder'].'</B><br>';

       if (isset($_POST['TASK_EDIT']) and ($access['ITTF']=='E')) {

        

// '<input type="text" name="work_folder"  value="'.$taskinfo['TASK_FOLDER'].'">';   

        echo  '<textarea name="work_folder" cols="50" rows="5">'.$taskinfo['TASK_FOLDER'].'</textarea>';   
       } else {
        echo '&nbsp;'.$taskinfo['TASK_FOLDER'].'&nbsp;';
       }
    echo '</td>';
    echo '</tr></table>';

    echo '</td>';

//        echo '<td rowspan="5" class="FS14" valign="top" align="left">&nbsp;Код главной сборки или детали (деталей):<br><b>&nbsp;UKRC000.000.000.00_главная сборка&nbsp;</b>'; //.$taskinfo['TASK_FOLDER']
//        echo '</td>';
    echo '<td rowspan="6" class="FS14" align="center" width="260px">';
    echo '&nbsp;';

/*    
    echo '<table>';
echo '<tr>';

// echo '<form action="it_task.php" value="VIEW_TASK" target="_blank" method="post">';

echo '<td width="5%" align="center">';
echo '<form action="it_task.php" value="VIEW_TASK" method="post">';
echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
echo '<button>';
echo '<img src="IMG/task_view.png" height="40px" alt="View task" title="Просмотр задачи">';
echo '</button>';
echo '</form>';
echo '</td>';


echo '</td>';
echo '</tr>';
    echo '</table>';
*/

if (isset($_POST['TASK_EDIT'])) {  
    echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
    echo '<table>';
    echo '<tr><td>';
    //name="TASK_EDIT"
    echo '<button type="submit" value="SAVE" name="action">';
    echo '<table width="200px">';
    echo '<tr><td><img src="IMG/task_save.png"  width="50px" alt="Save"></td>';
    echo '<td>'.$lang_interface['id_save'].'</td></tr>';
    echo '</table>';
    echo '</button>';
    echo '</td></tr>';

 
if ($access['ITEL']=='E') { // Если разрешено редактирование языковых полей задач
    echo '<tr><td>';
    echo '<button type="submit" name="EDIT_LANG">';
    echo '<table width="200px">';
    echo '<tr><td><img src="../../IMAGES/lang.png" height="50px" alt="Edit lang"></td>';
    echo '<td>'.$lang_interface['id_edit_lang'].'</td></tr>';
    echo '</table>';
    echo '</button>';
    echo '</td></tr>';
}
    

if ($access['ITAS']=='A') { // Если разрешено добавление подзадач
    echo '<tr><td>';
    echo '<button type="submit" name="action" value="add_sub_task" >';
    echo '<table width="200px">';
    echo '<tr><td><img src="IMG/AddSub_task.png"  width="50px" alt="add_sub_task"></td>';
    echo '<td align="center">'.$lang_interface['id_add_sub_task'].'</td></tr>';
    echo '</table>';
    echo '</button>';
    echo '</td></tr>';
}

    echo '</table>';
}



    echo '</td>';
    echo '</tr>';

//--------------------------------------------------------------------
echo '<tr>';
echo '<td class="FS14" align="left">&nbsp;'.$lang_interface['id_task_status'];
echo '</td>';

if (isset($_POST['TASK_EDIT']) and ($access['ITTS']=='E')) {
    $status = get_status(); //Массив статуса
    echo '<td align="center" width="140px" class="'.status_color($taskinfo['TASK_STATUS']).'">';
echo '<select size="1" class="'.status_color($taskinfo['TASK_STATUS']).' FS12" name="TASK_STATUS">';
    foreach ($status as $key => $value) {
        if ($taskinfo['TASK_STATUS']==$key){echo '<option class="'.status_color($key).' FS12" align="center" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} 
        else {echo '<option class="'.status_color($key).' FS12"  align="center" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
            echo '</select>';
            echo '</td>';
} else {
    echo '<td align="center" width="140px" class="'.status_color($taskinfo['TASK_STATUS']).'">&nbsp;'.status_name($taskinfo['TASK_STATUS']).'</td>';
}

// echo '<td class="backR_textW FSB14" align="center" width="100px">&nbsp;'.$taskinfo['TASK_STATUS'].'&nbsp;';
echo '</td>';
echo '</tr>';
//---------------------------------------------------------------------

    echo '<tr>';
    echo '<td class="FS14" align="left">&nbsp;'.$lang_interface['id_set_date'];  
    echo '</td>';
    echo '<td class="textH FSB14" align="center">';
    if (isset($_POST['TASK_EDIT']) and ($access['ITDT']=='E')) { // Дата постановки задачи
        echo  '<input align="center" class="FS14" name="TASK_SETED" type="date" value="'.@date_my2web($taskinfo['TASK_SETED']).'">';    
        } else {
        echo '&nbsp;'.date_my2ru($taskinfo['TASK_SETED']).'&nbsp;';
        } 
    echo '</td>';

    echo '</tr>';

    echo '<tr>';
    echo '<td class="FS14" align="left">&nbsp;'.$lang_interface['id_date_start'];
    echo '</td>';
    echo '<td class="textH FSB14" align="center">';
    if (isset($_POST['TASK_EDIT']) and ($access['ITDS']=='E')) { // Дата начала работы над задачей
    echo  '<input align="center" class="FS14" name="TASK_START" type="date" value="'.@date_my2web($taskinfo['TASK_START']).'">';    
    } else {
    echo '&nbsp;'.date_my2ru($taskinfo['TASK_START']).'&nbsp;';
    }
    echo '</td>';
    echo '<td rowspan="3" class="textH FS14" align="left">&nbsp;'.$lang_interface['id_resp_pers'].'<br><b>&nbsp;';
    if (isset($_POST['TASK_EDIT']) and ($access['ITPR']=='E')) {
        $responsable = get_respons(); //Массив ответственных
    echo '<select size="1" class="FS12" name="RESPONSIBLE">';

    if ($taskinfo['PERSON_RESPONSIBLE']==''){
        echo '<option class="FS12" selected value="">&nbsp;&nbsp;</option>';} else {echo '<option class="FS12" value="">&nbsp;&nbsp;</option>';}
        foreach ($responsable as $key => $value) {
            if ($taskinfo['PERSON_RESPONSIBLE']==$key){echo '<option class="FS12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
                echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
                echo '</select>';
    } else {
        echo get_FIO($taskinfo['PERSON_RESPONSIBLE']).'&nbsp;';
    }
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td nowrap class="FS14" align="left">&nbsp;'.$lang_interface['id_task_plan_end'];
    echo '</td>';
    echo '<td class="textH FSB14" align="center">';
    if (isset($_POST['TASK_EDIT']) and ($access['ITDP']=='E')) { // Дата планируемого завершения работы над задачей
    echo  '<input align="center" class="FS14" name="TASK_PLAN_END" type="date" value="'.@date_my2web($taskinfo['TASK_PLAN_END']).'">';    
    } else {
    echo  '&nbsp;'.date_my2ru($taskinfo['TASK_PLAN_END']).'&nbsp;';
    } 
    echo '</td>';
    echo '</tr>'; 


    echo '<tr>';
    echo '<td class="FS14" align="left">&nbsp;'.$lang_interface['id_fact_end'];
    echo '</td>';
    echo '<td class="textH FSB14" align="center">';
    if (isset($_POST['TASK_EDIT']) and ($access['ITDE']=='E')) { // Дата завершения работы над задачей
        echo  '<input align="center" class="FS14" name="TASK_END" type="date" value="'.@date_my2web($taskinfo['TASK_END']).'">';    
        } else {
        echo '&nbsp;'.date_my2ru($taskinfo['TASK_END']).'&nbsp;';
        }   
    echo '</td>';
    //echo '<td class="textH FSB14" align="center">&nbsp;'.get_FIO($taskinfo['PERSON_SET_TASK']).'&nbsp;';
    //echo '</td>';
    //echo '<td>&nbsp;';
    //echo '</td>';
//        echo '<td>&nbsp;';
//        echo '</td>';
    echo '</tr>';
    echo '</form>';
    echo '</table>';  // Табле таск



    
    echo '<table width="100%">';  // Таблица для кнопок
    echo '<tr>';

        echo '<td width="5px" align="center" valign="middle">';
        echo '<form action="it_notes.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<input type="hidden" name="SUB_TASK" value="'.$taskinfo['SUB_TASK'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/Notepad.png"  width="80px" alt="Notepad" title="'.$lang_interface['id_notes'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5px" align="center" valign="middle">';
        echo '<form action="it_reference.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/reference.png"  width="80px" alt="Reference" title="'.$lang_interface['id_reference'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center" valign="middle">';
        echo '<form action="it_3d_model.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/3D_model.png"  width="80px" alt="3D_model" title="'.$lang_interface['id_3d_models'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center" valign="middle">';
        echo '<form action="it_drawing.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/Blueprint.png"  width="80px" alt="drawing" title="'.$lang_interface['id_drow'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center" valign="middle">';
        echo '<form action="it_documentation.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/Documentation.png"  width="80px" alt="production" title="'.$lang_interface['id_doc'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '</td>';
        echo '<td width="5%" align="center" valign="middle">';
        echo '<form action="it_consider.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/consider.png"  width="80px" alt="Consider" title="'.$lang_interface['id_cons'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center" valign="middle">';
        echo '<form action="it_photography.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/Photography.png"  width="80px" alt="production" title="'.$lang_interface['id_foto'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center" valign="middle">';
        echo '<form action="it_sub_contractor.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button class = "border fly_button">';
        echo '<img src="IMG/Sub_contractor.png"  width="80px" alt="production" title="'.$lang_interface['id_order'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="90%" align="left" valign="top">';
        echo '</td>';

    echo '</tr>';
        echo '</table>'; // Таблица для кнопок

    echo '</table>';
}

//---------------------------------  Вывод подзадач ----------------------------------------------------------------------
$SQL_sub_task="SELECT task.TASK_ID, task.SUB_TASK, task_language.LANGUAGE, task.TASK_STATUS, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_SETED, task.PERSON_SET_TASK, task.PERSON_RESPONSIBLE, task.TASK_PRIORITY, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.TASK_FOLDER, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
GROUP BY task.TASK_ID, task.SUB_TASK, task_language.LANGUAGE, task.TASK_STATUS, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_SETED, task.PERSON_SET_TASK, task.PERSON_RESPONSIBLE, task.TASK_PRIORITY, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.TASK_FOLDER, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
HAVING (((task.TASK_ID)=".$_POST['TASK_ID'].") AND ((task.SUB_TASK)<>0) AND ((task_language.LANGUAGE)='".$_SESSION['LANGUAGE']."'))
ORDER BY task.SUB_TASK;";
$quer_sub_task=mysqli_query($mylink['link'], $SQL_sub_task) or die ("Ошибка TASK.<br>".mysqli_error($mylink['link']));
while ($subtaskinfo = mysqli_fetch_assoc($quer_sub_task)) {


    // ПОДЗАДАЧИ

        echo '<table border width="100%">';
        echo '<form action="it_task.php" target="TASK" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
        echo '<input type="hidden" name="SUB_TASK" value="'.$subtaskinfo['SUB_TASK'].'">';
        echo '<input type="hidden" name="TASK_EDIT">';  // Устанавливаем TASK_EDIT для того что бы после обновления опять загрузилась форма TASK_EDIT
 
        echo '<tr>';
        echo '<td rowspan="3" width="160px" align="center" valign="top">';


//---------------------- ЗАГРУЗКА ИЗОБРАЖЕНИЯ ---------------------------------------------------
$sql_get_id="SELECT Max(task_biblioteka.DOC_ID) AS Max_DOC_ID
FROM task_biblioteka
WHERE (((task_biblioteka.TASK_ID)='".$_POST['TASK_ID']."') AND ((task_biblioteka.SUB_TASK)='".$subtaskinfo['SUB_TASK']."')
 AND ((task_biblioteka.CODE_DOC_TYPE)='".$foto_cod."'));";
$query_get_id = mysqli_query($mylink['link'], $sql_get_id) or die ("Ошибка загрузки id фото <br>".mysqli_error($mylink['link']));
$assoc_doc_id=mysqli_fetch_assoc($query_get_id);
$img_doc_id=$assoc_doc_id['Max_DOC_ID'];

$sql_img_sub="SELECT task_biblioteka.FILE_TYPE, task_biblioteka.FILE_NAME, biblioteka.FILE
FROM biblioteka INNER JOIN task_biblioteka ON biblioteka.BIBLIOTEKA_ID = task_biblioteka.BIBLIOTEKA_ID
WHERE (((task_biblioteka.DOC_ID)='".$img_doc_id."') AND ((task_biblioteka.TASK_ID)='".$_POST['TASK_ID']."')
 AND ((task_biblioteka.SUB_TASK)='".$subtaskinfo['SUB_TASK']."') AND ((task_biblioteka.CODE_DOC_TYPE)='".$foto_cod."'));";
$query_img_sub = mysqli_query($mylink['link'], $sql_img_sub) or die ("Ошибка загрузки фото <br>".mysqli_error($mylink['link']));
$img_sub = mysqli_fetch_assoc($query_img_sub);
      
    if (($img_doc_id) != 0) {
                     //    echo '<a href="data:'.$img['FILE_TYPE'].';base64, '.base64_encode($img['FILE']).'" target="_blank">';
                    echo '<img src = "data:'.$img_sub['FILE_TYPE'].';base64, '.base64_encode($img_sub['FILE']).'" width = "160px" style="vertical-align: middle"';
                     //    echo '</a>'; 
                } else {                   
//        echo ' <a href="IMG/Sub_task.png" target="_blank">';
        echo '<img src="IMG/Sub_task.png" width="160px" style="vertical-align: middle"';
//        echo '</a>'; 
   }
//----------------------------------------------------------------------------------


        if (isset($_POST['TASK_EDIT']) and ($access['ITLI']=='A')) {
            echo '<br>';
            echo '<p align="center"><input type="file" name="image" multiple accept="image/*">';
//            echo '<input type="submit" value="Загрузить"></p>';
            echo '<input type="submit" name="action" value="rec_image"></p>';
        }

//'.$lang_interface['id_send'].'
        echo '</td>';
        echo '<td class="TH2"  height="10%" width="180px">&nbsp;'.($subtaskinfo['TASK_ID']).'('.($subtaskinfo['SUB_TASK']).')&nbsp;</td>';
        echo '<td class="warp Back_H3 FC_B FS16" align="left">&nbsp;'.($subtaskinfo['TASK_NAME']).'</td>';
        echo '</tr>';


if ($subtaskinfo['TASK_DESCRIPTION']!=''){
        echo '<tr>';
        echo '<td colspan="2" height="20%" class="warp Back_H4 FC_В FSB14" align="left" valign="top">&nbsp;'.($subtaskinfo['TASK_DESCRIPTION']);
        echo '</td>';
        echo '</tr>';}


        echo '<tr>';
        echo '<td colspan="2" align="left" valign="top">';

//----------------------------------- ТАБЛИЦА ДАННЫХ ПОДЗАДАЧИ ---------------------------------------
        echo '<table border width="100%">';

        echo '<tr>';


        echo '<td nowrap class="FS12" width="5%" align="left">&nbsp;'.$lang_interface['id_date_start'];
        echo '</td>';
        echo '<td nowrap class="textH FSB12" width="120px" align="center">';
        if (isset($_POST['TASK_EDIT']) and ($access['ITDS']=='E')) { // Дата начала работы над подзадачей 
        echo  '<input align="center" class="FS12" name="SUB_TASK_START" type="date" value="'.@date_my2web($subtaskinfo['TASK_START']).'">';    
        } else {
        echo '&nbsp;'.date_my2ru($subtaskinfo['TASK_START']).'&nbsp;';
        }
        echo '</td>';

// Второй столбец
        echo '<td rowspan="4" width="80%">';
        echo '&nbsp;';
        echo '</td>';

//Третий столбец
echo '<td rowspan="4" width="5%" align="center" valign="top">';
if (isset($_POST['TASK_EDIT']) and ($access['ITAS']=='A')) { // Если разрешено добавление подзадач и редактирование подзадач 
    echo '<button type="submit" name="action" value="SUB_TASK_SAVE">';
    echo '<table width="200px">';
    echo '<tr><td><img src="IMG/Sub_task_save.png"  width="50px" alt="Save"></td>';
    echo '<td>'.$lang_interface['id_save'].'</td></tr>';
    echo '</table>';
    echo '</button>';
}
    echo '</td>';

        echo '</tr>';
        echo '<tr>';

        echo '<td nowrap class="FS12" width="5%" align="left">&nbsp;'.$lang_interface['id_task_plan_end'];
        echo '</td>';
        echo '<td nowrap class="textH FSB12" width="120px" align="center">';
        if (isset($_POST['TASK_EDIT']) and ($access['ITDP']=='E')) { // Дата планируемого завершения работы над подзадачей 
        echo  '<input align="center" class="FS12" name="SUB_TASK_PLAN_END" type="date" value="'.@date_my2web($subtaskinfo['TASK_PLAN_END']).'">';    
        } else {
        echo '&nbsp;'.date_my2ru($subtaskinfo['TASK_PLAN_END']).'&nbsp;';
        }
        echo '</td>';

     //   echo '<td>&nbsp;';
     //   echo '</td>';
        echo '</tr>';
        echo '<tr>';


        echo '<td nowrap class="FS12" width="5%" align="left">&nbsp;'.$lang_interface['id_fact_end'];
        echo '</td>';
        echo '<td nowrap class="textH FSB12" width="120px" align="center">';
        if (isset($_POST['TASK_EDIT']) and ($access['ITDE']=='E')) { // Дата планируемого завершения работы над подзадачей 
        echo  '<input align="center" class="FS12" name="SUB_TASK_END" type="date" value="'.@date_my2web($subtaskinfo['TASK_END']).'">';    
        } else {
        echo '&nbsp;'.date_my2ru($subtaskinfo['TASK_END']).'&nbsp;';
        }
        echo '</td>';

    //    echo '<td>&nbsp;';
    //    echo '</td>';
        echo '</tr>';

echo '<tr>';
echo '<td nowrap class="FS12" width="5%" align="left">&nbsp;'.$lang_interface['id_task_status'];
echo '</td>';


if (isset($_POST['TASK_EDIT']) and ($access['ITTS']=='E')) {
 //   $status = get_status(); //Массив статуса
    echo '<td nowrap align="center" width="140px" class="'.status_color($subtaskinfo['TASK_STATUS']).'">';
echo '<select size="1" align="center" class="'.status_color($subtaskinfo['TASK_STATUS']).' FS12" name="TASK_STATUS">';
    foreach ($status as $key => $value) {
        if ($subtaskinfo['TASK_STATUS']==$key){echo '<option class="'.status_color($key).' FS12" align="center" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} 
        else {echo '<option class="'.status_color($key).' FS12"  align="center" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
            echo '</select>';
            echo '</td>';
} else {
    echo '<td nowrap align="center" width="140px" class="'.status_color($subtaskinfo['TASK_STATUS']).'">&nbsp;'.status_name($subtaskinfo['TASK_STATUS']).'</td>';
}

        echo '</table>';


//echo '<td>&nbsp;';
        echo '</td>';
        echo '</tr>';
        echo '</form>';
        echo '</table>';


} //КОНЕЦ WHEEL для SUBTASK



echo '</div>';
echo '</main>';
echo '</body>';
echo '</html>';


} else {
if ($access['ITAT']=='A'){  // Провкрка условия доступа INDUS TASK ADD TASK = ADD
 //==============================================================================================================================================================================       
 // Форма создания новой задачи
 echo '<div align="center">';
 echo '<form id="add_task" enctype="multipart/form-data" method="post">';
 echo '<table border width="100%">';
 
 echo '<tr>';
 echo '<td rowspan="9" width="182px"><img src="IMG/add_task.png" width="180px" style="vertical-align: middle">';
 echo '</td>';
 echo '</tr>';


 echo '<tr>';
 echo '<td class="TH1">&nbsp;'.$lang_interface['id_new_task'].'&nbsp;';
 echo '</td>';
 echo '</tr>';

 echo '<tr>';
 echo '<td nowarp class="textHBL FSB12">&nbsp;'.$lang_interface['id_task_name'].'&nbsp;';
 echo '</td>';
 echo '</tr>';

 echo '<tr>';
 echo '<td><input class="FSB14" name="task_name" id="long" value="'.@$_POST['task_name'].'">';
 echo '</td>';
 echo '</tr>';

 echo '<tr>';
 echo '<td nowarp class="textHBL FSB12">&nbsp;'.$lang_interface['id_task_deskript'].'&nbsp;';
 echo '</td>';
 echo '</tr>';

 echo '<tr>';
 echo '<td><input class="FSB14" name="task_opis" id="long" value="'.@$_POST['task_opis'].'">';
 echo '</td>';
 echo '</tr>';

echo '<tr>';
echo '<td class="textHBL">';
//----------------- ВЫБОР WORKSHOP ------------------------------------------------------------------------------------------------------------
echo '<label class="FSB12" for="WORKSHOP">&nbsp;'.$lang_interface['id_workshop'].'</label>';
?>
    <select size="1" class="FSB14" name="WORKSHOP" onChange="document.getElementById('add_task').submit();">
<?php
$workshop = get_workshop(); //Массив цехов
if (isset($_POST['WORKSHOP'])){
    if ($_POST['WORKSHOP']=='ALL'){echo '<option class="FSB14" selected value="ALL">&nbsp;'.$lang_interface['id_work_all'].'&nbsp;</option>';} else
    {echo '<option class="FSB14" value="ALL">&nbsp;'.$lang_interface['id_work_all'].'&nbsp;</option>';}

    foreach ($workshop as $key => $value) {
        if ($_POST['WORKSHOP']==$key){echo '<option class="FSB14" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
            echo '<option class="FSB14" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
} else {
    echo '<option class="FSB14" selected value="ALL">&nbsp;'.$lang_interface['id_work_all'].'&nbsp;</option>';
    foreach ($workshop as $key => $value) {echo '<option class="FSB14" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
    $_POST['WORKSHOP']='ALL';
}
echo '</select>';
//------------------------------------------------------------------------------------------------------------------------------------------------


//------- ВЫБОР AREA -----------------------------------------------------------------------------------------------------------------------------
echo '&nbsp;<label class="FSB12" for="AREA">&nbsp;&nbsp;'.$lang_interface['id_area'].'&nbsp;</label>';
//'.$lang_interface['id_area'].'
?>
    <select size="1" class="FSB14" name="AREA" onChange="document.getElementById('add_task').submit();">
<?php
$workshop_area = get_workshop_area(); //Массив рабочих зон
if (isset($_POST['AREA'])){
    if ($_POST['AREA']=='ALL'){echo '<option class="FSB14" selected value="ALL">&nbsp;'.$lang_interface['id_area_all'].'&nbsp;</option>';} else
    {echo '<option class="FSB14" value="ALL">&nbsp;'.$lang_interface['id_area_all'].'&nbsp;</option>';}

    foreach ($workshop_area as $key => $value) {
        if ($_POST['AREA']==$key){echo '<option class="FSB14" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
            echo '<option class="FSB14" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
}else{
    echo '<option class="FSB14" selected value="ALL">&nbsp;'.$lang_interface['id_area_all'].'&nbsp;</option>';
    foreach ($workshop_area as $key => $value) {echo '<option class="FSB14" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
    $_POST['AREA']='ALL';
}
echo '</select>';
//------------------------------------------------------------------------------------------------------------------------------------------------
echo '</td>';
echo '</tr>';


echo '<tr>';
if (isset($_POST['PRIORITY'])){echo '<td align="left" class="'.prior_color($_POST['PRIORITY']).'">'; } else {echo '<td>&nbsp;';}
//------- ВЫБОР ПРИОРИТЕТА -----------------------------------------------------------------------------------------------------------------------------
    echo '<label class="FSB12" for="PRIORITY">&nbsp;'.$lang_interface['id_priority'].'&nbsp;</label>';
    ?>
    <select size="1" class="FSB12" name="PRIORITY" onChange="document.getElementById('add_task').submit();">
    <?php
    $workshop_area = get_prior($cur_date); //Приоритеты
    if (isset($_POST['PRIORITY'])){
        foreach ($workshop_area as $key => $value) {
            if ($_POST['PRIORITY']==$key){echo '<option class="FSB12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
                echo '<option class="FSB12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
    }else{
        foreach ($workshop_area as $key => $value) {
            IF ($key=='P1'){echo '<option class="FSB12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}
            else {echo '<option class="FSB12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
            $_POST['PRIORITY']='P1';
    }}
    echo '</select>';
//------------------------------------------------------------------------------------------------------------------------------------------------
echo '&nbsp;<a href="priority.php" target="_blank"><img src="IMG/info.png" width="28px" style="vertical-align: middle"></a>';

    echo '</td>';
echo '</tr>';

echo '</table>';
echo '<br>';

    echo '<p><div align="center"><button type="submit" value="NEW_REC" name="action">&nbsp;'.$lang_interface['id_rec_new_task'].'&nbsp;</button>';
if ((isset($_POST['WORKSHOP'])) or (isset($_POST['AREA']))) {
} else {
    echo ' <button type="reset" class="button">&nbsp;'.$lang_interface['id_cleen'].'&nbsp;</button></div></p>';
    echo '<input type="button" value="Закрыть" onclick="self.close()">';}
echo  '</form>';
} else {echo '<BR><BR><BR>';
echo '<div align="center">';
echo '<div class="msgw600 warning shadow cirkle FSB20" align="center">'.$lang_interface['id_access_no_new'].'</div>';
    echo '<BR><BR>';
    echo '<input type="button" value="'.$lang_interface['id_close'].'" onclick="self.close()">';}



echo '</div>';
echo '</main>';
echo '</body>';
echo '</html>';
}
?>