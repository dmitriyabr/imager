<?php
/**
 * Created by IntelliJ IDEA.
 * User: kfuntov
 * Date: 14.02.13
 * Time: 13:21
 * To change this template use File | Settings | File Templates.
 */
$child_pid = pcntl_fork();
if ($child_pid) {
    // Выходим из родительского, привязанного к консоли, процесса
    exit();
}
// Делаем основным процессом дочерний.
posix_setsid();

include 'Resizer.php';
include 'Picture.php';
include 'DB.php';

function resize_recursive($dir_name,$rs, $config){
    foreach(scandir($dir_name) as $inner){
        if($inner[0]==='.')
            continue;
        $whole_path=$dir_name.DIRECTORY_SEPARATOR.$inner;
        if(is_dir($whole_path))
            resize_recursive($whole_path, $rs, $config);
        elseif(is_file($whole_path) && (substr($inner,-4)==='.jpg'||substr($inner,-5)==='.jpeg')){
            if(file_exists(str_replace($config['SOURCE_DIR'],$config['TARGET_DIR'],$whole_path))){
                echo "e\n";
                continue;
            }
            $pic=new Picture(imagecreatefromjpeg($whole_path),str_replace($config['SOURCE_DIR'],'',$whole_path),0);
            $rs->resize($pic)->save($config['TARGET_DIR'], false);

        }

    }
}

$config=parse_ini_file('config2.ini');

ini_set('error_log',$config['ERROR_LOG']);
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen($config['APPLICATION_LOG'], 'ab');
$STDERR = fopen($config['STDERR_LOG'], 'ab');
$rs=new Resizer($config['TARGET_WIDTH'],$config['TARGET_HEIGHT'],array('red'=>255,'green'=>255,'blue'=>255));
resize_recursive($config['SOURCE_DIR'],$rs, $config);
