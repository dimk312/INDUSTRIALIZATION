<?php

//--- Обновление и перевод записей NOTES
function update_notes_language($TASK_ID, $SUB_TASK, $DATE_NOTE) {
	    global $mylink;

//--- ПОЛЕ ДЛЯ ПЕРЕНОСА НА ВСЕ ЗАПИСИ ------------------------------
$sql_const="SELECT task_note.PERSON_ID
FROM task_note
WHERE (((task_note.TASK_ID)='".$TASK_ID."') AND ((task_note.SUB_TASK)='".$SUB_TASK."') AND ((task_note.LANG_STATUS)='U') AND ((task_note.DATE_NOTE)='".$DATE_NOTE."'));";
$quer_const = mysqli_query($mylink['link'], $sql_const);
$const = mysqli_fetch_assoc($quer_const);
$PERSON_ID=$const['PERSON_ID'];
//------------------------------------------------------------------


//---- ЯЗЫКИ ДЛЯ ДОБАВЛЕНИЯ --------------------------------
$sql_lang="SELECT dc_language_work.LANGUAGE
FROM (SELECT task_note.LANGUAGE
FROM task_note
WHERE (((task_note.TASK_ID)='".$TASK_ID."') and ((task_note.SUB_TASK)='".$SUB_TASK."') and ((task_note.DATE_NOTE)='".$DATE_NOTE."'))) AS SELECT_IDL RIGHT JOIN dc_language_work ON SELECT_IDL.LANGUAGE = dc_language_work.LANGUAGE
WHERE ((SELECT_IDL.LANGUAGE) Is Null);";
$quer_lang = mysqli_query($mylink['link'], $sql_lang);
while ($add_lang = mysqli_fetch_assoc($quer_lang)){
	// -------- ДОБАВЛЕНИЕ ЯЗЫКОВЫХ СТРОК ------------------
	$sql_add="INSERT INTO enterprise.task_note (TASK_ID, SUB_TASK, DATE_NOTE, LANGUAGE, LANG_STATUS) 
	VALUES ('".$TASK_ID."',  '".$SUB_TASK."', '".$DATE_NOTE."', '".$add_lang['LANGUAGE']."', 'A');";
	mysqli_query($mylink['link'], $sql_add);
	}
//-----------------------------------------------------------

	
//--------------ВЫБОР ЯЗЫКА И ОСНОВЫ - $lang_from ----------------------
$sql_sel_original="SELECT task_note.TASK_ID, task_note.SUB_TASK, task_note.LANGUAGE, task_note.LANG_STATUS, task_note.DATE_NOTE, task_note.SUBJECT, task_note.NOTE
FROM task_note
WHERE (((task_note.TASK_ID)='".$TASK_ID."') AND ((task_note.LANG_STATUS)='U') AND ((task_note.SUB_TASK)='".$SUB_TASK."') AND ((task_note.DATE_NOTE)='".$DATE_NOTE."'));";
$quer_original = mysqli_query($mylink['link'], $sql_sel_original);
$original = mysqli_fetch_assoc($quer_original);
if ($original['LANGUAGE']=='RUS'){$lang_from='ru';}
if ($original['LANGUAGE']=='UKR'){$lang_from='uk';}
if ($original['LANGUAGE']=='ENG'){$lang_from='en';}
if ($original['LANGUAGE']=='FRA'){$lang_from='fr';}
//---------------------------------------------------------------------


//--------------ВЫБОР ЗАПИСЕЙ ДЛЯ ОБНОВЛЕНИЯ-----------------
$sql_up="SELECT task_note.LANGUAGE
FROM task_note
WHERE (((task_note.TASK_ID)='".$TASK_ID."') AND ((task_note.SUB_TASK)='".$SUB_TASK."') AND ((task_note.DATE_NOTE)='".$DATE_NOTE."') AND ((task_note.LANG_STATUS)='A'));";
$quer_up = mysqli_query($mylink['link'], $sql_up);
while ($mod_lang = mysqli_fetch_assoc($quer_up)){
//-------------------------------------------------------------

//------------------ $lang_to ---------------------------------
if ($mod_lang['LANGUAGE']=='RUS'){$lang_to='ru';}
if ($mod_lang['LANGUAGE']=='UKR'){$lang_to='uk';}
if ($mod_lang['LANGUAGE']=='ENG'){$lang_to='en';}
if ($mod_lang['LANGUAGE']=='FRA'){$lang_to='fr';}

//-------------- ОБНОВЛЕНИЕ ----------------------------------
	$UP_SUBJECT=mysqli_real_escape_string($mylink['link'], gtranslate($original['SUBJECT'], $lang_from, $lang_to));
	$UP_NOTE=mysqli_real_escape_string($mylink['link'], gtranslate($original['NOTE'], $lang_from, $lang_to));
	$sql_up="UPDATE enterprise.task_note SET SUBJECT = '".$UP_SUBJECT."', NOTE = '".$UP_NOTE."',  USER_M ='".$_SESSION['logged_user']."'
	 WHERE (TASK_ID = '".$TASK_ID."') and (SUB_TASK = '".$SUB_TASK."') and (LANGUAGE = '".$mod_lang['LANGUAGE']."')  and (DATE_NOTE = '".$DATE_NOTE."');";

	
//echo '<br>$original[SUBJECT] - '.$original['SUBJECT'];
//echo '<br>$lang_from - '.$lang_from;
//echo '<br>$lang_to - '.$lang_to;	 
//echo '<br>$UP_SUBJECT - '.$UP_SUBJECT;
	
	mysqli_query($mylink['link'], $sql_up);
//------------------------------------------------------------
}//--------------ВЫБОР ЗАПИСЕЙ ДЛЯ ОБНОВЛЕНИЯ-----------------
}








	//--- Перезапись языковых строк для изменённой заметки -----------------------------
function change_records_note($TASK_ID, $SUB_TASK, $DATE_NOTE, $LANGUAGE, $SUBJECT, $NOTE) {
			global $mylink;
	
			/*
	//--- ПОЛЕ ДЛЯ ПЕРЕНОСА НА ВСЕ ЗАПИСИ ------------------------------
	$sql_const="SELECT task_note.PERSON_ID
	FROM task_note
	WHERE (((task_note.TASK_ID)='".$TASK_ID."') AND ((task_note.SUB_TASK)='".$SUB_TASK."') AND ((task_note.LANGUAGE)='". $LANGUAGE."') AND ((task_note.DATE_NOTE)='".$DATE_NOTE."'));";
	$quer_const = mysqli_query($mylink['link'], $sql_const);
	$const = mysqli_fetch_assoc($quer_const);
	$PERSON_ID=$const['PERSON_ID'];
	//------------------------------------------------------------------
*/

	//--------------ВЫБОР ЯЗЫКА И ОСНОВЫ - $lang_from ----------------------
if ($LANGUAGE=='RUS'){$lang_from='ru';}
if ($LANGUAGE=='UKR'){$lang_from='uk';}
if ($LANGUAGE=='ENG'){$lang_from='en';}
if ($LANGUAGE=='FRA'){$lang_from='fr';}
//---------------------------------------------------------------------


//--------------ВЫБОР ЗАПИСЕЙ ДЛЯ ОБНОВЛЕНИЯ-----------------
$sql_up="SELECT task_note.LANGUAGE
FROM task_note
WHERE (((task_note.TASK_ID)='".$TASK_ID."') AND ((task_note.SUB_TASK)='".$SUB_TASK."') AND ((task_note.DATE_NOTE)='".$DATE_NOTE."') AND ((task_note.LANGUAGE)<>'". $LANGUAGE."'));";
$quer_up = mysqli_query($mylink['link'], $sql_up);
while ($mod_lang = mysqli_fetch_assoc($quer_up)){
//-------------------------------------------------------------

//------------------ $lang_to ---------------------------------
if ($mod_lang['LANGUAGE']=='RUS'){$lang_to='ru';}
if ($mod_lang['LANGUAGE']=='UKR'){$lang_to='uk';}
if ($mod_lang['LANGUAGE']=='ENG'){$lang_to='en';}
if ($mod_lang['LANGUAGE']=='FRA'){$lang_to='fr';}

//-------------- ОБНОВЛЕНИЕ ----------------------------------
echo '<br>'.$SUBJECT;
	$UP_SUBJECT=mysqli_real_escape_string($mylink['link'], gtranslate($SUBJECT, $lang_from, $lang_to));
	$UP_NOTE=mysqli_real_escape_string($mylink['link'], gtranslate($NOTE, $lang_from, $lang_to));
	$sql_up="UPDATE enterprise.task_note SET SUBJECT = '".$UP_SUBJECT."', NOTE = '".$UP_NOTE."',  USER_M ='".$_SESSION['logged_user']."',  LANG_STATUS ='A'
	 WHERE (TASK_ID = '".$TASK_ID."') and (SUB_TASK = '".$SUB_TASK."') and (LANGUAGE = '".$mod_lang['LANGUAGE']."')  and (DATE_NOTE = '".$DATE_NOTE."');";
	
mysqli_query($mylink['link'], $sql_up);
//------------------------------------------------------------
}//--------------ВЫБОР ЗАПИСЕЙ ДЛЯ ОБНОВЛЕНИЯ-----------------

}  //  ----- КОНЕЦ ФУНКЦИИ ----------------------------------

?>
