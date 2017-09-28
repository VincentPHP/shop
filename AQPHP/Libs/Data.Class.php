<?php
/* +----------------------------------------------------------------
 * | Software: [AQPHP framework]
 * |	 Site: www.aqphp.com
 * |----------------------------------------------------------------
 * |   Author: 赵 港 < admin@gzibm.com | 847623251@qq.com >
 * |   WeChat: GZIDCW
 * |   Copyright (C) 2015-2020, www.aqphp.com All Rights Reserved.
 * +----------------------------------------------------------------*/
namespace AQPHP\Libs;
/**
 * 数据库操作类
 * @author Vincent
 */

Class Data{
    
    /**
     * 数据库连接
     * @var String $mysqli 数据库连接
     */
    protected $mysqli;  //
    
    /**
     * 实例化的表名
     * @var String $table 表名
     */
    protected $table;   //
    
    /**
     * 选项
     * @var unknown $opt 选项
     */
    protected $opt;     
    
    
    /**
     * 构造方法
     * @param array $tab_name 用户提供的表
     */
    function __construct($tab_name) {
        
        $this->config($tab_name);
        
    }
    
    /**
     * 配置方法
     * @param string $tab_name 表名
     */
    protected function config($tab_name)
    {
        $this->table = C('DBFIX').$tab_name;

        $this->Data = new \mysqli(C('DBHOST'), C('DBUSER'), C('DBPWD'), C('DBNAME'));
        
        if(mysqli_connect_errno()){
            
            echo "数据库连接错误" . mysqli_connect_errno();
            exit();
          
        }
        
        $this->Data->query("SET NAMES 'UTF8");
        
        $this->opt['field'] = "*";
        
        $this->opt['where'] = $this->opt['order'] = $this->opt['limit'] = $this->opt['group'] = '';  
       
    }
    
    /**
     * 获取表名
     * @return string 
     */
    function tbFields()
    {
        
        $result = $this->Data->query("DESC {$this->table}");
        
        $fieldArr = array();
        
        while (($row = $result->fetch_assoc()) != FALSE )
        {
            $fieldArr[] = $row['Field'];    
        }
        
        return $fieldArr;

    } 
    
    /**
     * SQL Field 获得查询的字段
     * @param String or Array $field 需要的查询的字段名 可以是字符串或数组
     */
    function field($field)
    {
        $fieldArr = is_string($field) ? explode( ",", $field ) : $field ;   //如果是字符串以逗号分割为数组
        
        if(is_array($fieldArr))
        {    
            $field = '';        //定义空数组 接收循环组合的数据
            
            foreach($fieldArr as $v)
            {
                $field .= "`" . $v . "`" . ","; //组合需要的格式 多一个,号
            }
        }
        
        $this->opt['field'] = rtrim( $field, ',' );//截取多余的，号，并保存到OPT里 
        
        return  $this->opt['field'];

    }
    
    
    /**
     * 数据 数组转换为字符串格式 同时进行转义
     * @param Array $values 数组转换为字符串
     */
    protected function values($value)
    {
    	
    	if( !get_magic_quotes_gpc() )
    	{
    		$strValue = '';		
    		
    		foreach( $value as $v )
    		{
    			$strValue .= "'" . addslashes( $v ) . "',";	
    		}  		
    	}
    	else 
    	{
			foreach( $value as $v )
			{
				$strValue .= "'$v',";
			}
		}
		
    	return rtrim( $strValue );
    	
    }
    
    /**
     * SQL条件查询 WHERE
     * @param String Or Array $where 查询的条件 可以是字符串或数组
     */
    function where($Text, $Where, $Value)
    {
    	$WhereData = is_array($Text) ? $Text : $Text.$Where."'{$Value}'";  
    	 
        $this->opt['where'] = "WHERE ".$WhereData;//如果传参的内容为字符串直接返回 不是则为空
        return $this;
    }
    
    /**
     * SQL LIMIT 分页语句
     * @param String $limit 分页条件
     */
    function limit($limit)
    {
        $this->opt['limit'] = is_string($limit) ? "LIMIT " . $limit : '' ;
        return $this;
    }
    
    /**
     * 排序 ORDER
     * @param String $order 排序
     */
    function order($order)
    {
        $this->opt['order'] = is_string($order) ? "ORDER BY " . $order : '' ;
        return $this;
    }
    
    /**
     * 分组 GROUP 
     * @param String $group 分组
     */
    function group($group)
    {
        $this->opt['group'] = is_string($group) ? "GROUP BY " . $group : '' ;
        return $this;
    }
    
    /**
     * 查询 SELECT
     */
    function select()
    {
        //组合SQL语句
        $sql = "SELECT {$this->opt['field']} FROM {$this->table} {$this->opt['where']} {$this->opt['group']} {$this->opt['limit']} {$this->opt['order']}";
        return $this->sql($sql);
    }
    
    /**
     * 获取单条数据
     * @param String $id 需要获取的数据的id
     */
    function find()
    {
        $sql = "SELECT {$this->opt['field']} FROM {$this->table} {$this->opt['where']}";
        
		$result = $this->Data->query($sql) or die($this->dbError());
		
        $row = $result->fetch_assoc();
		
        return $row;   
    }
    
    /**
     * 删除 DELETE
     * @param String $id 需要查找的id
     */
    function delete( $id = '' )
    {
        if($id == '' && empty($this->opt['where']))
        {  
            die('查询的条件不能为空');
        }
            
        if($id != '')
        {
            if(is_array($id))
            {
                $id = implode(',', $id);
            }
            
            $this->opt['where'] = "WHERE id IN (" . $id . ")";

        }
         
        $sql = "DELETE FROM {$this->table} {$this->opt['where']} {$this->opt['limit']}";
		
        return $this->query($sql);
    }
    
    
    /**
     * INSERT 添加数据
     * @param Array $args 需要添加数据  类型数组
     */
    function insert($args)
    {
    	is_array($args) or die('参数非数组');
		
        $fidles = $this->field( array_keys($args) );

        $values = rtrim( $this->values( array_values($args) ),',');

        $sql = "INSERT INTO {$this->table}($fidles) values($values)";

    	if( $this->query($sql) > 0 )
        {
    		return $this->Data->insert_id;	
    	}

		return FALSE;
    }
    
    /**
     * UPDATE 更新方法
     * @param Array $args 需要更新的数据内容 类型为数组
     */
    function update($args)
    {
    	is_array( $args ) or die('参数非数组'); 
    	
    	if(empty($this->opt['where'])) die('条件不能为空');
    	
    	$set = '';
    	
    	$gpc = get_magic_quotes_gpc();
    	
    	foreach ($args as $v=>$k)
    	{    	    
    		$v = !$gpc ? addslashes($v) : $v ; //转义处理	
    		$set .= "`{$v}`='".$k."',";
    	}
    	
		$set = rtrim($set,','); 		
		$sql = "UPDATE {$this->table} SET $set {$this->opt['where']}";

    	return $this->query($sql);
    }
    
    /**
     * count 分页查询
     * @param String $tabname 需要查询的表
     */
    function count($tabname='')
    {
		$tabname = $tabname=='' ? $this->table : $tabname ;
		
		$sql = "SELECT 'id' FROM {$tabname} {$this->opt['where']}";    	
		
		return $this->query($sql);
    	
    }
    
    
    
    /**
     * 没有结果集
     * @param String $sql 执行的SQL语句
     */
    function query($sql)
    {
        $this->Data->query( $sql ) or die( $this->dbError() );
        return $this->Data->affected_rows;
    }
    
    
    
    /**
     * 发送SQL返回结果集
     * @param String $sql 执行的SQL语句
     */
    function sql($sql)
    {
        $result = $this->Data->query($sql) or die($this->dbError());
        
        $resultArr = array();
        
        while (($row = $result->fetch_assoc()) != FALSE)
        {
            $resultArr[] = $row;  
        }
	
        return $resultArr;
    }
    
    /**
     * 返回错误信息 Error
     * @return String 返回错误信息
     */
    function dbError()
    {
        return $this->Data->error;
    }
    
}