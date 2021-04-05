<?php
require ("../../inc/base_con.inc");
require ("../../inc/date.inc");
require ("../../inc/translate.inc");
require ("../../login/access.inc");
session_start();
global  $mylink;
$mylink=con_my();
// $mylink['base'];
// $mylink['host'];
$SCRIPT=basename($_SERVER['SCRIPT_NAME']);
$cur_date=curdate_ms();

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
//$access=ACCESS($user_id);

$sql_taskP="SELECT dc_priority.CODE_PRIORITY, dc_priority.LANGUAGE, dc_priority.NAME, dc_priority.DESCRIPTION
FROM dc_priority
WHERE (((dc_priority.LANGUAGE)='".$_SESSION['LANGUAGE']."'));";
$quer_taskP=mysqli_query($mylink['link'], $sql_taskP) or die ("Ошибка загрузки языковой схемы приоритетов.<br>".mysqli_error($mylink['link']));
while ($assoc_taskP = mysqli_fetch_assoc($quer_taskP)){
$cod=$assoc_taskP['CODE_PRIORITY'];
$NAME[$cod]=$assoc_taskP['NAME'];
$DESCRIPTION[$cod]=$assoc_taskP['DESCRIPTION'];
}

echo '<!DOCTYPE html>
<html lang="'.$html_lang.'">
<head>
<meta charset="utf-8">
<title>PRIORITY</title>
<link rel="stylesheet" href="../../css/'.$_SESSION['COLOR_SCHEME'].'.css"  type="text/css"/>
</head>';

echo '<body>';
echo '<main>';
echo '<div align="center">';
echo '<div class="DB_back FC_W FSB22 msgw500 shadow cirkle">'.$lang_interface['id_head'].'</div>';
echo '</div>';
echo '<div align="center">';



echo '<br>';
echo '<br>';
echo '<table border width="70%">';

echo '<tr>';
echo '<td width="10%" class="FSB18" align="center">&nbsp;'.$lang_interface['id_task'].'&nbsp;</td>';
echo '<td width="45%" class="FSB18" align="center" valign="middle">&nbsp;'.$lang_interface['id_urgent'].'&nbsp;</td>';
echo '<td width="45%" class="FSB18" align="center" valign="middle">&nbsp;'.$lang_interface['id_noturgent'].'&nbsp;</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="FSB18" align="center">&nbsp;'.$lang_interface['id_important'].'&nbsp;</td>';
echo '<td class="backR_textW FSB18 warp" align="left" valign="top">&nbsp;P0 '.$NAME['P0'].'<br><p>&nbsp;'.$DESCRIPTION['P0'].'</p></td>';
echo '<td class="backO_textW FSB18 warp" align="left" valign="top">&nbsp;P1 '.$NAME['P1'].'<br><p>&nbsp;'.$DESCRIPTION['P1'].'</p></td>';
echo '</tr>';

echo '<tr>';
echo '<td class="FSB18" align="center">&nbsp;'.$lang_interface['id_notimportant'].'&nbsp;</td>';
echo '<td class="textHB FSB18 warp" align="left" valign="top">&nbsp;P2 '.$NAME['P2'].'<br><p>&nbsp;'.$DESCRIPTION['P2'].'</p></td>';
echo '<td class="textHG FSB18 warp" align="left" valign="top">&nbsp;P3 '.$NAME['P3'].'<br><p>&nbsp;'.$DESCRIPTION['P3'].'</p></td>';
echo '</tr>';

echo '</table>';
echo '<br>';
echo '<input type="button" value="Закрыть" onclick="self.close()">';
echo '</div>';


echo '</main>';
echo '</body>';
echo '</html>';
?>