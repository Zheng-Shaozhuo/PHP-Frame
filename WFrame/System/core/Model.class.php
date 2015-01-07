<?php
	/*
	 *	2014年12月18日2:10:44
	 *  模型类
	 *	更新 2014年12月18日19:25:33
	 */
	class Model{
		private $table;		//表名
		private $where;		//where条件
		private $field;		//字段名
		private $order;		//order排序
		private $join;		//join链接
		private $on;		//连接条件
		private $limit;		//limit查询个数
		private $pageSize;	//分页个数
		private $pageNum;	//页码数

		private $db;		//mysqli对象
		
		/*
		 *	2014年12月18日13:12:48
		 *	构造函数
		 *	new对象的时候，可以传表名
		 *	$table: 表名
		 */
		function __construct($table = null){
			self::init();
			$this->table = $table;
			$this->db    = new Db();
			return $this;
		}

		/*
		 *	2014年12月18日13:13:44
		 *	传入表名
		 *	$table: 表名
		 */
		public function table($table = null){
			$this->table = $table;
			return $this;
		}

		/*
		 *	2014年12月18日13:17:58
		 *	传入where 条件
		 *	$where: 查询条件
		 */
		public function where($where = array()){
			$this->where = $where;
			return $this;
		}

		/*
		 *	2014年12月18日16:17:49
		 *	传入查询字段
		 * 	$field: 目标查询字段
		 */
		public function field($field = null){
			$this->field = $field;
			return $this;
		}

		/*
		 *	2014年12月18日15:48:01
		 *	传入排序
		 *	$order: 排序字段
		 */
		public function order($order = null){
			$this->order = $order;
			return $order;
		}

		/*	
		 *	2014年12月18日15:49:55
		 *	传入join
		 *	$join: 多表关联方式
		 */
		public function join($join = null){
			$this->join = in_array($join, array('innner', 'left', 'right')) ? $join : null;
			return $this;
		}

		/*
		 *	2014年12月18日15:52:06
		 *	传入on
		 *	$on: 多表关联条件
		 */
		public function on($on = null){
			$this->on = $on;
			return $this;
		}

		/*
		 *	2014年12月18日15:54:47
		 *	传入limit
		 *	$limit: limit查询个数
		 */
		public function limit($limit = null){
			$this->limit = $limit;
			return $this;
		}

		/*
		 *	2014年12月18日17:43:50
		 *	分页查询
		 *	$pageSize: page查询个数, $pageNum: page页码数
		 */
		public function page($pageSize = null, $pageNum = null){
			$this->pageSize = $pageSize;
			$this->pageNum	= $pageNum;
			return $this;
		}


		/*
		 *	2014年12月18日15:58:51
		 *	单条查询
		 *	$result_type: 返回值类型 assoc array
		 */
		public function find($result_type = 'assoc'){
			if(is_null($this->table)){
				return array();
			}
			$result = $this->db->fetchOne($this->table, $this->where, $this->field, $this->order, $this->join, $this->on, $result_type);
			$this->init();

			return $result;
		}

		/*
		 *	2014年12月18日16:24:55
		 *	多条查询
		 *	$result_type: 返回值类型 assoc array
		 **/
		public function select($result_type = 'assoc'){
			if(is_null($this->table)){
				return array();
			}
			$result = $this->db->fetchAll($this->table, $this->where, $this->field, $this->order, $this->join, $this->on, $this->limit, $this->pageSize, $this->pageNum, $result_type);
			$this->init();

			return $result;
		}

		/*
		 *	2014年12月18日20:02:46
		 *	返回分页个数
		 *
		 */
		public function getPages(){
			if(is_null($this->table) || is_null($this->pageSize)){
				return 0;
			}
			$num = ceil($this->db->getCount($this->table, $this->where, $this->field, $this->order, $this->join, $this->on) / $this->pageSize);
			$this->init();

			return $num;
		}

		/*
		 *	2014年12月18日16:27:03
		 *	查询是否存在
		 */
		public function check(){
			if(is_null($this->table)){
				return false;
			}
			$flag = $this->db->checkOne($this->table, $this->where, $this->field, $this->order, $this->join, $this->on);
			$this->init();

			return $flag;
		}

		/*
		 *	2014年12月18日16:33:51
		 *	查询目标个数
		 */
		public function count(){
			if(is_null($this->table)){
				return 0;
			}
			$num = $this->db->getCount($this->table, $this->where, $this->field, $this->order, $this->join, $this->on);
			$this->init();

			return $num;
		}

		/*
		 *	2014年12月18日19:11:02
		 *	插入数据
		 *	$data: 插入数据
		 */
		public function insert($data = array()){
			if(is_null($this->table)){
				return false;
			}
			if(!is_array($data) || empty($data)){
				return false;
			}
			$flag = $this->db->insert($this->table, $data);
			$this->init();

			return $flag;
		}

		/*
		 *	2014年12月18日19:20:20
		 *	更新数据
		 *	$date: 更新数据
		 */
		public function update($data){
			if(is_null($this->table)){
				return false;
			}
			if(!is_array($data) || empty($data)){
				return false;
			}
			$flag = $this->db->update($this->table, $data, $this->where);
			$this->init();

			return $flag;
		}

		/*
		 *	2014年12月18日19:23:08
		 *	删除数据
		 */
		public function delete(){
			if(is_null($this->table)){
				return false;
			}
			$flag = $this->db->delete($this->table, $this->where);
			$this->init();

			return $flag;
		}

		/*
		 *	2014年12月18日20:19:57
		 *	返回最近一条查询语句
		 */
		public function get_last_query(){
			return $this->db->get_last_query();
		}

		/*
		 *	2014年12月18日16:00:27
		 *	初始化类参数
		 */
		public function init(){
			$this->table 		= null;
			$this->where 		= null;
			$this->order 		= null;
			$this->join  		= null;
			$this->on    		= null;
			$this->limit 		= null;
			$this->pageSize  	= null;
			$this->pageNum		= null;	
		}
	}