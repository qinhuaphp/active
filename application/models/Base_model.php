<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Base_model extends CI_Model{
	protected $table='';
	protected $pk='';
	function __construct(){
		parent::__construct();
	}
	function tablename(){
		return $this->table;
	}
	function getpk(){
		return $this->pk;
	}
	/*入库
	params:一维数组
	*/
	function add($data){
		return $this->db->insert($this->table,$data);
	}
	/*批量入库
	param:二维数组
	*/
	function add_batch($data){
		return $this->db->inser_batch($this->table,$data);
	}
	//更新
	function renew($id,$data){
		$this->db->where($this->pk,$id)->update($this->table,$data);
		return $this->db->affected_rows();
	}
	/*批量更新
		param:tablename(string)
		param:data (多维数组,字段名=>值)
		param:key(表中的字段名称,string)
		return:受影响的行数
	*/
	function renew_batch($array,$key){
		return $this->db->update_batch($this->table,$array,$key);
	}
	//查询所有数据
	function fetch_all($where=0,$fields=''){
		if($where==0 && $fields==''){
			return $this->db->get($this->table)->result_array();
		}
		if(is_array($where) && $fields!=''){
			return $this->db->select($fields)->from($this->table)->where($where)->get()->result_array();
		}
		if($where==0 && $fields!=''){
			return $this->db->select($fields)->from($this->table)->get()->result_array();
		}
	}
	//依据主键查询一行数据
	function fetch_row($fields,$id){
		return $this->db->select($fields)->from($this->table)->where($this->pk,$id)->get()->row_array();
	}
	//查询单个字段的数据
	function fetch_one($fields,$where){
		return $this->db->select($fields)->from($this->table)->where($where)->get()->row_array();
	}
	/*
		生成用于多条件复合查询的where条件数组
		传入的是含有空单元的where数组
		返回的是不含空单元的数组
	*/
	function filter($array){
		static $arr=array();
		foreach($array as $k=>$v){
			if(!empty($v) or $v!=0){
				$arr[$k]=$v;
			}
		}
		return $arr;
	}
	/*
	加密
	param:$password (string)
	*/
	function encrypt($password){
		$security_code=rand();
		return array('password'=>md5(md5($password).$security_code),'security_code'=>$security_code);
	}
}
?>