<?php
// ДЕЙСТВИЯ
// VIEW, EDIT, NEW_TASK
require ("../../inc/base_con.inc");
//require ("../../inc/date.inc");
require ("../../inc/translate.inc");
require ("../../login/access.inc");
require ("../../DICTION/PERSON/person.inc");
//require ("it_task.inc");
session_start();
global  $mylink;
global  $access;
$mylink=con_my();
// $mylink['base'];
// $mylink['host'];
$SCRIPT=basename($_SERVER['SCRIPT_NAME']);
$cur_date = date('Y-m-d H:i:s');
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





echo '</main>';
echo '</body>';
echo '</html>';
?>