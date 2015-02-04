<?php
/**
 * Created by PhpStorm.
 * User: lazar
 * Date: 28.12.14
 * Time: 16:18
 */

class yandexWeatherCache
{
    const CACHE_TYPE_DB = 1;
    const CACHE_TYPE_FS = 2;

    private $cacheType;
    private $tableName;
    private $cacheDir;
    private $conn;

    public function __construct($cacheType = self::CACHE_TYPE_FS, $cacheCfg)
    {
        $this->cacheType = $cacheType;

        switch ($cacheType)
        {
            case self::CACHE_TYPE_FS:
                $this->cacheDir = @is_dir($cacheCfg['dir']) ? @$cacheCfg['dir'] : sys_get_temp_dir();
                break;
            case self::CACHE_TYPE_DB:
                $host = @$cacheCfg['host'];
                $db = @$cacheCfg['db'];
                $user = @$cacheCfg['user'];
                $pass = @$cacheCfg['pass'];
                $tableName = @$cacheCfg['table'];

                $this->tableName = $tableName;

                $this->conn = @mysql_connect($host, $user, $pass) or die('Connect error: ' . mysql_errno());

                @mysql_query("CREATE DATABASE $db");
                @mysql_select_db($db) or die('Select BD error: ' . mysql_errno());

                $query = "CREATE TABLE IF NOT EXISTS $tableName(
                                id      VARCHAR(40) PRIMARY KEY UNIQUE,
                                value   MEDIUMTEXT,
                                time    TIMESTAMP,
                                INDEX   i_id(id(40)),
                                INDEX   i_time(time)
                          )";
                @mysql_query($query, $this->conn) or die('Database create error: ' . mysql_errno());
                break;
            default: return false;
        }
    }

    public function getCache($id, $ttl)
    {
        switch ($this->cacheType)
        {
            case self::CACHE_TYPE_DB:
                return $this->_getCacheDb($this->_getHash($id), $ttl);
                break;
            case self::CACHE_TYPE_FS:
                return $this->_getCacheFs($this->_getHash($id), $ttl);
                break;
            default: return false;
        }
    }

    public function setCache($id, $data)
    {
        switch ($this->cacheType)
        {
            case self::CACHE_TYPE_DB:
                $this->_setCacheDb($this->_getHash($id), $data);
                break;
            case self::CACHE_TYPE_FS:
                $this->_setCacheFs($this->_getHash($id), $data);
                break;
            default: return false;
        }
    }

    private function _getCacheDb($id, $ttl)
    {
        $query = "SELECT time, value
                  FROM {$this->tableName}
                  WHERE id = '$id'";
        $res = mysql_query($query, $this->conn);
        $row = mysql_fetch_assoc($res);

        return $this->_isExpired(strtotime($row['time']), $ttl) ? unserialize($row['value']) : false;
    }

    private function _setCacheDb($id, $data)
    {
        $data = mysql_real_escape_string(serialize($data));

        $query = "SELECT time
                  FROM {$this->tableName}
                  WHERE id = '$id'";
        $res = mysql_query($query, $this->conn);

        if (mysql_num_rows($res) > 0)
        {
            $query = "UPDATE {$this->tableName}
                      SET   value='$data',
                            time = NULL
                      WHERE id = '$id'";
        }
        else
        {
            $query = "INSERT INTO {$this->tableName}
                      SET
                            id = '$id',
                            value='$data'";
        }
        mysql_query($query, $this->conn) or die(mysql_error());
    }

    private function _getCacheFs($id, $ttl)
    {
        $cacheFile = $this->_getCacheFile($id);

        if (file_exists($cacheFile))
        {
            $data = unserialize(file_get_contents($cacheFile));
            if ($this->_isExpired($data['time'], $ttl) !== false)
            {
                return $data['value'];
            }
        }

        return false;
    }

    private function _setCacheFs($id, $data)
    {
        $cacheFile = $this->_getCacheFile($id);
        if (file_exists($cacheFile))
        {
            unlink($cacheFile);
        }

        $data = array(
            'value' =>  $data,
            'time'  =>  time(),
        );

        file_put_contents($cacheFile, serialize($data), LOCK_EX);
    }

    private function _getCacheFile($id)
    {
        return $this->cacheDir . '/' . 'yaw_' . $this->_getHash($id) . '.cache';
    }

    private function _getHash($id)
    {
        return sha1($id);
    }

    private function _isExpired($timestamp, $ttl)
    {
        return $timestamp + $ttl >= time() ? true : false;
    }
}

//TODO: Хранить TTL для каждой записи. Если TTL = 0, то запись всегда действительна
//TODO: Полная очистка кеша, с удалением таблицы