<html>
<head></head>

<body>



<?php

if ($handle = opendir($csv_files_path)) {

    $files = array();

    /* Esta é a forma correta de varrer o diretório */
    while (false !== ($file = readdir($handle))) {
        if (preg_match('/^.*\.csv$/',$file)) {
            $files[] = $file;
        }
    }

    closedir($handle);
}

if ($handle = opendir($log_files_path)) {

    $reports = array();

    /* Esta é a forma correta de varrer o diretório */
    while (false !== ($file = readdir($handle))) {
        if (preg_match('/^.*\.log$/',$file)) {
            $reports[] = $file;
        }
    }

    closedir($handle);
}

?>

<form action="process" method="GET">

<label>Selecione o arquivo:</label>

<select name="filename">

<?php foreach ($files as $file): ?>
    <option value="<?=$csv_files_path.$file?>"><?=$file?></option>
<?php endforeach; ?>
</select>


<input type="submit" value="Enviar" />

</form>

<?php
foreach ($reports as $report): ?>

<?php
echo '<a target="_blank" href="download_report?filename='.$report.'">'.$report.' </a></br>';
?>

<?php
endforeach;
?>

</body>
</html>

