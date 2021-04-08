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

$week_start = date("d/m/Y", strtotime('monday this week'));   
$week_end = date("d/m/Y", strtotime('sunday this week'));
$date_s = date('Y-m-d', strtotime('monday this week')); 
$date_e = date('Y-m-d', strtotime('sunday this week'));



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


// ------------------------- НОВЫЕ ЗАДАЧИ НА НЕДЕЛЕ ----------------------------------------
$out_excel .='<H3>New tasks set on this week.</H3>';
$new_task_color='#B4FFB4;';
$out_excel .='<table border="1">
<tr>
    <th align="center" style="background:'.$new_task_color.'"><B>Task №</B></th>
    <th style="background:'.$new_task_color.'"><B>Workshop</B></th>
    <th style="background:'.$new_task_color.'"><B>Workshop area</B></th>
    <th style="background:'.$new_task_color.'"><B>Task name</B></th>
    <th style="background:'.$new_task_color.'"><B>Task description</B></th>
    <th style="background:'.$new_task_color.'"><B>Priority</B></th>
    <th style="background:'.$new_task_color.'"><B>Status</B></th>    
    <th style="background:'.$new_task_color.'"><B>Responsible</B></th>
</tr>';

$SQL_NEW_TASK='SELECT task.TASK_ID, task.WORKSHOP, task.WORKSHOP_AREA, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task.TASK_PRIORITY,
task.TASK_STATUS, task.PERSON_RESPONSIBLE
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
WHERE (((task_language.LANGUAGE)="'.$LANG.'") AND ((task.SUB_TASK)="0") AND ((task.TASK_SETED)<"'.$date_e.' 23:59:59" AND (task.TASK_SETED)>"'.$date_s.' 00:00:00"))
GROUP BY task.TASK_ID, task.WORKSHOP, task.WORKSHOP_AREA, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task.TASK_PRIORITY,
task.TASK_STATUS, task.PERSON_RESPONSIBLE;';

$query_new_task=mysqli_query($mylink['link'], $SQL_NEW_TASK) or die ("Ошибка загрузки данных отчёта.<br>".mysqli_error($mylink['link']));
while ($assoc_new = mysqli_fetch_assoc($query_new_task)) {
    if (($assoc_new['WORKSHOP'])==''){$WORKSHOP='';} else {$WORKSHOP=$workshop_dc[$assoc_new['WORKSHOP']];}
    if (($assoc_new['WORKSHOP_AREA'])==''){$AREA='';} else {$AREA=$work_area_dc[$assoc_new['WORKSHOP_AREA']];}
    if (($assoc_new['TASK_STATUS'])==''){$STATUS='';} else {$STATUS=$Status_dc[$assoc_new['TASK_STATUS']];}

    $out_excel .='
    <tr>
        <td align="center">'.$assoc_new["TASK_ID"].'</td>
        <td align="center">'.$WORKSHOP.'</td>
        <td>'.$AREA.'</td>
        <td>'.$assoc_new["TASK_NAME"].'</td>
        <td>'.$assoc_new["TASK_DESCRIPTION"].'</td>
        <td align="center" style="background:'.prior_color($assoc_new['TASK_PRIORITY']).';">'.$assoc_new['TASK_PRIORITY'].'</td>
        <td align="center" style="background:'.status_color($assoc_new['TASK_STATUS']).';">'.$STATUS.'</td>       
        <td>'.get_FIO($assoc_new["PERSON_RESPONSIBLE"], $LANG).'</td>

    </tr>';  
}
    $out_excel .='</table><br>';
//-------------------------------------------------------------------------------------------

//----------------------------- Таблица завершенных задач ----------------------------------
$out_excel .='<H3>Tasks completed in a week.</H3>';
$task_fin_color='#F4A702';
$out_excel .='<table border="1">
<tr>
    <th align="center" style="background:'.$task_fin_color.'"><B>Task №</B></th>
    <th style="background:'.$task_fin_color.'"><B>Workshop</B></th>
    <th style="background:'.$task_fin_color.'"><B>Workshop area</B></th>
    <th style="background:'.$task_fin_color.'"><B>Task name</B></th>
    <th style="background:'.$task_fin_color.'"><B>Task description</B></th>
    <th style="background:'.$task_fin_color.'"><B>Priority</B></th>
    <th style="background:'.$task_fin_color.'"><B>Status</B></th>    
    <th style="background:'.$task_fin_color.'"><B>Responsible</B></th>

</tr>';

$SQL_A_NUM='SELECT task.TASK_ID
FROM task
WHERE (((task.TASK_STATUS)="A") AND ((task.TASK_END)<"'.$date_e.' 23:59:59" And (task.TASK_END)>"'.$date_s.' 00:00:00"))
GROUP BY task.TASK_ID;';
$query_a_num=mysqli_query($mylink['link'], $SQL_A_NUM) or die ("Ошибка загрузки номеров завершенных задач.<br>".mysqli_error($mylink['link']));
while ($assoc_mum = mysqli_fetch_assoc($query_a_num)) {

$SQL_COUNT_A='SELECT Count(task.TASK_ID) AS Count_ID
FROM task
WHERE (((task.TASK_ID)="'.$assoc_mum['TASK_ID'].'") AND ((task.TASK_STATUS)="A"));';
$query_count_a=mysqli_query($mylink['link'], $SQL_COUNT_A) or die ("Ошибка загрузки кол-ва завешенных пунктов задач.<br>".mysqli_error($mylink['link'])); 
$assoc_count_a = mysqli_fetch_assoc($query_count_a);
$count_a=$assoc_count_a['Count_ID'];

$SQL_COUNT_STEP='SELECT Count(task.TASK_ID) AS Count_ID
FROM task
WHERE (((task.TASK_ID)="'.$assoc_mum['TASK_ID'].'"));';
$query_count_step=mysqli_query($mylink['link'], $SQL_COUNT_STEP) or die ("Ошибка загрузки кол-ва пунктов задач.<br>".mysqli_error($mylink['link'])); 
$assoc_count_step = mysqli_fetch_assoc($query_count_step);
$count_step=$assoc_count_step['Count_ID'];

if ($count_a==$count_step){

    $SQL_GET_TASK='SELECT task.TASK_ID, task.WORKSHOP, task.WORKSHOP_AREA, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task.TASK_PRIORITY,
    task.TASK_PRIORITY, task.TASK_STATUS, task.PERSON_RESPONSIBLE
    FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
    WHERE (((task.SUB_TASK)="0") AND ((task_language.LANGUAGE)="'.$LANG.'"))
    GROUP BY task.TASK_ID, task.WORKSHOP, task.WORKSHOP_AREA, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task.TASK_PRIORITY,
    task.TASK_PRIORITY, task.TASK_STATUS, task.PERSON_RESPONSIBLE
    HAVING (((task.TASK_ID)="'.$assoc_mum['TASK_ID'].'"));';  
    $query_get_task=mysqli_query($mylink['link'], $SQL_GET_TASK) or die ("Ошибка загрузки данных отчёта.<br>".mysqli_error($mylink['link']));
    $assoc_task = mysqli_fetch_assoc($query_get_task);
        if (($assoc_task['WORKSHOP'])==''){$WORKSHOP='';} else {$WORKSHOP=$workshop_dc[$assoc_task['WORKSHOP']];}
        if (($assoc_task['WORKSHOP_AREA'])==''){$AREA='';} else {$AREA=$work_area_dc[$assoc_task['WORKSHOP_AREA']];}
        if (($assoc_task['TASK_STATUS'])==''){$STATUS='';} else {$STATUS=$Status_dc[$assoc_task['TASK_STATUS']];}
     
        $out_excel .='
        <tr>
            <td align="center">'.$assoc_task["TASK_ID"].'</td>
            <td align="center">'.$WORKSHOP.'</td>
            <td>'.$AREA.'</td>
            <td>'.$assoc_task["TASK_NAME"].'</td>
            <td>'.$assoc_task["TASK_DESCRIPTION"].'</td>
            <td align="center" style="background:'.prior_color($assoc_task['TASK_PRIORITY']).';">'.$assoc_task['TASK_PRIORITY'].'</td>
            <td align="center" style="background:'.status_color($assoc_task['TASK_STATUS']).';">'.$STATUS.'</td>       
            <td>'.get_FIO($assoc_task["PERSON_RESPONSIBLE"], $LANG).'</td>
    
        </tr>';
    }     
}

$out_excel .='</table><br>';
//-----------------------------------------------------------------------------------------------   

//----------------------------- Таблица задач в процессе ----------------------------------
$out_excel .='<H3>Tasks in progress.</H3>';
$task_in_color='#D9E1F2';
$out_excel .='<table border="1">
<tr>
    <th align="center" style="background:'.$task_in_color.'"><B>Task №</B></th>
    <th style="background:'.$task_in_color.'"><B>Workshop</B></th>
    <th style="background:'.$task_in_color.'"><B>Workshop area</B></th>
    <th style="background:'.$task_in_color.'"><B>Task name</B></th>
    <th style="background:'.$task_in_color.'"><B>Task description</B></th>
    <th style="background:'.$task_in_color.'"><B>Priority</B></th>
    <th style="background:'.$task_in_color.'"><B>Status</B></th>    
    <th style="background:'.$task_in_color.'"><B>Responsible</B></th>

</tr>';


$SQL_TASK_IN_WORK='SELECT task.TASK_ID, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_PRIORITY, task.PERSON_RESPONSIBLE, task.TASK_STATUS,
task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task_language.LANGUAGE
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
WHERE (((task.SUB_TASK)="0") AND ((task_language.LANGUAGE)="'.$LANG.'"))
GROUP BY task.TASK_ID, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_PRIORITY, task.PERSON_RESPONSIBLE, task.TASK_STATUS, task_language.TASK_NAME, task_language.TASK_DESCRIPTION, task_language.LANGUAGE
HAVING (((task.TASK_STATUS)="W" Or (task.TASK_STATUS)="E" Or (task.TASK_STATUS)="P"))
ORDER BY task.TASK_ID DESC;';

$query_task_in_work=mysqli_query($mylink['link'], $SQL_TASK_IN_WORK) or die ("Ошибка загрузки данных отчёта.<br>".mysqli_error($mylink['link']));
while ($assoc_tiw = mysqli_fetch_assoc($query_task_in_work)) {
    if (($assoc_tiw['WORKSHOP'])==''){$WORKSHOP='';} else {$WORKSHOP=$workshop_dc[$assoc_tiw['WORKSHOP']];}
    if (($assoc_tiw['WORKSHOP_AREA'])==''){$AREA='';} else {$AREA=$work_area_dc[$assoc_tiw['WORKSHOP_AREA']];}
    if (($assoc_tiw['TASK_STATUS'])==''){$STATUS='';} else {$STATUS=$Status_dc[$assoc_tiw['TASK_STATUS']];}
 //   if (($assoc_tiw["TASK_PRIORITY"])==''){$PRIORITY='';} else {$PRIORITY=$priority_dc[$assoc_tiw['TASK_PRIORITY']];}   
 //   prior_color($taskinfo['TASK_PRIORITY']).
 
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