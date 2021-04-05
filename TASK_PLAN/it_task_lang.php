<?php
// ДЕЙСТВИЯ
// VIEW, EDIT, NEW_TASK
require ("../../inc/base_con.inc");
require ("../../inc/date.inc");
require ("../../inc/translate.inc");
require ("../../login/access.inc");
require ("../../DICTION/PERSON/person.inc");
require ("it_task.inc");
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
echo '<title>NOTE LANGUAGE</title>';
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

//------------ Запись изменения -----------------------------------------------------
if (isset($_POST['SAVE_EDIT'])){
$sql_up="UPDATE enterprise.task_note SET LANG_STATUS = 'U',  SUBJECT = '".$_POST['SUBJECT']."', NOTE = '".$_POST['NOTE']."', USER_M = '".$_SESSION['logged_user']."' 
WHERE (`TASK_ID` = '".$_POST['TASK_ID']."') and (`SUB_TASK` = '".$_POST['SUB_TASK']."') and (`LANGUAGE` = '".$_POST['LANGUAGE']."') and (`DATE_NOTE` = '".$_POST['DATE_NOTE']."');";
if (($_SESSION['logged_user']) == $otladchik) {echo '<br> sql_up = '.$sql_up.'<br>';};
mysqli_query($mylink['link'], $sql_up) or die ("Ошибка обновления записи<br>".mysqli_error($mylink['link']));
}

//-------------------------------------------------------------------------------------------------------------------
// Извлечение названия задачи
$SQL_task_name="SELECT task.TASK_ID, task.SUB_TASK, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task_language.LANGUAGE
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
GROUP BY task.TASK_ID, task.SUB_TASK, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task_language.LANGUAGE
HAVING (((task.TASK_ID)=".$_POST['TASK_ID'].") AND ((task.SUB_TASK)=0) AND ((task_language.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
$quer_task_name=mysqli_query($mylink['link'], $SQL_task_name) or die ("Ошибка TASK NAME<br>".mysqli_error($mylink['link']));
$taskinfo = mysqli_fetch_assoc($quer_task_name);

//==============================================================================================================================================================================           
echo '<div align="center">';
echo '<form id="add_task" enctype="multipart/form-data" method="post">';
echo '<table border width="100%">';

echo '<tr>';
echo '<td  class="TH1" width="5%" align="center" valign="middle"><img src="IMG/NOTE.png" height="100px" style="vertical-align: middle"</td>';
echo '<td colspan=4 class="TH1" valign="middle">&nbsp;Задача №&nbsp;'.$taskinfo['TASK_ID'].'&nbsp'.$taskinfo['TASK_NAME'].'&nbsp;';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td colspan=5 class="TH3" valign="middle">&nbsp;Коррекция авторереводов.&nbsp;';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<th class="textHB" width="5%" class="textH FSB14" align="center" >&nbsp;Дата время&nbsp';
echo '</th>';
echo '<th class="textHB" width="4%" class="textH FSB14" align="center" >&nbsp;Этап&nbsp';
echo '</th>';
echo '<th class="textHB" width="4%" class="textH FSB14" align="center" >&nbsp;Язык&nbsp';
echo '</th>';
echo '<th colspan=2 class="textHB" class="textH FSB14" align="center">&nbsp;Примечание&nbsp';
echo '</th>';
echo '</tr>';


$sql_note="SELECT task_note.TASK_ID, task_note.SUB_TASK, task_note.DATE_NOTE, task_note.LANGUAGE, task_note.LANG_STATUS, task_note.SUBJECT, task_note.PERSON_ID, task_note.NOTE
FROM task_note
WHERE ((task_note.TASK_ID)='".$_POST['TASK_ID']."')
ORDER BY task_note.SUB_TASK, task_note.LANG_STATUS  DESC, task_note.DATE_NOTE;";
$quer_note=mysqli_query($mylink['link'], $sql_note) or die ("Ошибка NOTE.<br>".mysqli_error($mylink['link']));
while ($note = mysqli_fetch_assoc($quer_note)) { // Запуск перебора заметок
if (($note['LANG_STATUS'])=='U'){$celcolor='textHG FS14';} else {$celcolor='FS14';}
    // ---- ВЫВОДИТСЯ ФОРМА ДЛЯ ИЗМЕНЕНИЯ ЗАПИСЕЙ ----------------------------------------
    echo '<form id="notes" value="SAVE_EDIT" enctype="multipart/form-data" method="post">';
    echo '<input type="hidden" name="TASK_ID" value="'.$note['TASK_ID'].'">';
    echo '<input type="hidden" name="SUB_TASK" value="'.$note['SUB_TASK'].'">';  
    echo '<input type="hidden" name="DATE_NOTE" value="'.$note['DATE_NOTE'].'">';  
    echo '<input type="hidden" name="LANGUAGE" value="'.$note['LANGUAGE'].'">';  
    echo '<tr>';
    echo '<td class="'.$celcolor.'" valign="top" align="center">';
    echo '&nbsp;'.date_time_my2ru($note['DATE_NOTE']).'&nbsp;';
    echo '</td>';
    echo '<td class="'.$celcolor.'" valign="top" align="center">';
    echo '&nbsp;'.$note['SUB_TASK'].'&nbsp;';
    echo '</td>';
    echo '<td class="'.$celcolor.'" valign="top" align="center">';
    echo '&nbsp;'.$note['LANGUAGE'].'&nbsp;';
    echo '</td>';
    echo '<td class="'.$celcolor.'" align="left" valign="top">';
    echo '<input type="text" name="SUBJECT" id="long" value="'.$note['SUBJECT'].'">';
    echo '<input type="text" name="NOTE" id="long" value="'.$note['NOTE'].'">';
    echo '</td>';
    echo '<td width="40px" nowarp class="'.$celcolor.'" valign="top" align="center">';
    echo '<button name="SAVE_EDIT"><img src="IMG/SAVE_edit.png" height="36px" alt="Save" style="vertical-align: middle" title="Сохранить измененимя"></button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';     
}




echo '</table>';
echo  '</form>';
echo '</div>';
echo '<br>';



echo '<p align="center">';
echo '<input type="button" value="Закрыть" onclick="self.close()">';

echo '</main>';
echo '</body>';
echo '</html>';

?>