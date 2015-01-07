<?php
	/*
	 *	2014年12月17日20:29:13
	 *	mysql类
	 *	更新 2014年12月18日0:28:06
	 */
	class Db{
		private $db_conf = array();
		private $mysqli;
		private $last_query;
		/*
		 *	2014年12月17日21:17:32
		 *	获取数据库配置常量
		 *	数据库连接
		 */
		function __construct(){
			$this->db_conf = require_once 'mysql.config.php';
			$this->mysqli = new mysqli($this->db_conf['DB_HOST'], $this->db_conf['DB_USER'], $this->db_conf['DB_PWD'], $this->db_conf['DB_PREFIX'].$this->db_conf['DB_NAME'], $this->db_conf['DB_PORT']);

			if (mysqli_connect_errno()) {
			    printf("Connect failed: %s\n", mysqli_connect_error());
			    exit();
			}
			$this->mysqli->set_charset($this->db_conf['DB_CHARSET']);
		}

		/*
		 *	2014年12月17日21:37:22
		 * 	数据库插入操作
		 *	$table：表名， $data: 插入数据
		 */
		public function insert($table, $data){
			$keys = join(",", array_keys($data));
			$vals = "'" . join("','", array_values($data)) . "'";
			$sql = "insert into {$table} ({$keys}) values({$vals})";
			$this->mysqli->query($sql);

			$this->last_query = $sql;
			return $this->mysqli->insert_id > 0 ? true : false;
		}

		/*
		 *	2014年12月17日22:07:54
		 *	数据库更新操作
		 *	$table: 表名， $data: 更新数据， $where: 更新条件
		 */
		public function update($table, $data, $where = null){
			$str = null;
			foreach ($data as $key => $value) {
				if($str == null){
					$sep = "";
				}else{
					$sep = ",";
				}
				$str .= $sep . $key . "='" . $value . "'";
			}
			$where = $this->dealWhere($where);
			$sql = "update {$table} set {$str}" . $where;
			$this->mysqli->query($sql);

			$this->last_query = $sql;
			return $this->mysqli->affected_rows > 0 ? true : false;
		}

		/*
		 *	2014年12月17日22:18:01
		 *	数据库删除操作
		 *	$table: 表名字， $where: 条件
		 */
		public function delete($table, $where = null){
			if($where == null){
				$this->truncate($table);
				return true;
			}else{
				$where = $this->dealWhere($where);
				$sql = "delete from {$table}" . $where;
				$this->mysqli->query($sql);

				$this->last_query = $sql;
				return $this->mysqli->affected_rows > 0 ? true : false;
			}
		}

		/*
		 *	2014年12月17日22:21:17
		 *	格式化数据表
		 *	$table: 表名
		 */
		private function truncate($table){
			$sql = "truncate table {$table}";

			$this->last_query = $sql;
			$this->mysqli->query($sql);
		}

		/*
		 *	2014年12月17日22:33:37
		 *	返回一条记录
		 *	$table: 表名，$where: 条件， $field: 字段, $result_type：返回类型
		 */
		public function fetchOne($table, $where = array(), $field = null, $order = null, $join = null, $on = null, $result_type = 'assoc'){
			$sql = $this->dealSql($table, $where, $field, $order, $join, $on) . "limit 1"; 
			if($sql == "limit 1"){
				return array();
			}

			$this->last_query = $sql;
			$result = $this->mysqli->query($sql);
			
			return $result ? ($result_type == 'assoc' ? $result->fetch_assoc() : $result->fetch_array()) : array();
		}

		/*
		 *	2014年12月17日22:51:53
		 *	返回多条查询记录
		 * 	$table: 表名， $where: 条件， $field：字段， $result_type: 返回值类型
		 */
		public function fetchAll($table, $where = array(), $field = null, $order = null, $join = null, $on = null, $limit = null, $pageSize = null, $pageNum = null, $result_type = 'assoc'){
			$sql = $this->dealSql($table, $where, $field, $order, $join, $on, $limit, $pageSize, $pageNum);
			if($sql == ""){
				return array();
			}

			$this->last_query = $sql;
			$result = $this->mysqli->query($sql);

			if(!$result){
				return array();
			}

			$rows = array();
			if($result_type == 'assoc'){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			}else{
				while($row = $result->fetch_array()){
					$rows[] = $row;
				}
			}

			return $rows;
		}

		/*
		 *	2014年12月17日23:46:45
		 *	检索该数据是否存在
		 *	$table: 表名， $where: 条件
		 */
		public function checkOne($table, $where = array(), $field = null, $order = null, $join = null, $on = null){
			$sql = $this->dealSql($table, $where, $field, $order, $join, $on); 
			if($sql == "limit 1"){
				return array();
			}

			$this->last_query = $sql;
			$result = $this->mysqli->query($sql);
			
			return $result ? ($result->num_rows == 1 ? true : false) : false;
		}

		/*
		 *	2014年12月18日0:06:27
		 *	返回结果数目
		 *	$table: 表名， $where: 条件
		 */
		public function getCount($table, $where = array(), $field = null, $order = null, $join = null, $on = null){
			$sql = $this->dealSql($table, $where, $field, $order, $join, $on); 
			if($sql == "limit 1"){
				return array();
			}

			$this->last_query = $sql;
			$result = $this->mysqli->query($sql);
			
			return $result ? $result->num_rows : 0;
		}

		/*
		 *	2014年12月17日23:55:49
		 *	返回最近一条查询sql
		 */
		public function get_last_query(){
			return $this->last_query;
		}

		/*
		 *	2014年12月18日0:11:54
		 *	进行事务操作
		 *	$trans: 数组，对应的值均为sql语句
		 */
		public function transaction($trans = array()){
			$this->mysqli->autocommit(FALSE);
			if(is_array($trans) && !empty($trans)){
				foreach ($trans as $k => $val) {
					$this->last_query .= "[{$k}] => {$val} \n";
					$this->mysqli->query($val);
				}
			}

			if($this->mysqli->commit()){
				return true;
			}else{
				$this->mysqli->rollback();
				return false;
			}
		}

		/*
		 *	2014年12月18日11:57:22
		 *	执行sql查询
		 *	$sql: 查询语句
		 */
		public function exec($sql){
			if(isset($sql) && !empty($sql)){
				$result = $this->mysqli->query($sql);

				return $result ? $result : array();
			}else{	
				return array();
			}
		}

		/*
		 *	2014年12月18日17:33:16
		 *	处理成sql语句
		 */
		public function dealSql($table, $where = array(), $field = null, $order = null, $join = null, $on = null, $limit = null, $pageSize = null, $pageNum = null){
			if(strpos($table, ',')){
				if(isset($join) && isset($on)){
					$tmp = explode(',', $table);
					$table = "";
					foreach($tmp as $v){
						if($table == ""){
							$table = trim($v) . " {$join} join (";
						}else{
							$table .= trim($v) . ",'";
						}
					}
					$table 	= substr($table, 0, strlen($table) - 2) . ")";
					
					$tmp = explode(',', $on);
					$on = "on (";
					foreach ($tmp as $v){
						$on .= trim($v) . ",";
					}
					$on = substr($on, 0, strlen($on) - 1) . ")";
				}else{
					return '';
				}
			}
			if(is_numeric($limit) && is_numeric($pageSize) && is_numeric($pageNum)){
				return '';
			}
			$where 		= $this->dealWhere($where);
			$field 		= isset($field) && !is_null($field) ? $field : "*";
			$order 		= isset($order) && !is_null($order) ? "order by {$order}" : "";
			$on 		= isset($on) && !is_null($on) ? $on : "";
			if(is_numeric($limit) && is_null($pageSize) && is_null($pageNum)){
				$limit 		= "limit " . intval($limit);
			}else if(is_null($limit) && is_numeric($pageSize) && is_numeric($pageNum)){
				$limit 		= "limit " . (intval($pageNum) - 1) * intval($pageSize) . "," . intval($pageSize);
			}else if(is_null($limit) && is_null($pageSize) && is_null($pageNum)){
				$limit		= "";
			}

			return "select {$field} from {$table} {$on} {$where} {$order} {$limit}";
		}

		/*
		 *	2014年12月17日23:33:23
		 *	处理查询条件where数组
		 */
		private function dealWhere($where){
			$str = " where ";
			if(isset($where) && is_array($where)){
				foreach($where as $key => $value){
					if($str == " where "){
						$sep = "";
					}else{
						$sep = ",";
					}
					if(is_array($value)){
						$tmpStr = "";
						switch ($value[0]) {
							case 'eq':	//等于
								$tmpStr = $key . " = '" . $value[1] . "'";
								break;
							case 'neq':	//不等于
								$tmpStr = $key . " <> '" . $value[1] . "'";
								break;
							case 'gt':	//大于
								$tmpStr = $key . " > '" . $value[1] . "'";
								break;
							case 'get':	//大于等于
								$tmpStr = $key . " >= '" . $value[1] . "'";
								break;
							case 'lt':	//小于
								$tmpStr = $key . " < '" . $value[1] . "'";
								break;
							case 'elt':	//小于等于
								$tmpStr = $key . " <= '" . $value[1] . "'";
								break;
							case 'like':
								$tmpStr = $key . " like '%" . $value[1] . "%'";
								break;
							case 'between':
								if(is_string($value[1]) && strpos($value[1], ',')){
									$d = explode(',', $value[1]);
									$tmpStr = $key . " between '" . $d[0] . "' and '" . (isset($d[1]) ? $d[1] : '') . "'";
								}else if(is_array($value[1])){
									$tmpStr = $key . " between '" . $value[1][0] . "' and '" . (isset($value[1][1]) ? $value[1][1] : '') . "'";
								}
								break;
							case 'not between':
								if(is_string($value[1]) && strpos($value[1], ',')){
									$d = explode(',', $value[1]);
									$tmpStr = $key . " not between '" . $d[0] . "' and '" . (isset($d[1]) ? $d[1] : '') . "'";
								}else if(is_array($value[1])){
									$tmpStr = $key . " not between '" . $value[1][0] . "' and '" . (isset($value[1][1]) ? $value[1][1] : '') . "'";
								}
								break;
							case 'in':
								if(is_string($value[1]) && strpos($value[1], ',')){
									$d = explode(',', $value[1]);
									$in = "('";
									foreach ($d as $v) {
										$in .= $v . "','";
									}
									$in = substr($in, 0, strlen($in) - 2) . ")";
									$tmpStr = $key . " in " . $in;
								}else if(is_array($value[1])){
									$in = "('";
									foreach ($value[1] as $v) {
										$in .= $v . "','";
									}
									$in = substr($in, 0, strlen($in) - 2) . ")";
									$tmpStr = $key . " in " . $in;
								}
								break;
							case 'not in':
								if(is_string($value[1]) && strpos($value[1], ',')){
									$d = explode(',', $value[1]);
									$in = "('";
									foreach ($d as $v) {
										$in .= $v . "','";
									}
									$in = substr($in, 0, strlen($in) - 2) . ")";
									$tmpStr = $key . " not in " . $in;
								}else if(is_array($value[1])){
									$in = "('";
									foreach ($value[1] as $v) {
										$in .= $v . "','";
									}
									$in = substr($in, 0, strlen($in) - 2) . ")";
									$tmpStr = $key . " not in " . $in;
								}
								break;
							default:
								return "";
								break;
						}
						$str .= $sep . $tmpStr;
					}else{
						if(strpos($key, ">") || strpos($key, ">=") || strpos($key, "<>") || strpos($key, "<") || strpos($key, "<=") || strpos($key, "between") || strpos($key, "in")){
							return '';
						}
						if($value == null || $value == 'null'){
							$str .= $sep . $key . " is " . $value;
						}else{
							$str .= $sep . $key . "='" . $value . "'";
						}
					}
				}
			}

			return $str == " where " ? '' : $str;
		}
	}