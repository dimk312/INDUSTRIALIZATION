<?php
// ДЕЙСТВИЯ
// VIEW, EDIT, NEW_TASK
require ("../../inc/base_con.inc");
require ("../../inc/date.inc");
require ("../../inc/translate.inc");
require ("../../login/access.inc");
require ("../../DICTION/PERSON/person.inc");
//require ("it_task.inc");
require ("it_notes_up.inc");
session_start();
global  $mylink;
$mylink=con_my();
// $mylink['base'];
// $mylink['host'];
$SCRIPT=basename($_SERVER['SCRIPT_NAME']);
$cur_date = date('Y-m-d H:i:s');

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
$otladchik='dimk312';
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
$access=ACCESS();

echo '<!DOCTYPE html>';
echo '<html lang="'.$html_lang.'">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>NOTE</title>';
echo '<link rel="stylesheet" href="../../css/'.$_SESSION['COLOR_SCHEME'].'.css"  type="text/css"/>';
echo '</head>';
echo '<body>';
echo '<main>';

//-------------------------------------------------------------------------------------------------------------------
if (($_SESSION['logged_user']) == $otladchik) {
    echo '<br>';
    echo '<br> $SCRIPT = ' . $SCRIPT;
    echo '<br> $cur_date = '.$cur_date;
    print "<pre>";
    echo '<br> $_POST<br>';
    print_r($_POST);
    echo '<br> $_SESSION<br>';
    print_r($_SESSION);
    echo '<br>  $_FILES<br>';
    print_r($_FILES);
    print "</pre>";
}
//-------------------------------------------------------------------------------------------------------------------
if (isset($_POST['SAVE_NOTE'])){     // Записываем новую заметку

        if (($_POST['SUBJECT'])=='' and ($_POST['NOTE'])=='') {$errors_save[] = $lang_interface['id_nosub_notext'];} else {
            if (($_POST['NOTE'])=='') {
                $errors_save[] = $lang_interface['id_notext'];
            }
        }

        if (empty($errors_save)) {
    $NEW_SUBJECT = mysqli_real_escape_string($mylink['link'], $_POST['SUBJECT']);  //Экранируем спецсимволы
    $NEW_NOTE = mysqli_real_escape_string($mylink['link'], $_POST['NOTE']);  //Экранируем спецсимволы
    $PERSON_ID=get_pfu($_SESSION['logged_user']);
    $sql_ins="INSERT INTO enterprise.task_note (TASK_ID, SUB_TASK, LANGUAGE, LANG_STATUS, SUBJECT, NOTE, PERSON_ID, DATE_NOTE,  USER_M)
     VALUES ('".$_POST['TASK_ID']."', '".$_POST['SUB_TASK']."', '".$_SESSION['LANGUAGE']."', 'U', '".$NEW_SUBJECT."', '".$NEW_NOTE."', '". $PERSON_ID."', '".$cur_date."',  '".$_SESSION['logged_user']."');";
    mysqli_query($mylink['link'], $sql_ins) or die ("Ошибка новой заметки.<br>".mysqli_error($mylink['link']));
    update_notes_language($_POST['TASK_ID'], $_POST['SUB_TASK'], $cur_date);
        } else {
                        // Выводим ошибки и снова форма добавления задачи
            foreach ($errors_save as $key => $value) {echo '<div class="message warning msgw600 FSB20 center shadow cirkle" align="center">' . $value . '</div><br>';}
        }
}
 //==============================================================================================================================================================================      
 
     if (isset($_POST['SAVE_EDIT'])){     // Записываем изменённую заметку
        if (($_POST['SUBJECT'])=='' and ($_POST['NOTE'])=='') {$errors_save[] = $lang_interface['id_nosub_notext'];} else {
            if (($_POST['NOTE'])=='') {
                $errors_save[] = $lang_interface['id_notext'];
            }
        }

        if (empty($errors_save)) {
    $NEW_SUBJECT = mysqli_real_escape_string($mylink['link'], $_POST['SUBJECT']);  //Экранируем спецсимволы
    $NEW_NOTE = mysqli_real_escape_string($mylink['link'], $_POST['NOTE']);  //Экранируем спецсимволы
    $sql_up="UPDATE enterprise.task_note SET SUBJECT = '".$NEW_SUBJECT."', `NOTE` = '".$NEW_NOTE."'
    WHERE (TASK_ID = '".$_POST['TASK_ID']."') and (SUB_TASK = '".$_POST['SUB_TASK']."') and (LANGUAGE = '".$_SESSION['LANGUAGE']."') and (DATE_NOTE = '".$_POST['DATE_NOTE']."');";
    mysqli_query($mylink['link'], $sql_up) or die ("Ошибка обновления заметки.<br>".mysqli_error($mylink['link']));
    change_records_note($_POST['TASK_ID'], $_POST['SUB_TASK'], $_POST['DATE_NOTE'], $_SESSION['LANGUAGE'], $_POST['SUBJECT'], $_POST['NOTE']);
        } else {
                        // Выводим ошибки и снова форма добавления задачи
            foreach ($errors_save as $key => $value) {echo '<div class="message warning msgw600 FSB20 center shadow cirkle" align="center">' . $value . '</div><br>';}
        }
}     
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
if (isset($_POST['TASK_ID'])) {  // Извлечение названия задачи

$SQL_task_name="SELECT task.TASK_ID, task.SUB_TASK, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task_language.LANGUAGE
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
GROUP BY task.TASK_ID, task.SUB_TASK, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task_language.LANGUAGE
HAVING (((task.TASK_ID)=".$_POST['TASK_ID'].") AND ((task.SUB_TASK)=0) AND ((task_language.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
$quer_task_name=mysqli_query($mylink['link'], $SQL_task_name) or die ("Ошибка TASK NAME<br>".mysqli_error($mylink['link']));
$taskinfo = mysqli_fetch_assoc($quer_task_name);
}
 //==============================================================================================================================================================================           
echo '<div align="center">';
echo '<table border width="100%">';

if ($access['TNEL']== 'E') {
    echo '<tr>';
    echo '<td  class="TH1" width="5%" align="center" valign="middle"><img src="IMG/NOTE.png" height="100px" style="vertical-align: middle"</td>';
    echo '<td colspan=4 class="TH1" valign="middle">&nbsp;'.$lang_interface['id_taskN'].'&nbsp;'.$taskinfo['TASK_ID'].'&nbsp'.$taskinfo['TASK_NAME'].'&nbsp;';
    echo '<td  class="TH1" width="1%" align="center" valign="middle">';
    echo '<form action="it_notes_lang.php" id="notes" value="EDIT_LANG" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
    //echo '<input type="hidden" name="SUB_TASK" value="'.$note['SUB_TASK'].'">';  
    //echo '<input type="hidden" name="DATE_NOTE" value="'.$note['DATE_NOTE'].'">'; 
    echo '<button name="EDIT_LANG"><img src="../../IMAGES/lang.png" height="100px" alt="Save" style="vertical-align: middle" title="'.$lang_interface['id_lang_edit'].'"></button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
} else {
    echo '<tr>';
    echo '<td  class="TH1" width="5%" align="center" valign="middle"><img src="IMG/NOTE.png" height="100px" style="vertical-align: middle"</td>';
    echo '<td colspan=5 class="TH1" valign="middle">&nbsp;'.$lang_interface['id_taskN'].'&nbsp;'.$taskinfo['TASK_ID'].'&nbsp'.$taskinfo['TASK_NAME'].'&nbsp;';
    echo '</tr>';
} 


echo '<tr>';
echo '<th class="textHB" width="5%" class="textH FSB14" align="center" >&nbsp;'.$lang_interface['id_datatime'].'&nbsp';
echo '</th>';
echo '<th class="textHB" width="4%" class="textH FSB14" align="center" >&nbsp;'.$lang_interface['id_step'].'&nbsp';
echo '</th>';
echo '<th colspan=2 class="textHB" class="textH FSB14" align="center">&nbsp;'.$lang_interface['id_prim'].'&nbsp';
echo '</th>';
echo '<th colspan=2 class="textHB" width="220px" class="textH FSB14" align="center">&nbsp;'.$lang_interface['id_user'].'&nbsp';
echo '</th>';
echo '</tr>';

echo '<form id="add_task" enctype="multipart/form-data" method="post">';

$sql_note="SELECT task_note.TASK_ID, task_note.SUB_TASK, task_note.DATE_NOTE, task_note.SUBJECT, task_note.PERSON_ID, task_note.NOTE
FROM task_note
WHERE (((task_note.LANGUAGE)='".$_SESSION['LANGUAGE']."'))
GROUP BY task_note.TASK_ID, task_note.SUB_TASK, task_note.DATE_NOTE, task_note.SUBJECT, task_note.NOTE, task_note.PERSON_ID
HAVING (((task_note.TASK_ID)=".$_POST['TASK_ID']."))
ORDER BY task_note.DATE_NOTE, task_note.SUB_TASK;";
$quer_note=mysqli_query($mylink['link'], $sql_note) or die ("Ошибка NOTE.<br>".mysqli_error($mylink['link']));
while ($note = mysqli_fetch_assoc($quer_note)) { // Запуск перебора заметок

    if ((isset($_POST['EDIT_NOTE'])) and ($_POST['TASK_ID'] == $note['TASK_ID']) and  ($_POST['SUB_TASK'] == $note['SUB_TASK']) and ($_POST['DATE_NOTE'] == $note['DATE_NOTE']))
    {   // ---- ВЫВОДИТСЯ ФОРМА ДЛЯ ИЗМЕНЕНИЯ ЗАПИСЕЙ ----------------------------------------
    echo '<form id="notes" value="SAVE_EDIT" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$note['TASK_ID'].'">';
    echo '<input type="hidden" name="SUB_TASK" value="'.$note['SUB_TASK'].'">';  
    echo '<input type="hidden" name="DATE_NOTE" value="'.$note['DATE_NOTE'].'">';    
//    echo '<input type="hidden" name="SUB_TASK" value="'.$note['SUBJECT'].'">';  
//    echo '<input type="hidden" name="DATE_NOTE" value="'.$note['NOTE'].'">'; 
    echo '<tr>';
    echo '<td class="FS14" valign="top" align="center">';
    echo '&nbsp;'.date_time_my2ru($note['DATE_NOTE']).'&nbsp;';
    echo '</td>';
    echo '<td class="FS14" valign="top" align="center">';
    echo '&nbsp;'.$note['SUB_TASK'].'&nbsp;';
    echo '</td>';
    echo '<td colspan=2 class="FS14" align="left" valign="top">';
    echo '<input type="text" name="SUBJECT" id="long" value="'.$note['SUBJECT'].'">';
    echo '<input type="text" name="NOTE" id="long" value="'.$note['NOTE'].'">';
    echo '</td>';
    echo '<td width="180px" nowarp class="FS14" valign="top" align="center">';
    echo '&nbsp;'.get_FIO($note['PERSON_ID']).'&nbsp;';
    echo '</td>';
    echo '<td width="1%" nowarp class="FS14" valign="top" align="center">';
    echo '<button name="SAVE_EDIT"><img src="IMG/SAVE_edit.png" height="36px" alt="Save" style="vertical-align: middle" title="'.$lang_interface['id_save_mod'].'"></button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';     
    } else {    // ------- ОТОБРАЖЕНИЕ ЗАПИСИ ИЗ БАЗЫ ------------------------------------------
        echo '<form id="notes" value="VIEW_NOTE" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$note['TASK_ID'].'">';
        echo '<input type="hidden" name="SUB_TASK" value="'.$note['SUB_TASK'].'">';
        echo '<input type="hidden" name="DATE_NOTE" value="'.$note['DATE_NOTE'].'">';
        echo '<tr>';
        echo '<td class="FS14" valign="top" align="center">';
        echo '&nbsp;'.date_time_my2ru($note['DATE_NOTE']).'&nbsp;';
        echo '</td>';
        echo '<td class="FS14" valign="top" align="center">';
        echo '&nbsp;'.$note['SUB_TASK'].'&nbsp;';
        echo '</td>';
        echo '<td colspan=2 class="FS14" align="left" valign="top">';
        if (($note['SUBJECT'])!=''){echo '<b>&nbsp;&nbsp;'.$note['SUBJECT'].'&nbsp;</b><br>';}
        echo '&nbsp; '.$note['NOTE'];
        echo '</td>';
        echo '<td width="180px" nowarp class="FS14" valign="top" align="center">';
        echo '&nbsp;'.get_FIO($note['PERSON_ID']).'&nbsp;';
        echo '</td>';
        echo '<td width="1%" nowarp class="FS14" valign="top" align="center">';
        echo '<button name="EDIT_NOTE"><img src="IMG/NOTE_edit.png" height="36px" alt="Save" style="vertical-align: middle" title="'.$lang_interface['id_edit_prim'].'"></button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    } 
}



//--- ВЫВОД ФОРМЫ ДЛЯ НОВОЙ ЗАПИСИ -----------------------------------------------------

if (empty($errors_save)) {   //Если нет ошибок вывод обычной пустой формы
    echo '<form id="notes" value="SAVE_NOTE" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
    echo '<input type="hidden" name="SUB_TASK" value="'.$_POST['SUB_TASK'].'">';
    echo '<tr>';
    echo '<td class="FS14" valign="top" align="center">';
    echo '&nbsp;';
    echo '</td>';
    echo '<td class="FS14" valign="top" align="center">';
    echo '&nbsp;';
    echo '</td>';
    echo '<td  colspan=2 class="FS14" align="left" valign="top">';
    echo '<input type="text" name="SUBJECT" id="long" placeholder=" '.$lang_interface['id_note_sub'].'">';
    echo '<input type="text" name="NOTE" id="long" placeholder=" '.$lang_interface['id_note_text'].'">';
    echo '</td>';
    echo '<td colspan=2 class="FS14" valign="top" align="center">';
    echo '<button name="SAVE_NOTE"><img src="IMG/NOTE_add.png" height="36px" alt="Save" style="vertical-align: middle" title="'.$lang_interface['id_save_new_prim'].'"></button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
} else {   //Если не заполнено сообщение вывод для доп редактирования
    echo '<form id="notes" value="SAVE_NOTE" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$_POST['TASK_ID'].'">';
    echo '<input type="hidden" name="SUB_TASK" value="'.$_POST['SUB_TASK'].'">';
    echo '<tr>';
    echo '<td class="FS14" valign="top" align="center">';
    echo '&nbsp;';
    echo '</td>';
    echo '<td class="FS14" valign="top" align="center">';
    echo '&nbsp;';
    echo '</td>';
    echo '<td colspan=2 class="FS14" align="left" valign="top">';
    echo '<input type="text" name="SUBJECT" id="long" value="'.$_POST['SUBJECT'].'">';
    echo '<input type="text" name="NOTE" id="long" value="'.$_POST['NOTE'].'">';
    echo '</td>';
    echo '<td colspan=2 class="FS14" valign="top" align="center">';
    echo '<button name="SAVE_NOTE"><img src="IMG/NOTE_add.png" height="36px" alt="Save" style="vertical-align: middle" title="'.$lang_interface['id_save_new_prim'].'"></button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';  
}

echo '</table>';
echo  '</form>';
echo '</div>';
echo '<br>';


echo '<br>';

echo '<p align="center">';
echo '<input type="button" value="'.$lang_interface['id_close'].'" onclick="self.close()">';

echo '</main>';
echo '</body>';
echo '</html>';

?>