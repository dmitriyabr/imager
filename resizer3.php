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

include 'Resizer3.php';
include 'Picture3.php';

function resize_recursive($dir_name, $rs, $config){
    foreach(scandir($dir_name) as $inner){
        if($inner[0]==='.')
            continue;
        $whole_path=$dir_name.DIRECTORY_SEPARATOR.$inner;
        if(is_dir($whole_path))
            resize_recursive($whole_path, $rs, $config);
        elseif(is_file($whole_path) && (substr($inner,-4)==='.jpg'||substr($inner,-5)==='.jpeg')){
            $s_mtime = filemtime($whole_path);
            $t_path = str_replace($config['SOURCE_DIR'],$config['TARGET_DIR'],$whole_path);
            if(file_exists($t_path)){
                echo "Already exists. ";
                $t_mtime = filemtime($t_path);
                if ($t_mtime == $s_mtime) {
                    echo "Same mtime. SKIPPING\n";
                    continue;
                } else {
                    echo "Mtime changed! ";
                }
            }
            $pic=new Picture(imagecreatefromjpeg($whole_path),str_replace($config['SOURCE_DIR'],'',$whole_path), $s_mtime);
            $rs->resize($pic)->save($config['TARGET_DIR']);

        }

    }
}

$config=parse_ini_file('config3.ini');

ini_set('error_log',$config['LOG_DIR'].date('Y-m-d').'_error.log');
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen($config['LOG_DIR'].date('Y-m-d').'.log', 'ab');
$STDERR = fopen($config['LOG_DIR'].date('Y-m-d').'_stderror.log', 'ab');
echo 'Started at '.date('Y-m-d H:i:s')."\n";
$rs=new Resizer(array('red'=>255,'green'=>255,'blue'=>255));
resize_recursive($config['SOURCE_DIR'],$rs, $config);
echo 'Ended at '.date('Y-m-d H:i:s')."\n";
