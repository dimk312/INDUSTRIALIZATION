<?php
require ("../../inc/base_con.inc");
//require ("it_task.inc");
global  $mylink;
$mylink=con_my();
$cur_date = date('Y-m-d H:i:s');

$LANG='ENG';  // Язык отчёта
// Отчётный период
$day = date('N');  //от 1 (понедельник) до 7 (воскресенье)
//$week_start = date('m-d-Y', strtotime('-'.$day.' days'));
//$week_end = date('m-d-Y', strtotime('+'.(6-$day).' days'));
$week_num=date('W');  //Порядковый номер недели года в соответствии со стандартом ISO-8601;

//$week_start = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")-($day), date("Y"))); 
//$week_end = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")+($day), date("Y")));        

$week_start = date("d-m-Y", strtotime('monday this week'));   
$week_end = date("d-m-Y", strtotime('sunday this week'));




//Загрузка справочников
/*
//Загрузка справочника цеха из базы
//------------------ ЦЕХ ----------------------------------
 //Массив цехов
    $sql_w="SELECT dce_workshop.CODE_WORKSHOP, dce_workshop.NAME
FROM dce_workshop
WHERE (((dce_workshop.LANGUAGE)='".$LANG."') AND ((dce_workshop.DATE_B)<='".$cur_date."') AND ((dce_workshop.DATE_E) Is Null)) 
OR (((dce_workshop.LANGUAGE)='".$LANG."') AND ((dce_workshop.DATE_B)<='".$cur_date."') AND ((dce_workshop.DATE_E)>'".$cur_date ."'))
GROUP BY dce_workshop.CODE_WORKSHOP, dce_workshop.NAME
ORDER BY dce_workshop.CODE_WORKSHOP;";
$quer_w = mysqli_query($mylink['link'], $sql_w) or die ("Ошибка загрузки справочника workshop<br>".mysqli_error($mylink['link']));
while($assoc_w = mysqli_fetch_assoc($quer_w)){
    $key = $assoc_w['CODE_WORKSHOP'];
    $value =$assoc_w['NAME'];
    $workshop_dc[$key] = $value;}
//------------------------------------------------------------
*/

//Справочник для расшифровки цехов
$workshop_dc['UKR'] = 'WH';
$workshop_dc['UKRC'] = 'HP';
$workshop_dc['UKRG'] = 'General';


//Справочник для расшифровки статуса
$Status_dc['A'] = 'Done';
$Status_dc['C'] = 'Canceled';
$Status_dc['E'] = 'To do';
$Status_dc['P'] = 'PAUSE';
$Status_dc['W'] = 'In progress';

//---------------- Массив рабочих зон ------------------------
$sql_wa="SELECT dcе_workshop_area.CODE_WORKSHOP, dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
FROM dcе_workshop_area
WHERE (((dcе_workshop_area.LANGUAGE)='".$LANG."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E) Is Null)) 
OR (((dcе_workshop_area.LANGUAGE)='".$LANG."') AND ((dcе_workshop_area.DATE_B)<='".$cur_date."') AND ((dcе_workshop_area.DATE_E)>'".$cur_date ."'))
GROUP BY dcе_workshop_area.CODE_AREA, dcе_workshop_area.NAME
ORDER BY dcе_workshop_area.CODE_AREA;";
$quer_wa = mysqli_query($mylink['link'], $sql_wa) or die ("Ошибка загрузки справочника workshop_area<br>".mysqli_error($mylink['link']));
while($assoc_wa = mysqli_fetch_assoc($quer_wa)){
    $workshop=$assoc_wa['CODE_WORKSHOP'];
    $key = $assoc_wa['CODE_AREA'];
    $value =$assoc_wa['NAME'];
    $work_area_dc[$key] = $value;}
//---------------------------------------------------------------    


/*
//------------- ПРИОРИТЕТ ---------------------------------------
$sql_p = "SELECT dc_priority.CODE_PRIORITY, dc_priority.NAME
FROM dc_priority
WHERE (((dc_priority.LANGUAGE)='".$LANG."') AND ((dc_priority.DATE_B)<='".$cur_date."') AND ((dc_priority.DATE_E) Is Null)) 
OR (((dc_priority.LANGUAGE)='".$LANG."') AND ((dc_priority.DATE_B)<='".$cur_date."') AND ((dc_priority.DATE_E)>'".$cur_date ."'))
GROUP BY dc_priority.CODE_PRIORITY, dc_priority.NAME
ORDER BY dc_priority.CODE_PRIORITY;";
$quer_p = mysqli_query($mylink['link'], $sql_p) or die ("Ошибка загрузки справочника приоритета<br>".mysqli_error($mylink['link']));
    while($assoc_p = mysqli_fetch_assoc($quer_p)){
        $key = $assoc_p['CODE_PRIORITY'];
        $value =$assoc_p['NAME'];
        $priority_dc[$key] = $value;}
//--------------------------------------------------------------- 
*/



//-----------------------Расшифровка ФИО ------------------------
function get_FIO($PERSON_ID, $LANG) {
    global $mylink;
    if (isset($PERSON_ID) and $PERSON_ID!=''){
    $sql = "SELECT person_language.PERSON_ID, person_language.LANGUAGE, person_language.NAME, person_language.SURNAME, person_language.PATRONYMIC
FROM person_language
WHERE (((person_language.PERSON_ID)='".$PERSON_ID."') AND ((person_language.LANGUAGE)= '".$LANG."'));";
    $query = mysqli_query($mylink['link'], $sql) or die ("Ошибка загрузки данных PERSON <br>".mysqli_error($mylink['link']));
    $assoc = mysqli_fetch_assoc($query);
    $I=mb_substr(($assoc['NAME']), 0, 1);
    if (($assoc['PATRONYMIC']=='') or ($assoc['PATRONYMIC']=='-')) {$FIO=  $assoc['SURNAME']." ".$I.".";
    } else {$O=mb_substr(($assoc['PATRONYMIC']), 0, 1);
        $FIO=  $assoc['SURNAME']." ".$I.". ".$O.".";
    }}  else {$FIO='';}
    return($FIO);}
//--------------------------------------------------------------- 

function status_color($STATUS) {
    $color="#FEFEfE";
    if ($STATUS=='A'){$color="#FF2020";}
    if ($STATUS=='C'){$color="#F4A702";}
    if ($STATUS=='W'){$color="#B4B4FF";}
    if ($STATUS=='E'){$color="#B4FFB4";}
    if ($STATUS=='P'){$color="textHY";}
    return($color);}  

function prior_color($PRIORITY) {
    $color="#FFFF6A";
    if ($PRIORITY=='P0'){$color="#FF2020";}
    if ($PRIORITY=='P1'){$color="#F4A702";}
    if ($PRIORITY=='P2'){$color="#B4B4FF";}
    if ($PRIORITY=='P3'){$color="#B4FFB4";}
    return($color);}    

//------------------DATE_MY2RU---------------------------
function my2ru($date) {  //if ($date==null) { return date("d/m/Y");}
if ($date==null) { return '&nbsp;';}
    else {return strftime("%d/%m/%Y",strtotime($date));}
	}
//-------------------------------------------------------
// Преобразуем дату из строки с разделителем "./-" в формат mysql
function formdate_ru2my($date) {

    if (preg_match ("/([0-9]{2})[.\/-]([0-9]{2})[.\/-]([0-9]{4})/", $date, $regs)) {
        list(,$day,$month,$year)=$regs;
    } else {
        return false;
    }
    if (checkdate($month, $day, $year)) return ("$year-$month-$day");
        else return false;
    } 
//-------------------------------------------------------

$out_excel ='<H2>List of tasks of the industrialization department for the period '.$week_start.' - '.$week_end.'.<br> (Week number - '.$week_num.')</H2>';
$out_excel .='<br>';

/*
// ------------------------- НОВЫЕ ЗАДАЧИ НА НЕДЕЛЕ ----------------------------------------
$out_excel .='<H3>New tasks set on this week.</H3>';
$new_task_color='#B4FFB4';
$out_excel .='<table border="1">
<tr>
    <th align="center" style="background:'.$new_task_color.';"><B>Task №</B></th>
    <th align="center" style="background:'.$new_task_color.';"><B>Date set task</B></th>
    <th align="center" style="background:'.$new_task_color.';"><B>Date task start</B></th>
    <th align="center" style="background:'.$new_task_color.';"><B>Date planned end</B></th>
    <th align="center" style="background:'.$new_task_color.';"><B>Date end task</B></th>
    <th style="background:'.$new_task_color.';"><B>Workshop</B></th>
    <th style="background:'.$new_task_color.';"><B>Workshop area</B></th>
    <th style="background:'.$new_task_color.';"><B>Priority</B></th>
    <th style="background:'.$new_task_color.';"><B>Responsible</B></th>
    <th style="background:'.$new_task_color.';"><B>Task name</B></th>
    <th style="background:'.$new_task_color.';"><B>Task description</B></th>
</tr>';

$SQL_NEW_TASK=' SELECT task.TASK_ID, task.TASK_SETED, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_PRIORITY, task.PERSON_RESPONSIBLE, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task.TASK_STATUS, task_language.LANGUAGE
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
GROUP BY task.TASK_ID, task.TASK_SETED, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_PRIORITY, task.PERSON_RESPONSIBLE, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task.TASK_STATUS, task_language.LANGUAGE
HAVING (((task.TASK_SETED) Between "'.formdate_ru2my($week_start).' 00:00:00" And "'.formdate_ru2my($week_end).' 23:59:59") AND ((task_language.LANGUAGE)="'.$LANG.'"));';

//echo '<br>$SQL_NEW_TASK='.$SQL_NEW_TASK.'<br>';

$QUERY_NEW_TASK=mysqli_query($mylink['link'], $SQL_NEW_TASK) or die ("Ошибка загрузки данных отчёта.<br>".mysqli_error($mylink['link']));
while ($ASSOC_NT = mysqli_fetch_assoc($QUERY_NEW_TASK)) {
    if (($ASSOC_NT['WORKSHOP'])==''){$WORKSHOP='';} else {$WORKSHOP=$workshop_dc[$ASSOC_NT['WORKSHOP']];}
    if (($ASSOC_NT['WORKSHOP_AREA'])==''){$AREA='';} else {$AREA=$work_area_dc[$ASSOC_NT['WORKSHOP_AREA']];}
    if (($ASSOC_NT["TASK_PRIORITY"])==''){$PRIORITY='';} else {$PRIORITY=$priority_dc[$ASSOC_NT['TASK_PRIORITY']];}   


    $out_excel .='
    <tr>
        <td align="center">'.$ASSOC_NT["TASK_ID"].'</td>
        <td align="center">'.my2ru($ASSOC_NT["TASK_SETED"]).'</td>
        <td align="center">'.my2ru($ASSOC_NT["TASK_START"]).'</td>
        <td align="center">'.my2ru($ASSOC_NT["TASK_PLAN_END"]).'</td>
        <td align="center">'.my2ru($ASSOC_NT["TASK_END"]).'</td>
        <td>'.$WORKSHOP.'</td>
        <td>'.$AREA.'</td>
        <td align="center" style="background:'.prior_color($ASSOC_NT['TASK_PRIORITY']).';">'.$PRIORITY.'</td>
        <td>'.get_FIO($ASSOC_NT["PERSON_RESPONSIBLE"], $LANG).'</td>
        <td>'.$ASSOC_NT["TASK_NAME"].'</td>
        <td>'.$ASSOC_NT["TASK_DESCRIPTION"].'</td>
    </tr>';  
}
    $out_excel .='</table>';
//-----------------------------------------------------------------------------------------
$out_excel .='<br>';
*/







//----------------------------- Таблица задач в процессе ----------------------------------
//$out_excel .='<H3>Tasks in progress.</H3>';
$task_in_color='#D9E1F2';
$out_excel .='<table border="1">
<tr>
    <th align="center" style="background:#D9E1F2;"><B>Task №</B></th>
    <th style="background:#D9E1F2;"><B>Workshop</B></th>
    <th style="background:#D9E1F2;"><B>Workshop area</B></th>
    <th style="background:#D9E1F2;"><B>Task name</B></th>
    <th style="background:#D9E1F2;"><B>Task description</B></th>
    <th style="background:#D9E1F2;"><B>Priority</B></th>
    <th style="background:#D9E1F2;"><B>Status</B></th>    
    <th style="background:#D9E1F2;"><B>Responsible</B></th>

</tr>';

/*
    <th align="center" style="background:#D9E1F2;"><B>Date set task</B></th>
    <th align="center" style="background:#D9E1F2;"><B>Date task start</B></th>
    <th align="center" style="background:#D9E1F2;"><B>Date planned end</B></th>
    <th align="center" style="background:#D9E1F2;"><B>Date end task</B></th>
*/



$SQL_TASK_IN_WORK='SELECT task.TASK_ID, task.TASK_SETED, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.WORKSHOP, task.WORKSHOP_AREA, 
task.TASK_PRIORITY, task.PERSON_RESPONSIBLE, task.TASK_STATUS, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
WHERE (((task_language.LANGUAGE)="'.$LANG.'") AND (((task.TASK_STATUS)="W") or ((task.TASK_STATUS)="E") or ((task.TASK_STATUS)="P")))
GROUP BY task.TASK_ID, task.TASK_SETED, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_PRIORITY, task.PERSON_RESPONSIBLE,
task_language.TASK_NAME, task_language.TASK_DESCRIPTION;';
$query_task_in_work=mysqli_query($mylink['link'], $SQL_TASK_IN_WORK) or die ("Ошибка загрузки данных отчёта.<br>".mysqli_error($mylink['link']));
while ($assoc_tiw = mysqli_fetch_assoc($query_task_in_work)) {
    if (($assoc_tiw['WORKSHOP'])==''){$WORKSHOP='';} else {$WORKSHOP=$workshop_dc[$assoc_tiw['WORKSHOP']];}
    if (($assoc_tiw['WORKSHOP_AREA'])==''){$AREA='';} else {$AREA=$work_area_dc[$assoc_tiw['WORKSHOP_AREA']];}
    if (($assoc_tiw['TASK_STATUS'])==''){$STATUS='';} else {$STATUS=$Status_dc[$assoc_tiw['TASK_STATUS']];}
 //   if (($assoc_tiw["TASK_PRIORITY"])==''){$PRIORITY='';} else {$PRIORITY=$priority_dc[$assoc_tiw['TASK_PRIORITY']];}   
 //   prior_color($taskinfo['TASK_PRIORITY']).
 
/*
        <td align="center">'.my2ru($assoc_tiw["TASK_SETED"]).'</td>
        <td align="center">'.my2ru($assoc_tiw["TASK_START"]).'</td>
        <td align="center">'.my2ru($assoc_tiw["TASK_PLAN_END"]).'</td>
        <td align="center">'.my2ru($assoc_tiw["TASK_END"]).'</td>
*/

    $out_excel .='
    <tr>
        <td align="center">'.$assoc_tiw["TASK_ID"].'</td>
        <td align="center">'.$WORKSHOP.'</td>
        <td>'.$AREA.'</td>
        <td>'.$assoc_tiw["TASK_NAME"].'</td>
        <td>'.$assoc_tiw["TASK_DESCRIPTION"].'</td>
        <td align="center" style="background:'.prior_color($assoc_tiw['TASK_PRIORITY']).';">'.$assoc_tiw['TASK_PRIORITY'].'</td>
        <td align="center" style="background:'.status_color($assoc_tiw['TASK_STATUS']).';">'.$STATUS.'</td>       
        <td>'.get_FIO($assoc_tiw["PERSON_RESPONSIBLE"], $LANG).'</td>

    </tr>';  
}
    $out_excel .='</table>';
//-----------------------------------------------------------------------------------------------    

    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename=report_week_'.$week_num.'.xls');

echo $out_excel;

?>