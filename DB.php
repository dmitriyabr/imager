<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfuntov
 * Date: 15.01.13
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */ 
class DB {

    private static $instance=null;

    /**
     * @return DB
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance=new DB();
        }
        return self::$instance;
    }

    public static function getMTime($path)
    {
        return self::getInstance()->_getMTime($path);
    }

    public static function setMTime($path, $mtime)
    {

        self::getInstance()->_setMTime($path, $mtime);
    }



    private $dbh=null;

    private $getMTimeCommand=null;
    private $setMTimeCommand=null;

    private function __construct()
    {
        $this->dbh=new PDO(
            Cfg::DB_DSN(),
            Cfg::DB_USER(),
            Cfg::DB_PASSWORD(),
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        $this->getMTimeCommand=$this->dbh->prepare(
            'SELECT mtime FROM image_resizer_pictures
             WHERE path=:path'
        );
        $this->setMTimeCommand=$this->dbh->prepare(
            'INSERT INTO image_resizer_pictures (path, mtime)
             VALUES (:path, :mtime)
             ON DUPLICATE KEY UPDATE mtime=:mtime'
        );
    }

    private function _getMTime($path)
    {
        $this->getMTimeCommand->execute(array('path'=>$path));
        $return=$this->getMTimeCommand->fetchAll();
        if(!isset($return[0][0])){
            $return=false;
        }else{
            $return=intval($return[0][0]);
        }

        //$this->getMTimeCommand->closeCursor();
        return $return;
    }

    private function _setMTime($path, $mtime)
    {

        $this->setMTimeCommand->execute(array('path'=>$path, 'mtime'=>$mtime));
    }

}
