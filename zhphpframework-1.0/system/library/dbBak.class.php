<?php
class dbBak {
    var $_mysql_link_id;
    var $_dataDir;
    var $_tableList;
    var $_TableBak;

    function DbBak($_mysql_link_id,$dataDir)
    {
        ( (!is_string($dataDir)) || strlen($dataDir)==0) && die('error_log:$datadir is not a string');
        !is_dir($dataDir) && mkdir($dataDir);
        $this->_dataDir = $dataDir;
        $this->_mysql_link_id = $_mysql_link_id;
    }

    function backupDb($dbName,$tableName=null)
    {
        ( (!is_string($dbName)) || strlen($dbName)==0 ) && die('$dbName must be a string value');
//step1:选择数据库：
        mysql_select_db($dbName);
//step2:创建数据库备份目录
        $dbDir = $this->_dataDir.DIRECTORY_SEPARATOR.$dbName;
        !is_dir($dbDir) && mkdir($dbDir);
//step3:得到数据库所有表名 并开始备份表
        $this->_TableBak = new TableBak($this->_mysql_link_id,$dbDir);
        if(is_null($tableName)){//backup all table in the db
            $back=$this->_backupAllTable($dbName);
            return $back;
        }
        if(is_string($tableName)){
            (strlen($tableName)==0) && die('....');
            $this->_backupOneTable($dbName,$tableName);
            return;
        }
        if (is_array($tableName)){
            foreach ($tableName as $table){
                ( (!is_string($table)) || strlen($table)==0 ) && die('....');
            }
            $this->_backupSomeTalbe($dbName,$tableName);
            return;
        }
    }

    function restoreDb($dbName,$tableName=null){
        ( (!is_string($dbName)) || strlen($dbName)==0 ) && die('$dbName must be a string value');
//step1:检查是否存在数据库 并连接：
        @mysql_select_db($dbName) || die("the database <b>$dbName</b> dose not exists");
//step2:检查是否存在数据库备份目录
        $dbDir = $this->_dataDir.DIRECTORY_SEPARATOR.$dbName;
        !is_dir($dbDir) && die("$dbDir not exists");
//step3:start restore
        $this->_TableBak = new TableBak($this->_mysql_link_id,$dbDir);
        if(is_null($tableName)){//backup all table in the db
            $back=$this->_restoreAllTable($dbName);
            return $back;
        }
        if(is_string($tableName)){
            (strlen($tableName)==0) && die('....');
            $this->_restoreOneTable($dbName,$tableName);
            return;
        }
        if (is_array($tableName)){
            foreach ($tableName as $table){
                ( (!is_string($table)) || strlen($table)==0 ) && die('....');
            }
            $this->_restoreSomeTalbe($dbName,$tableName);
            return;
        }
    }

    function _getTableList($dbName)
    {
        $tableList = array();
        $result=mysql_query("show tables from $dbName");
        //$result=mysql_list_tables($dbName,$this->_mysql_link_id);
        for ($i = 0; $i < mysql_num_rows($result); $i++){
            array_push($tableList,mysql_tablename($result, $i));
        }
        mysql_free_result($result);
        return $tableList;
    }

    function _backupAllTable($dbName)
    {
        foreach ($this->_getTableList($dbName) as $tableName){
            $back=$this->_TableBak->backupTable($tableName);
        }
        return $back;
    }

    function _backupOneTable($dbName,$tableName)
    {
        !in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");
        $this->_TableBak->backupTable($tableName);
    }

    function _backupSomeTalbe($dbName,$TableNameList)
    {
        foreach ($TableNameList as $tableName){
            !in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");
        }
        foreach ($TableNameList as $tableName){
            $this->_TableBak->backupTable($tableName);
        }
    }

    function _restoreAllTable($dbName)
    {
//step1:检查是否存在所有数据表的备份文件 以及是否可写：
        foreach ($this->_getTableList($dbName) as $tableName){
            $tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR
                . $dbName.DIRECTORY_SEPARATOR
                . $tableName.DIRECTORY_SEPARATOR
                . $tableName.'.sql';
            !is_writeable ($tableBakFile) && die("$tableBakFile not exists or unwirteable");
        }
//step2:start restore
        foreach ($this->_getTableList($dbName) as $tableName){
            $tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR
                . $dbName.DIRECTORY_SEPARATOR
                . $tableName.DIRECTORY_SEPARATOR
                . $tableName.'.sql';
            $back=$this->_TableBak->restoreTable($tableName,$tableBakFile);

        }
        return $back;
    }

    function _restoreOneTable($dbName,$tableName)
    {
//step1:检查是否存在数据表:
        !in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");
//step2:检查是否存在数据表备份文件 以及是否可写：
        $tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR
            . $dbName.DIRECTORY_SEPARATOR
            . $tableName.DIRECTORY_SEPARATOR
            . $tableName.'.sql';
        !is_writeable ($tableBakFile) && die("$tableBakFile not exists or unwirteable");
//step3:start restore
        $this->_TableBak->restoreTable($tableName,$tableBakFile);
    }
    function _restoreSomeTalbe($dbName,$TableNameList)
    {
//step1:检查是否存在数据表:
        foreach ($TableNameList as $tableName){
            !in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");
        }
//step2:检查是否存在数据表备份文件 以及是否可写：
        foreach ($TableNameList as $tableName){
            $tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR
                . $dbName.DIRECTORY_SEPARATOR
                . $tableName.DIRECTORY_SEPARATOR
                . $tableName.'.sql';
            !is_writeable ($tableBakFile) && die("$tableBakFile not exists or unwirteable");
        }
//step3:start restore:
        foreach ($TableNameList as $tableName){
            $tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR
                . $dbName.DIRECTORY_SEPARATOR
                . $tableName.DIRECTORY_SEPARATOR
                . $tableName.'.sql';
            $this->_TableBak->restoreTable($tableName,$tableBakFile);
        }
    }
}