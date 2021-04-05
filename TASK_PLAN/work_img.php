<?
//Запись изображения
// ----СЧЁТЧИК ИЗОБРАЖЕНИЙ ---------
    $foto_cod='0100'; // 0100 - Код Фото
    $FOTO_DOC_ID=DOC_ID_MAX($_POST['PERSON_ID'], $foto_cod);

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
    $limitWidth  = 4096;
    $limitHeight = 4096;

    // Проверим нужные параметры
    if (filesize($fileTmpName) > $limitBytes)     die($lang_interface['id_err_more80mb']);  // больше 80 мегабайт
    if ($imagesize[1] > $limitHeight)             die($lang_interface['id_err_limit_height']); // Высота >4096
    if ($imagesize[0] > $limitWidth)              die($lang_interface['id_err_limit_width']); // ширина >4096

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
// echo '<br> file_name = '.$file_name;
// echo '<br> file_type = '.$file_type;

$doc_cod='0100'; // 0100 - Код Фото
$DOC_ID=task_foto_max($TASK_ID, $SUB_TASK, $doc_cod);
$DOC_ID++;
$sql_rec="INSERT INTO enterprise.person_file (PERSON_ID, DOC_ID, FILE_TYPE, FILE, DOC_NAME, CODE_DOC_TYPE) 
VALUES ('".$_POST['PERSON_ID']."', ".$FOTO_DOC_ID.", '".$file_type."', '".$image_data."', '".$file_name."', '".$foto_cod."');";
mysqli_query($mylink['link'], $sql_rec) or die ("Ошибка записи изображения в базу <br>".mysqli_error($mylink['link']));

//    echo '<BR> Файл успешно записан в базу MYSQL !';
  }
 }
//----------- КОНЕЦ ЗАПИСЬ ИЗОБРАЖЕНИЯ ---------------------------------------------------------------







//---------------------------Загрузка ФОТО ----------------------------------
$sql_foto = "SELECT person_file.PERSON_ID, person_file.DOC_ID, person_file.FILE_TYPE, person_file.FILE, person_file.DOC_NAME, person_file.CODE_DOC_TYPE
FROM person_file
GROUP BY person_file.PERSON_ID, person_file.FILE_TYPE, person_file.FILE, person_file.DOC_NAME, person_file.CODE_DOC_TYPE
HAVING (((person_file.PERSON_ID)='".$_POST['PERSON_ID']."') AND ((person_file.DOC_ID)='".$FOTO_DOC_ID."') AND ((person_file.CODE_DOC_TYPE)='".$foto_cod."'));";
$query_foto = mysqli_query($mylink['link'], $sql_foto) or die ("Ошибка загрузки фото <br>".mysqli_error($mylink['link']));
echo '<tr><td rowspan="2" width="322px" align="center">';
//----------------------------------------------------------------------------------------------------------------------------------------
echo '<table width="100%" align="center">';
echo '<tr>';
echo '<td align="center">';
if (mysqli_num_rows($query_foto) == 0) {
echo '<img src="def_avatar.jpg" width="320px" alt="'.$lang_interface['id_foto'].'">';
} else {
$foto = mysqli_fetch_assoc($query_foto);
echo '<img src = "data:'.$foto['FILE_TYPE'].';base64,' . base64_encode($foto['FILE']) . '" width = "320px" alt = "'.$lang_interface['id_foto'].'"/>';
}
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td align="center">';

echo '<p><input type="file" name="image" multiple accept="image/*">';
echo '<input type="submit" value="'.$lang_interface['id_send'].'"></p>';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</td>';
//----------------------------------------------------------------------------------------------------------------------------------------









