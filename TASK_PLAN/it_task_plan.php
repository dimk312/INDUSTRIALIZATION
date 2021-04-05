<?php
require ("../../inc/base_con.inc");
require ("../../inc/date.inc");
require ("../../inc/translate.inc");
require ("../../login/access.inc");
require ("../../DICTION/PERSON/person.inc");
require ("it_task.inc");
session_start();
global  $mylink;
$mylink=con_my();
// $mylink['base'];
// $mylink['host'];
$SCRIPT=basename($_SERVER['SCRIPT_NAME']);
$cur_date=curdate_ms();
$access=ACCESS();
// Игнорировать вывод для 
$foto_cod='0100'; // 0100 - Код Фото
$otladchik='dimk312';

//Выбрано по умолчанию
if(isset($_POST['WAIT']) or isset($_POST['ARCHIVE']) or isset($_POST['WORK']) or isset($_POST['CANCEL']) or isset($_POST['PAUSE'])){ } else {$_POST['WAIT'] = 'ON'; $_POST['WORK'] = 'ON'; $_POST['PAUSE'] = 'ON';}

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
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------


echo '<!DOCTYPE html>';
echo '<head>';
echo '<html lang="'.$html_lang.'">';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>List of task</title>';
echo '<link rel="stylesheet" href="../../CSS/'.$_SESSION['COLOR_SCHEME'].'.css"  type="text/css" />';
echo '</head>';
echo '<body>';
echo '<main>';
echo '<header>';
echo '<form id="task_filter" enctype="multipart/form-data" method="post">';
echo '<div class="Back_H1 shadow" align="center">';
echo '<table width="100%">';

echo '<tr>'; //------------------------------------------------------------------------------------------------------------------------------------------------------
echo '<td width="100px" align="center"><img src="../IMG/industrialization.png" height="100px" style="vertical-align: middle"</td>';


echo '<td align="left" valign="top" width="320px">'; // ------------------------------- 1-я колонка ---------------------------------------------------------------
echo '<table width="100%">';
echo '<tr>';
echo '<td nowrap class="FC_W FSB26" align="left">&nbsp;&nbsp;'.$lang_interface['id_task_list'].'&nbsp;</td>';
echo '</tr>';
echo '<tr>';
//------------------------ ОТВЕТСТВЕННЫЙ -------------------------------------------
//width="320px"
echo '<td nowrap class="FC_W FS14" align="left"><label for="RESPONSIBLE">'.$lang_interface['id_responsible'].'&nbsp;</label>';
?>
    <select size="1" class="FS12" name="RESPONSIBLE" onChange="document.getElementById('task_filter').submit();">
<?php

$responsable = get_responsable(); //Массив ответственных
if (isset($_POST['RESPONSIBLE'])){
    if ($_POST['RESPONSIBLE']=='ALL'){echo '<option class="FS12" selected value="ALL">&nbsp;'.$lang_interface['id_resp_all'].'&nbsp;</option>';} else
    {echo '<option class="FS12" value="ALL">&nbsp;'.$lang_interface['id_resp_all'].'&nbsp;</option>';}

    foreach ($responsable as $key => $value) {
        if ($_POST['RESPONSIBLE']==$key){echo '<option class="FS12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
            echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
}else{
    echo '<option class="FS12" selected value="ALL">&nbsp;'.$lang_interface['id_resp_all'].'&nbsp;</option>';
    foreach ($responsable as $key => $value) {echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
    $_POST['RESPONSIBLE']='ALL';
}
echo '</select>';
echo '</td>';
//----------------------------------------------------------------------------
echo '</tr>';
echo '<tr>';
//-------------------- СОРТИРОВКА ------------------------------------
echo '<td nowrap class="FC_W FS14" align="left"><label for="SORT">'.$lang_interface['id_sort'].'&nbsp;</label>';
?>
    <select size="1" class="FS12" name="SORT" onChange="document.getElementById('task_filter').submit();">
<?php

$sort=get_sorting();
if (isset($_POST['SORT'])){
    foreach ($sort as $key => $value) {
        if ($_POST['SORT']==$key){echo '<option class="FS12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
            echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
} else {
    foreach ($sort as $key => $value) {echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
    $_POST['SORT']='NUMB';
}
echo '</select>';
//-------------------- ORDER  ------------------------------------
echo '<label class="switch" name="ORDER" data-tooltip="'.$lang_interface['id_sort_rev'].'">';

if (isset($_POST['ORDER'])) {echo '<input type="checkbox" checked name="ORDER" value="ON"';
?>
        onChange="document.getElementById('task_filter').submit();"
<?php
echo  '>';} else {echo '<input type="checkbox" name="ORDER" value="ON"';
?>
        onChange="document.getElementById('task_filter').submit();"
<?php
echo '>';}


echo '<span class="slider round"></span>';
//echo '<span class="slider"></span>';
echo '</label>';
//--------------------------------------------------------------------------
echo '</td>';
//------------------------------------------------------------------------
echo '</tr>';
echo '</table>';
echo '</td>'; // -------------------------------END 1-я колонка -------------------------------------------------------------------------------------------------------------------

echo '<td align="left" valign="top" width="280px">'; // ------------------------------- 2-я колонка ----------------------------------------------------------------------------------------------------------------------
echo '<table width="100%">';
echo '<tr>';
echo '<td nowrap align="center" colspan="2" class="FC_W FS14" align="left"><label for="STATUS">'.$lang_interface['id_status'].'&nbsp;</label></td>';
//echo '<td>&nbsp;</td>';
echo '</tr>';

//-------------------- WAIT  ------------------------------------
echo '<tr>';
echo '<td nowrap width="50%" class="FC_W FS14" align="left">&nbsp;'.$lang_interface['id_stat_wait'].'&nbsp;';
echo '<label class="switch" name="WAIT">';
if(isset($_POST['WAIT'])){echo '<input type="checkbox" checked name="WAIT" value="ON"';
        ?>
        onChange="document.getElementById('task_filter').submit();"
        <?php
        echo  '>';} else {echo '<input type="checkbox" name="WAIT" value="ON"';
        ?>
        onChange="document.getElementById('task_filter').submit();"
        <?php
        echo '>';}
echo '<span class="slider round"></span>';
//echo '<span class="slider"></span>';
echo '</label>';
//--------------------------------------------------------------------------
echo '</td>';


echo '<td nowrap width="50%" class="FC_W FS14" align="left">&nbsp;'.$lang_interface['id_stat_arhiv'].'&nbsp;';
//-------------------- ARCHIVE  ------------------------------------
echo '<label class="switch" name="ARCHIVE">';
if(isset($_POST['ARCHIVE'])){echo '<input type="checkbox" checked name="ARCHIVE" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo  '>';} else {echo '<input type="checkbox" name="ARCHIVE" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo '>';}
echo '<span class="slider round"></span>';
//echo '<span class="slider"></span>';
echo '</label>';
//--------------------------------------------------------------------------
echo '</td>';


echo '</tr>';
echo '<tr>';

echo '<td nowrap class="FC_W FS14" align="left">&nbsp;'.$lang_interface['id_stat_inwork'].'&nbsp;';
//-------------------- WORK  ------------------------------------
echo '<label class="switch" name="WORK">';
if(isset($_POST['WORK'])){echo '<input type="checkbox" checked name="WORK" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo  '>';} else {echo '<input type="checkbox" name="WORK" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo '>';}
echo '<span class="slider round"></span>';
//echo '<span class="slider"></span>';
echo '</label>';
//--------------------------------------------------------------------------
echo '</td>';


echo '<td nowrap class="FC_W FS14" align="left">&nbsp;'.$lang_interface['id_stat_cancel'].'&nbsp;';
//-------------------- CANCEL  ------------------------------------
echo '<label class="switch" name="CANCEL">';
if(isset($_POST['CANCEL'])){echo '<input type="checkbox" checked name="CANCEL" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo  '>';} else {echo '<input type="checkbox" name="CANCEL" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo '>';}
echo '<span class="slider round"></span>';
//echo '<span class="slider"></span>';
echo '</label>';
//--------------------------------------------------------------------------
echo '</td>';


echo '</tr>';
echo '<tr>';
echo '<td nowrap class="FC_W FS14" align="left">&nbsp;'.$lang_interface['id_stat_pause'].'&nbsp;';
//-------------------- PAUSE  ------------------------------------
echo '<label class="switch" name="PAUSE">';
if(isset($_POST['PAUSE'])){echo '<input type="checkbox" checked name="PAUSE" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo  '>';} else {echo '<input type="checkbox" name="PAUSE" value="ON"';
    ?>
    onChange="document.getElementById('task_filter').submit();"
    <?php
    echo '>';}
echo '<span class="slider round"></span>';
//echo '<span class="slider"></span>';
echo '</label>';
//--------------------------------------------------------------------------
echo '</td>';



echo '<td>&nbsp;</td>';
echo '</tr>';
echo '</table>';
echo '</td>'; // -------------------------------END 2-я колонка -------------------------------------------------------------------------------------------------------------------

echo '<td align="left" valign="top" width="850px">'; // ------------------------------- 3-я колонка ----------------------------------------------------------------------------------------------------------------------
echo '<table>';
echo '<tr>';
echo '<td colspan="2" width="850px">';
if(isset($_POST['SERCH_TEXT']) and ($_POST['SERCH_TEXT'])!='') {
    // Удаляем лишние пробелы и спецсимволы
    $_POST['SERCH_TEXT']=preg_replace('/\s/', ' ', $_POST['SERCH_TEXT']);
    $_POST['SERCH_TEXT']=trim ($_POST['SERCH_TEXT'], " \n\r\t\v\0");
    $_POST['SERCH_TEXT'] = preg_replace('|[\s]+|s', ' ', $_POST['SERCH_TEXT']);

    echo '&nbsp;<input class="FSB22"  name="SERCH_TEXT" id="serch_long" value="'.$_POST['SERCH_TEXT'].'">';
} else {
    echo '&nbsp;<input class="FSB22"  name="SERCH_TEXT" id="serch_long" placeholder="&nbsp;'.$lang_interface['id_serch_in'].'">';}
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td nowrap width="220px" class="FC_W FS14" align="left"><label for="WORKSHOP">'.$lang_interface['id_workshop'].'&nbsp;</label></td>';
echo '<td nowrap class="FC_W FS14" align="left"><label for="AREA">&nbsp;'.$lang_interface['id_area'].'&nbsp;</label></td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
//----------------- ВЫБОР WORKSHOP ----------------------------------------
?>
    <select size="1" class="FS12" name="WORKSHOP" onChange="document.getElementById('task_filter').submit();">
<?php
$workshop = get_workshop(); //Массив цехов
if (isset($_POST['WORKSHOP'])){
    if ($_POST['WORKSHOP']=='ALL'){echo '<option class="FS12" selected value="ALL">&nbsp;'.$lang_interface['id_work_all'].'&nbsp;</option>';} else
    {echo '<option class="FS12" value="ALL">&nbsp;'.$lang_interface['id_work_all'].'&nbsp;</option>';}

    foreach ($workshop as $key => $value) {
        if ($_POST['WORKSHOP']==$key){echo '<option class="FS12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
            echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
} else {
    echo '<option class="FS12" selected value="ALL">&nbsp;'.$lang_interface['id_work_all'].'&nbsp;</option>';
    foreach ($workshop as $key => $value) {echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
    $_POST['WORKSHOP']='ALL';
}
echo '</select>';
//----------------------------------------------------------------------------
echo '</td>';
echo '<td>';
//------- ВЫБОР AREA -----------------------------------------------
?>
    <select size="1" class="FS12" name="AREA" onChange="document.getElementById('task_filter').submit();">
<?php
$workshop_area = get_workshop_area(); //Массив рабочих зон
if (isset($_POST['AREA'])){
    if ($_POST['AREA']=='ALL'){echo '<option class="FS12" selected value="ALL">&nbsp;'.$lang_interface['id_area_all'].'&nbsp;</option>';} else
    {echo '<option class="FS12" value="ALL">&nbsp;'.$lang_interface['id_area_all'].'&nbsp;</option>';}

    foreach ($workshop_area as $key => $value) {
        if ($_POST['AREA']==$key){echo '<option class="FS12" selected value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';} else {
            echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';}}
}else{
    echo '<option class="FS12" selected value="ALL">&nbsp;'.$lang_interface['id_area_all'].'&nbsp;</option>';
    foreach ($workshop_area as $key => $value) {echo '<option class="FS12" value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>'; }
    $_POST['AREA']='ALL';
}
echo '</select>';
//----------------------------------------------------------------------------
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</td>'; // -------------------------------END 3-я колонка -------------------------------------------------------------------------------------------------------------------
echo '<td align="left"  valign="midle" >';  // ------------------------------- 4-я колонка -----------------------------------------------------------------------
echo '<button class="fly_button"><img src="../../IMAGES/serch.png" height="28px">SERCH</button>';
echo '</td>';// -------------------------------END 3-я колонка -------------------------------------------------------------------------------------------------------------------
echo '</form>';

echo '<td width="5%">';
if (($access['ITAT'])=='A') {
    echo '<table width="100%">';
    echo '<tr>';

    echo '<td align="center">';
    echo '<form action="it_task.php" target="TASK" enctype="multipart/form-data" method="post">';
    echo '<button type="submit" name="NEW_TASK">';
    echo '<img src="IMG/add_task.png" height="40px" alt="Add task" title="'.$lang_interface['id_add_task'].'">';
    echo '</button>';
    echo '</form>';
    echo '</td>';

    echo '</tr>';
    echo '<tr>';

    echo '<td align="center">';
    echo '<form action="exp_plan_excel.php" target="EXCEL" enctype="multipart/form-data" method="post">';
    echo '<button type="submit" name="EXP_EXCEL">';
    echo '<img src="IMG/exp_excel.png" height="40px" alt="Export excel" title="'.$lang_interface['id_exp_excel'].'">';
    echo '</button>';
    echo '</form>';
    echo '</td>';

    echo '</tr>';
    echo '</table>';

}
echo '</td>';
echo '</table>';
echo '</div>';
echo '</main>';
echo '</header>';

//-------------------------------------------------------------------------------------------------------------------
echo '<BR><BR><BR>';
echo '<BR><BR><BR>';
echo '<BR><BR>';

if (($_SESSION['logged_user']) == $otladchik) {

echo '<br>';
echo '<br> $SCRIPT = '.$SCRIPT;
print "<pre>";
echo '<br> $_POST<br>';
print_r($_POST);
echo '<br> $_SESSION<br>';
print_r($_SESSION);
echo '<br>  $_FILES<br>';
print_r($_FILES);
print "</pre>";
}
//----------------------------------------------------------------------------------------------------------------------------------------
echo '<main_list>';
$sql_task='SELECT task.TASK_ID';
$from="FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)";
$WHERE='';
$WHERE_MAIN='';
$GROUP='GROUP BY task.TASK_ID';
$ORDER='';
$WHERE_LIKE_TASK = '';
$WHERE_LIKE_DESCRIPTION = '';
$WHERE_STATUS='';

if (isset ($_POST['SORT']) and ($_POST['SORT'])=='NUMB') {$ORDER='ORDER BY task.TASK_ID';}
if (isset ($_POST['SORT']) and ($_POST['SORT'])=='PRIO') {$sql_task=$sql_task.', task.TASK_PRIORITY'; $GROUP=$GROUP.', task.TASK_PRIORITY'; $ORDER='ORDER BY task.TASK_PRIORITY';}
if (isset ($_POST['SORT']) and ($_POST['SORT'])=='TERM') {$sql_task=$sql_task.', task.TASK_PLAN_END'; $GROUP=$GROUP.', task.TASK_PLAN_END'; $ORDER='ORDER BY task.TASK_PLAN_END';}

if (isset ($_POST['ORDER']) AND (($_POST['ORDER'])=='ON')) {$ORDER=$ORDER.' DESC';}

// ----  Сбока условий поиска -----
if (isset ($_POST['WAIT'])){
    if($WHERE_STATUS!=''){$WHERE_STATUS=$WHERE_STATUS." OR ((task.TASK_STATUS)='E')";
    } else {
    $WHERE_STATUS="((task.TASK_STATUS)='E')";
    }}

if (isset ($_POST['ARCHIVE'])){
    if($WHERE_STATUS!=''){$WHERE_STATUS=$WHERE_STATUS." OR ((task.TASK_STATUS)='A')";
    } else {
        $WHERE_STATUS="((task.TASK_STATUS)='A')";
    }}

if (isset ($_POST['WORK'])){
    if($WHERE_STATUS!=''){$WHERE_STATUS=$WHERE_STATUS." OR ((task.TASK_STATUS)='W')";
    } else {
        $WHERE_STATUS="((task.TASK_STATUS)='W')";
    }}

if (isset ($_POST['CANCEL'])){
    if($WHERE_STATUS!=''){$WHERE_STATUS=$WHERE_STATUS." OR ((task.TASK_STATUS)='C')";
    } else {
        $WHERE_STATUS="((task.TASK_STATUS)='C')";
    }}

if (isset ($_POST['PAUSE'])){
    if($WHERE_STATUS!=''){$WHERE_STATUS=$WHERE_STATUS." OR ((task.TASK_STATUS)='P')";
    } else {
        $WHERE_STATUS="((task.TASK_STATUS)='P')";
    }}

if($WHERE_STATUS!=''){$WHERE_STATUS='('.$WHERE_STATUS.')';
    $WHERE_MAIN=$WHERE_STATUS;
}


//-----------------------------------------------------------------------------------
if (isset ($_POST['STATUS'])) {if (($_POST['STATUS'])!='ALL') {
    if ($_POST['STATUS']=='A') {$stat="='A'";} else {$stat="<>'A'";}
    if($WHERE_MAIN!=''){
    $WHERE_MAIN=$WHERE_MAIN." AND ((task.TASK_STATUS)".$stat.")";
    } else {
        $WHERE_MAIN="((task.TASK_STATUS)".$stat.")";
    }}}
//------------------------------------------------------------------------------------

if (isset ($_POST['RESPONSIBLE'])) {if (($_POST['RESPONSIBLE'])!='ALL') {
    if($WHERE_MAIN!=''){
        $WHERE_MAIN=$WHERE_MAIN." AND ((task.PERSON_RESPONSIBLE)='".$_POST['RESPONSIBLE']."')";
    } else {
        $WHERE_MAIN="((task.PERSON_RESPONSIBLE)='".$_POST['RESPONSIBLE']."')";
    }}
}
if (isset ($_POST['WORKSHOP'])) {if (($_POST['WORKSHOP']) != 'ALL') {
    if ($WHERE_MAIN != '') {
            $WHERE_MAIN = $WHERE_MAIN . " AND ((task.WORKSHOP)='" .$_POST['WORKSHOP']. "')";
        } else {
               $WHERE_MAIN = "((task.WORKSHOP)='" .$_POST['WORKSHOP']. "')";
            }

    }
}
if (isset ($_POST['AREA'])) {if (($_POST['AREA']) != 'ALL') {
    if($WHERE_MAIN!='') {
       $WHERE_MAIN = $WHERE_MAIN . " AND ((task.WORKSHOP_AREA)='" . $_POST['AREA'] . "')";
        } else {
            $WHERE_MAIN = "((task.WORKSHOP_AREA)='" . $_POST['AREA'] . "')";

        }
    }
}


if (isset($_POST['SERCH_TEXT']) and ($_POST['SERCH_TEXT'])!='') {
    $serch_words = explode(' ', $_POST['SERCH_TEXT']);
    foreach ($serch_words as $key => $value) {
        if (($WHERE_LIKE_TASK) != '') {
            $WHERE_LIKE_TASK = $WHERE_LIKE_TASK . " Or ((task_language.TASK_NAME) Like '%" . $value . "%')";
            $WHERE_LIKE_DESCRIPTION = $WHERE_LIKE_DESCRIPTION . " Or ((task_language.TASK_DESCRIPTION) Like '%" . $value . "%')";
        } else {
            $WHERE_LIKE_TASK = "((task_language.TASK_NAME) Like '%" . $value . "%')";
            $WHERE_LIKE_DESCRIPTION = "((task_language.TASK_DESCRIPTION) Like '%" . $value . "%')";
        }
    }
}

if ($WHERE_MAIN != '') {
    if (($WHERE_LIKE_TASK) != '') {
        $WHERE='WHERE (('.$WHERE_MAIN.' AND '.$WHERE_LIKE_TASK.') OR ('.$WHERE_MAIN.' AND '.$WHERE_LIKE_DESCRIPTION.'))';
    } else {
        $WHERE='WHERE ('.$WHERE_MAIN.')';
    }
} else {
    if (($WHERE_LIKE_TASK) != '') {
        $WHERE='WHERE (('.$WHERE_LIKE_TASK.') OR ('.$WHERE_LIKE_DESCRIPTION.'))';
    } else {
        $WHERE='';
    }

}

//------------------- СБОРКА SQL ----------------------------------------------------------------------------------------------------
$sql_task=$sql_task.' '.$from.' '.$WHERE.' '.$GROUP.' '.$ORDER.';';
//if (($_SESSION['logged_user']) == $otladchik) { echo '<br> $sql_task ='.$sql_task.'<br>';}

$quer_task=mysqli_query($mylink['link'], $sql_task) or die ("Ошибка загрузки индексов.<br>".mysqli_error($mylink['link']));
while ($assoc_task = mysqli_fetch_assoc($quer_task)){ // Запуск перебора выбранных $assoc_task['TASK_ID']

//------------------- ВЫБОРКА ИНФО О TASK  ----------------------------------------------------------------------------------------------------
$sql_taskinfo="SELECT task.TASK_ID, task.SUB_TASK, task_language.LANGUAGE, task.TASK_STATUS, task.WORKSHOP, task.WORKSHOP_AREA, task.TASK_SETED, task.PERSON_SET_TASK, task.PERSON_RESPONSIBLE, task.TASK_PRIORITY, task.TASK_START, task.TASK_PLAN_END, task.TASK_END, task.TASK_FOLDER, task_language.TASK_NAME, task_language.TASK_DESCRIPTION
FROM task INNER JOIN task_language ON (task.SUB_TASK = task_language.SUB_TASK) AND (task.TASK_ID = task_language.TASK_ID)
WHERE (((task.TASK_ID)='".$assoc_task['TASK_ID']."') AND ((task.SUB_TASK)=0) AND ((task_language.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
$quer_taskinfo=mysqli_query($mylink['link'], $sql_taskinfo) or die ("Ошибка загрузки информации о задаче ID = ".$assoc_task['TASK_ID']."<br>".mysqli_error($mylink['link']));
$taskinfo = mysqli_fetch_assoc($quer_taskinfo);

//------------------- РАСШИФРОВКА  ----------------------------------------------------------------------------------------------------
//Загрузка справочников
$workshop = get_workshop(); //Массив цехов
$work_area=get_area();
//$status = get_status($cur_date);

//------------------------------------  ЛИСТИНГ -----------------------------------------------------------------------


//  ------------------------------------ ВЫВОД ИНФОРМАЦИИ О ЗАДАЧЕ ----------------------------------------------------
    echo '<div align="center">';
    echo '<table border width="99%">';

    echo '<tr>';
    echo '<td width="182px" align="center" valign="top">';

    //---------------------- ЗАГРУЗКА ИЗОБРАЖЕНИЯ ---------------------------------------------------  
    $sql_img="SELECT Max(task_biblioteka.DOC_ID) AS MaxOfDOC_ID, task_biblioteka.FILE_TYPE, biblioteka.FILE, task_biblioteka.FILE_NAME
    FROM biblioteka INNER JOIN task_biblioteka ON biblioteka.BIBLIOTEKA_ID = task_biblioteka.BIBLIOTEKA_ID
    WHERE (((task_biblioteka.TASK_ID)='".$taskinfo['TASK_ID']."') AND ((task_biblioteka.SUB_TASK)='0') AND ((task_biblioteka.CODE_DOC_TYPE)='".$foto_cod."'));";
    $query_img = mysqli_query($mylink['link'], $sql_img) or die ("Ошибка загрузки фото <br>".mysqli_error($mylink['link']));
    $img = mysqli_fetch_assoc($query_img);
      
    if (($img['MaxOfDOC_ID']) != 0) {
            //    echo '<a href="data:'.$img['FILE_TYPE'].';base64, '.base64_encode($img['FILE']).'" target="_blank">';
        echo '<img src = "data:'.$img['FILE_TYPE'].';base64, '.base64_encode($img['FILE']).'" width="180px" vertical-align="middle"';
     //    echo '</a>'; 
    } else {                   
       // echo ' <a href="IMG/task_drawing_board.png" target="_blank">';
    echo ' <img src="IMG/task_drawing_board.png" width="180px" vertical-align="middle"';
    //        echo '</a>'; 
    }
    //----------------------------------------------------------------------------------





    echo '</td>';
    echo '<td valign="top">';


    echo '<table border valign="top" width="100%">';

    echo '<tr>';
    echo '<td class="Back_H1 FC_W FSB24" align="center" width="100px">&nbsp;'.$taskinfo['TASK_ID'].'&nbsp;';
    echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
    echo '</td>';
    $tasklen=strlen($taskinfo['TASK_NAME']);
    if ($tasklen<95) {$th='"Back_H1 FC_W FSB20"';} else {
        if ($tasklen<115) {$th='"Back_H1 FC_W FSB18"';} else {$th='"Back_H1 FC_W FSB16"';}}
    echo '<td colspan="3" class='.$th.'>&nbsp;'.$taskinfo['TASK_NAME'].'</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="Back_H2 FC_W FS18">&nbsp;'.$taskinfo['TASK_DESCRIPTION'].'</td>';
    echo '</tr>';
          
if (($taskinfo['WORKSHOP'])==''){$WORKSHOP='';} else {$WORKSHOP=$workshop[$taskinfo['WORKSHOP']];}
if (($taskinfo['WORKSHOP_AREA'])==''){$AREA='';} else {$AREA=$work_area[$taskinfo['WORKSHOP_AREA']];}
    echo '<tr>';
    echo '<td nowrap colspan="2" width="10%" class="Back_H3 FC_W FS16">&nbsp;'.$WORKSHOP.'</td>';
    echo '<td class="Back_H3 FC_W FS16">&nbsp;'.$AREA.'</td>';
    echo '<td class="Back_H3 FC_W FS16" width="100px" align="right">&nbsp;</td>';
    echo '</tr>';

/*
    echo '<tr>';
    echo '<td nowrap colspan="2" width="10%" class="Back_H3 FC_W FS16">&nbsp;'.$workshop.'</td>';
    echo '<td class="Back_H3 FC_W FS16">&nbsp;'.$area.'</td>';
    echo '<td class="Back_H3 FC_W FS16" width="100px" align="right">&nbsp;</td>';
    echo '</tr>';
*/
    echo '</table>';

    echo '<table border valign="top" width="100%">';

    echo '<tr>';
    echo '<td nowrap align="right" width="180px" class="FS12">&nbsp;'.$lang_interface['id_task_set_date'].'&nbsp;</td>';
    echo '<td align="center" width="100px" class="FS12">&nbsp;'.date_my2ru($taskinfo['TASK_SETED']).'&nbsp;</td>';
    echo '<td nowrap align="right" width="160px" class="FS12">&nbsp;'.$lang_interface['id_task_priority'].'&nbsp;</td>';
    echo '<td align="center" width="160px" class="'.prior_color($taskinfo['TASK_PRIORITY']).'">&nbsp;'.$taskinfo['TASK_PRIORITY'].'&nbsp;'.prior_name($taskinfo['TASK_PRIORITY']).'</td>';
    echo '<td nowrap align="right" width="240px" class="FS12">&nbsp;'.$lang_interface['id_task_set'].'&nbsp;</td>';
    echo '<td align="left" width="140px" class="FS12">&nbsp;'.get_FIO($taskinfo['PERSON_SET_TASK']).'&nbsp;</td>';
    echo '<td nowrap rowspan="4" align="center" valign="middle" width="35%" class="FS12">';

//=============================================  ТАБЛИЦА КНОПОК ========================================================

        echo '<table width="100%">';
        echo '<tr>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_task.php" target="TASK" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit" name="TASK_VIEW">';
        echo '<img src="IMG/task_view.png" height="40px" alt="View task" title="'.$lang_interface['id_view_task'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_task.php" target="TASK"  enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit" name="TASK_EDIT">';
        echo '<img src="IMG/task_edit.png" height="40px" alt="Edit task" title="'.$lang_interface['id_edit_task'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_3d_model.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/3D_model.png" height="40px" alt="3D_model" title="'.$lang_interface['id_3D_model'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_drawing.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/Blueprint.png" height="40px" alt="drawing" title="'.$lang_interface['id_blueprints'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_documentation.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/Documentation.png" height="40px" alt="production" title="'.$lang_interface['id_doc_inf'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';


    echo '<td width="5%" align="center">';
    echo '</td>';

    echo '<td width="25%" align="center">';
    echo '</td>';

        echo '</tr>';
        echo '<tr>';

        echo '</td>';
        echo '<td width="5%" align="center">';
        echo '<form action="it_consider.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/consider.png" height="40px" alt="Consider" title="'.$lang_interface['id_consideration'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_reference.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/Reference.png" height="40px" alt="reference" title="'.$lang_interface['id_reference'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_photography.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/Photography.png" height="40px" alt="production" title="'.$lang_interface['id_foto_video'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="5%" align="center">';
        echo '<form action="it_sub_contractor.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/Sub_contractor.png" height="40px" alt="production" title="'.$lang_interface['id_customized'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '</td>';
        echo '<td width="5%" align="center">';
        echo '<form action="it_notes.php" target="_blank" enctype="multipart/form-data" method="post">';
        echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
        echo '<input type="hidden" name="SUB_TASK" value="'.$taskinfo['SUB_TASK'].'">';
        echo '<button type="submit">';
        echo '<img src="IMG/Notepad.png" height="40px" alt="Notepad" title="'.$lang_interface['id_notes'].'">';
        echo '</button>';
        echo '</form>';
        echo '</td>';

        echo '<td width="20%" align="center">';
        echo '</td>';

        echo '</tr>';
        echo '</table>';


//======================================================================================================================
    echo '</td>';
    echo '</tr>';
/*
    echo '<table width="100%" >';
    echo '<tr>';
    echo '<td width="100%" align="center">';
    echo '<form id="edit_task" action="it_task.php" enctype="multipart/form-data" method="post" target="_blank">';
    echo '<input type="hidden" name="TASK_ID" value="'.$taskinfo['TASK_ID'].'">';
    echo '<button type="submit" value="TASK_EDIT" name="action">';
    echo '<img src="IMG/task_edit.png" height="50px" alt="Task_edit" title="Редактировать задачу">';
    echo '</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
*/

    echo '<tr>';
    echo '<td nowrap align="right" class="FS12">&nbsp;'.$lang_interface['id_task_start'].'&nbsp;</td>';
    echo '<td nowrap align="center" class="FS12">&nbsp;'.date_my2ru($taskinfo['TASK_START']).'&nbsp;</td>';
    echo '<td nowrap align="right" class="FS12">&nbsp;'.$lang_interface['id_task_stat'].'&nbsp;</td>';
    //$taskinfo['TASK_STATUS']
    echo '<td nowrap align="center" width="140px" class="'.status_color($taskinfo['TASK_STATUS']).'">&nbsp;'.status_name($taskinfo['TASK_STATUS']).'</td>';
//    echo '<td align="center" width="140px" class="'.status_color($taskinfo['TASK_STATUS']).'">&nbsp;'.$taskinfo['TASK_STATUS'].'&nbsp;-&nbsp;'.status_name($taskinfo['TASK_STATUS']).'</td>';
//    echo '<td nowrap align="center" class="FS12">&nbsp;'.$status[$taskinfo['TASK_STATUS']].'&nbsp;</td>';

    echo '<td nowrap align="right" class="FS12">&nbsp;'.$lang_interface['id_task_respons'].'&nbsp;</td>';
    echo '<td nowrap align="left" class="FS12">&nbsp;'.get_FIO($taskinfo['PERSON_RESPONSIBLE']).'&nbsp;</td>';
 //   echo '<td align="right" class="FS12">&nbsp;&nbsp;</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td nowrap align="right" class="FS12">&nbsp;'.$lang_interface['id_task_plan_end'].'&nbsp;</td>';
    echo '<td nowrap align="center" class="FS12">&nbsp;'.date_my2ru($taskinfo['TASK_PLAN_END']).'</td>';
    echo '<td nowrap rowspan="2" colspan="3" width="280px" align="center" class="FS12">';

?>

<script type="text/javascript">
    $(function() {
        // copy content to clipboard
        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
        }

        // copy test to clipboard
        $(".folder-btn").on("click", function() {
            copyToClipboard("#folder");

        });
    });

</script>
<?php

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td align="center"><p class="folder" id="folder">'.$taskinfo['TASK_FOLDER'].'</p></td>';
    echo '<td width="5%" align="center"><button class="folder-btn"><img src="IMG/work_folder.png" height="50px" alt="Work folder" title="Скопировать в буфер. НЕ РАБОТАЕТ"></button></td>';
    echo '</tr>';
    echo '</table>';

    echo '</td>';

//    echo '<td rowspan="2" align="Left" class="FS12">&nbsp;&nbsp;</td>';
//    echo '<td align="right" class="FS12">&nbsp;&nbsp;</td>';
//    echo '<td rowspan="2" colspan="2" align="center" class="FS12">';

//    echo '<button type="submit" value="NOTE" name="action"><table width="100%"><tr><td align="center"><img src="IMG/Notepad.png" height="50px" alt="Notepad"></td>
//    <td align="center">&nbsp;Примечания&nbsp;</td></tr></table></button>';
    echo '</td>';

//    echo '<td rowspan="2" align="center" class="FS12">&nbsp;';

//    echo '</td>';
    echo '</tr>';



    echo '<tr>';
    echo '<td align="right" class="FS12">&nbsp;'.$lang_interface['id_task_end'].'&nbsp;</td>';
    echo '<td align="center" class="FS12">&nbsp;'.date_my2ru($taskinfo['TASK_END']).'&nbsp;</td>';
//    echo '<td align="right" class="FS12">&nbsp;&nbsp;</td>';
//    echo '<td align="Left" class="FS12">&nbsp;&nbsp;</td>';
//    echo '<td align="right" class="FS12">&nbsp;&nbsp;</td>';
//    echo '<td align="Left" class="FS12">&nbsp;&nbsp;</td>';
//    echo '<td align="right" class="FS12">&nbsp;&nbsp;</td>';
    echo '</tr>';

    echo '</table>';

    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';



// Не используется
// .$lang_interface['id_task_numb']






/*

        echo '<td align="center" class="FS12" width="300px">';
        echo '&nbsp;'.$lang_interface['id_work_folder'].'Ссылка';
        echo '</td>';
        echo '<td rowspan="2" align="left" class="FS12" width="300px">';
        echo '&nbsp;'.$lang_interface['id_contraktor'].'&nbsp;<br>&nbsp;&nbsp;Название подрядчика';
        echo '</td>';
        echo '<td rowspan="2" align="left" class="FS12" width="240px">';
        echo '&nbsp;'.$lang_interface['id_task_invest'].'&nbsp;<br>&nbsp;&nbsp;№ инвестиции&nbsp;';
        echo '</td>';

        echo '<td rowspan="3" align="center" class="FS12">';
        echo '&nbsp;'.$lang_interface['id_code_drow'].'&nbsp;<br><b>&nbsp;UKRC000.000.000.00&nbsp;<br>&nbsp;главная сборка&nbsp;</b>';
        echo '</td>';

        echo '<td  rowspan="2" align="left" class="FS12">';
        echo '&nbsp;'.$lang_interface['id_cont_pers'].'<BR>&nbsp;&nbsp;Подрядчик И.О.';
        echo '</td>';
        echo '<td align="left" class="FS12">';
        echo '&nbsp;'.$lang_interface['id_cont_send'].' 01/01/2021';
        echo '</td>';

        echo '<td align="left" class="FS12">';
        echo '&nbsp;'.$lang_interface['id_cont_back'].'&nbsp;01/01/2021';
        echo '</td>';
*/

}


echo '</div>';
echo '</main_list>';
echo '</body>';
echo '</html>';
?>
