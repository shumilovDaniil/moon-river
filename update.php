<?php set_time_limit(0);
header('Content-Type: text/html; charset=utf-8');

if (!extension_loaded('zip')) {
    die('Ошибка: Расширение ZIP не установлено на вашем хостинге.<br>Включите его или попросите это сделать поддержку хостинга.');
}
if (!extension_loaded('curl')) {
    die('Ошибка: Расширение CURL не установлено на вашем хостинге.<br>Включите его или попросите это сделать поддержку хостинга.');
}

if (isset($_GET['source'])) {
    $source_site = 'moon-river'; // Название домена на Webflow без окончания webflow.io

    $source_path = dirname($_GET['source']);
    $source_file = $source_path . '/' . $source_site . '.zip';

    if (dirname(substr($source_path, strpos($source_path, 'webflow-converter.ru'))) === 'webflow-converter.ru' ||
        dirname(substr($source_path, strpos($source_path, 'digital-gid.ru'))) === 'digital-gid.ru') {
        $output_file = basename($source_file);

        curl_download($source_file, $output_file);

        $zip = new ZipArchive;
        $zip->open($output_file);
        $zip->extractTo('./');
        $zip->close();

        unlink($output_file);

        echo '<script>history.back();</script>';
    } else {
        die('Обновление невозможно. Проверьте настройку source_site в update.php!');
    }
}

function curl_download($url, $file)
{
    $dest_file = @fopen($file, "w");
    $resource = curl_init();
    curl_setopt($resource, CURLOPT_URL, $url);
    curl_setopt($resource, CURLOPT_FILE, $dest_file);
    curl_setopt($resource, CURLOPT_HEADER, 0);
    curl_setopt($resource, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
    curl_setopt($resource, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
    curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
    if (!curl_exec($resource)) {
        die(curl_error($resource));
    }
    curl_close($resource);
    fclose($dest_file);
}
