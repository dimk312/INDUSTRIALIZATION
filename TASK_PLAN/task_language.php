<?php
require ("../../inc/base_con.inc");
require ("../../inc/translate.inc");
session_start();
global  $mylink, $link;
$mylink=con_my();
// $mylink['base'];
// $mylink['host'];
$link=$mylink['link'];
//$user=$_SESSION['logged_user'];
//$data = $_POST;
// print "<pre>";
// print_r($data);
 //-----------------------------------------------------------------------------------------------


echo '<!DOCTYPE html>
<html lang="ru">
<head>
	 <meta charset="utf-8">
	 <title>user info</title>
     <link rel="stylesheet" href="../../css/style_pdm.css"  type="text/css">
</head>';

 
echo '<body>'; 
echo '<form id="form_user" name="user" method="post">';	

echo '<div align="center">';
echo '<div class="message FSB22 info shadow cirkle center msgw600">ОБРАБОТКА ЯЗЫКОВЫХ ПОЛЕЙ<BR>ТАБЛИЦ TASK</div>';
echo '</div>';
echo '<br>';
echo '<br>';
echo '<div align="center">';

echo '<table border align="center">';

echo '<tr>';
echo '<th class="thg">TASK_ID</th>';
echo '<th class="thg">SUB_TASK</th>';
echo '<th class="thg">LANGUAGE</th>';
echo '<th class="thg">TASK_NAME</th>';
echo '<th class="thg">TASK_DESCRIPTION</th>';
echo '</tr>';



// ----- Ключи для добавления языков  TASK_ID, SUB_TASK  --------------------------------------------------------------------------------------------------------------------
$sql_count_lang="SELECT Count(dc_language_work.LANGUAGE) AS Count
FROM dc_language_work;";
$count_lang=mysqli_fetch_assoc(mysqli_query($mylink['link'], $sql_count_lang));

$sql_get_key="SELECT task_language.TASK_ID, task_language.SUB_TASK
FROM task_language
GROUP BY task_language.TASK_ID, task_language.SUB_TASK
HAVING (((Count(task_language.LANGUAGE))<".$count_lang['Count']."));";
$quer_get_key = mysqli_query($mylink['link'], $sql_get_key) or die ("Ошибка загрузки ключевых полей LIST TASK <br>".mysqli_error($mylink['link']));
while ($id = mysqli_fetch_assoc($quer_get_key)){




//----- ЯЗЫКОВОЕ ОБНОВЛЕНИЕ ДЛЯ  $id['TASK_ID'] и $id['SUB_TASK'] --------
//-------------Загрузка источника ------------------------------------
$sql_from="SELECT task_language.TASK_ID, task_language.SUB_TASK, task_language.LANGUAGE, task_language.LANG_STATUS, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task_language
WHERE (((task_language.TASK_ID)=".$id['TASK_ID'].") AND ((task_language.SUB_TASK)=".$id['SUB_TASK'].") AND ((task_language.LANG_STATUS)='U'));";
$quer_from = mysqli_query($mylink['link'], $sql_from) or die ("Ошибка загрузки источника для перевода <br>".mysqli_error($mylink['link']));
$from = mysqli_fetch_assoc($quer_from);

if ($from['LANGUAGE']=='RUS'){$lang_from='ru';}
if ($from['LANGUAGE']=='UKR'){$lang_from='uk';}
if ($from['LANGUAGE']=='ENG'){$lang_from='en';}
if ($from['LANGUAGE']=='FRA'){$lang_from='fr';}

echo '<tr>';
echo '<td class="textHG" align="left">'.$id['TASK_ID'].'</td>';
echo '<td class="textHG" align="left">'.$id['SUB_TASK'].'</td>';
echo '<td class="textHG" align="left">'.$from['LANGUAGE'].'</td>';
echo '<td class="textHG" align="left">'.$from['TASK_NAME'].'</td>';
echo '<td class="textHG" align="left">'.$from['TASK_DESCRIPTION'].'</td>';
echo '</tr>';

//=====================================================================

//------- Выбор языка на который надо перевести ДОБАВИВ ЗАПИСЬ --------------------------------

$sql_get_lang="SELECT dc_language_work.LANGUAGE
FROM (SELECT task_language.LANGUAGE
FROM task_language
WHERE (((task_language.TASK_ID)=".$id['TASK_ID'].") AND ((task_language.SUB_TASK)=".$id['SUB_TASK']."))) as E_lang RIGHT JOIN dc_language_work ON E_lang.LANGUAGE = dc_language_work.LANGUAGE
WHERE (((E_lang.LANGUAGE) Is Null));";
$quer_get_lang = mysqli_query($mylink['link'], $sql_get_lang) or die ("Ошибка загрузки списка добавляемых языков для задачи- ".$id['TASK_ID']." и ID - ".$id['SUB_TASK']." <br>".mysqli_error($mylink['link']));
while ($lang = mysqli_fetch_assoc($quer_get_lang)){

//------------------ $lang_to ------------------------------------------
if ($lang['LANGUAGE']=='RUS'){$lang_to='ru';}
if ($lang['LANGUAGE']=='UKR'){$lang_to='uk';}
if ($lang['LANGUAGE']=='ENG'){$lang_to='en';}
if ($lang['LANGUAGE']=='FRA'){$lang_to='fr';}



$NEW_task=mysqli_real_escape_string($mylink['link'], gtranslate($from['TASK_NAME'], $lang_from, $lang_to));
$NEW_description=mysqli_real_escape_string($mylink['link'], gtranslate($from['TASK_DESCRIPTION'], $lang_from, $lang_to));


echo '<tr>';
echo '<td class="backO_textB" align="left">'.$id['TASK_ID'].'</td>';
echo '<td class="backO_textB" align="left">'.$id['SUB_TASK'].'</td>';
echo '<td class="backO_textB" align="left">'.$lang['LANGUAGE'].'</td>';
echo '<td class="backO_textB" align="left">'.$NEW_task.'</td>';
echo '<td class="backO_textB" align="left">'.$NEW_description.'</td>';
echo '</tr>';


    $sql_up="INSERT INTO enterprise.task_language (TASK_ID, SUB_TASK, LANGUAGE, LANG_STATUS, TASK_NAME, TASK_DESCRIPTION)
 VALUES ('".$id['TASK_ID']."', '".$id['SUB_TASK']."', '".$lang['LANGUAGE']."', 'A', '".$NEW_task."', '".$NEW_description."');";
mysqli_query($mylink['link'], $sql_up) or die ("Ошибка добавление  - ".$id['TASK_ID'].", ID - ".$id['SUB_TASK']." и языка -".$lang['LANGUAGE']." <br>".mysqli_error($mylink['link']));


} // Перебор новых языков
}  // Перебор ID
echo '</table>';

echo '<br>';
echo '<br>';
// -----------------------------------  ОБНОВЛЕНИЕ ЗАМЕТОК -------------------------------------------------------------------

echo '<table border align="center">';
echo '<tr>';
echo '<th class="thg">TASK_ID</th>';
echo '<th class="thg">SUB_TASK</th>';
echo '<th class="thg">LANGUAGE</th>';
echo '<th class="thg">NOTE</th>';
echo '<th class="thg">DATE_NOTE</th>';
echo '</tr>';


// ----- Ключи для добавления языков  TASK_ID, SUB_TASK  --------------------------------------------------------------------------------------------------------------------

$sql_get_key="SELECT task_note.TASK_ID, task_note.SUB_TASK
FROM task_note
GROUP BY task_note.TASK_ID, task_note.SUB_TASK
HAVING (((Count(task_note.LANGUAGE))<".$count_lang['Count']."));";
$quer_get_key = mysqli_query($mylink['link'], $sql_get_key) or die ("Ошибка загрузки ключевых полей TASK NOTE <br>".mysqli_error($mylink['link']));
while ($id = mysqli_fetch_assoc($quer_get_key)){


//----- ЯЗЫКОВОЕ ОБНОВЛЕНИЕ ДЛЯ  $id['TASK_ID'] и $id['SUB_TASK'] --------
//-------------Загрузка источника ------------------------------------
    $sql_from="SELECT task_note.TASK_ID, task_note.SUB_TASK, task_note.LANGUAGE, task_note.LANG_STATUS, task_note.NOTE, task_note.DATE_NOTE
FROM task_note
WHERE (((task_note.TASK_ID)=".$id['TASK_ID'].") AND ((task_note.SUB_TASK)=".$id['SUB_TASK'].") AND ((task_note.LANG_STATUS)='U'));";
    $quer_from = mysqli_query($mylink['link'], $sql_from) or die ("Ошибка загрузки источника для перевода TASK NOTE<br>".mysqli_error($mylink['link']));
    $from = mysqli_fetch_assoc($quer_from);

    if ($from['LANGUAGE']=='RUS'){$lang_from='ru';}
    if ($from['LANGUAGE']=='UKR'){$lang_from='uk';}
    if ($from['LANGUAGE']=='ENG'){$lang_from='en';}
    if ($from['LANGUAGE']=='FRA'){$lang_from='fr';}

    echo '<tr>';
    echo '<td class="textHG" align="left">'.$id['TASK_ID'].'</td>';
    echo '<td class="textHG" align="left">'.$id['SUB_TASK'].'</td>';
    echo '<td class="textHG" align="left">'.$from['LANGUAGE'].'</td>';
    echo '<td class="textHG" align="left">'.$from['NOTE'].'</td>';
    echo '<td class="textHG" align="left">'.$from['DATE_NOTE'].'</td>';
    echo '</tr>';

//=====================================================================
//------- Выбор языка на который надо перевести ДОБАВИВ ЗАПИСЬ --------------------------------

    $sql_get_lang="SELECT dc_language_work.LANGUAGE
FROM (SELECT task_note.LANGUAGE
FROM task_note
WHERE (((task_note.TASK_ID)=".$id['TASK_ID'].") AND ((task_note.SUB_TASK)=".$id['SUB_TASK']."))) as E_lang RIGHT JOIN dc_language_work ON E_lang.LANGUAGE = dc_language_work.LANGUAGE
WHERE (((E_lang.LANGUAGE) Is Null));";
    $quer_get_lang = mysqli_query($mylink['link'], $sql_get_lang) or die ("Ошибка загрузки списка добавляемых языков для задачи- ".$id['TASK_ID']." и ID - ".$id['SUB_TASK']." <br>".mysqli_error($mylink['link']));
    while ($lang = mysqli_fetch_assoc($quer_get_lang)){

//------------------ $lang_to ------------------------------------------
        if ($lang['LANGUAGE']=='RUS'){$lang_to='ru';}
        if ($lang['LANGUAGE']=='UKR'){$lang_to='uk';}
        if ($lang['LANGUAGE']=='ENG'){$lang_to='en';}
        if ($lang['LANGUAGE']=='FRA'){$lang_to='fr';}



        $NEW_note=mysqli_real_escape_string($mylink['link'], gtranslate($from['NOTE'], $lang_from, $lang_to));


        echo '<tr>';
        echo '<td class="backO_textB" align="left">'.$id['TASK_ID'].'</td>';
        echo '<td class="backO_textB" align="left">'.$id['SUB_TASK'].'</td>';
        echo '<td class="backO_textB" align="left">'.$lang['LANGUAGE'].'</td>';
        echo '<td class="backO_textB" align="left">'.$NEW_note.'</td>';
        echo '<td class="backO_textB" align="left">'.$from['DATE_NOTE'].'</td>';
        echo '</tr>';



        $sql_up="INSERT INTO enterprise.task_note (TASK_ID, SUB_TASK, LANGUAGE, LANG_STATUS, NOTE, DATE_NOTE)
 VALUES ('".$id['TASK_ID']."', '".$id['SUB_TASK']."', '".$lang['LANGUAGE']."', 'A', '".$NEW_note."', '".$from['DATE_NOTE']."');";
        mysqli_query($mylink['link'], $sql_up) or die ("Ошибка добавление  - ".$id['TASK_ID'].", ID - ".$id['SUB_TASK']." и языка -".$lang['LANGUAGE']." <br>".mysqli_error($mylink['link']));


    } // Перебор новых языков
}  // Перебор ID


    echo '</div>';
echo ' </body>';
echo ' </html>';
?>