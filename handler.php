// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем наличие файла в массиве $_FILES
    if (isset($_FILES['fileTz']) && $_FILES['fileTz']['error'] === UPLOAD_ERR_OK) {

        // Получаем данные о загруженном файле
        $fileTmpPath = $_FILES['fileTz']['tmp_name'];
        $fileName = $_FILES['fileTz']['name'];
        $fileSize = $_FILES['fileTz']['size'];
        $fileType = $_FILES['fileTz']['type'];

        // Заголовки для отправки письма
        $subject = 'Файл от пользователя'; // Тема письма
        $message = 'Пожалуйста, найдите прикрепленный файл.'; // Текст сообщения

        // Читаем содержимое файла и кодируем его в base64
        $fileContent = chunk_split(base64_encode(file_get_contents($fileTmpPath)));
        $uid = md5(date('r', time())); // Генерируем уникальный идентификатор для разделителей

        // Заголовки для формата multipart/mixed
        $headers = "MIME-Version: 1.0";
        $headers .= "Content-Type: multipart/mixed; boundary=$uid\r\n \r\n";

        // Тело сообщения
        $headers .= "--$uid\r\n";
        $headers .= "Content-type:text/plain; charset=\"utf-8\"\r\n";
        $headers .= "Content-Transfer-Encoding: 7bit\r\n \r\n";
        $headers .= $message . "\r\n \r\n";
        
        // Прикрепляем файл
        $headers .= "--$uid\r\n";
        $headers .= "Content-Type: application/octet-stream; name=\"$fileName\"\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";
        $headers .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n \r\n";
        $headers .= $fileContent;
        $headers .= "--$uid--";

        // Отправляем на почту
        if (mail($to, $subject, '', $headers)) {
            echo 'ok';
        } else {
            echo 'no';
        }
    } else {
        echo 'Ошибка: файл не был загружен.';
    }
} else {
    echo 'Ошибка: некорректный запрос.';
}
