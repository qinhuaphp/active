<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Jury_model extends Base_model{
	protected $table='jury';
	protected $pk='jid';
	/*
		分页，查询
		param:$pagesize (int),每页显示的条数
		param:$offset (int),偏移量
	*/
	function get_jury_cate($pagesize=10,$offset=4,$where=array()){
		$field='jury.jid,jury.username,jury.telephone,jury.realname,jury.emailbox,jury.pseudonym,jury.is_normal,jury.addtime,cate.cate_name';
		
		$res['jurys']=$this->db->select($field)->from($this->table)->join('cate','cate.cate_id=jury.cate_id','left outer')->where($where)
		->limit($pagesize,$offset)->order_by('jury.addtime','desc')->get()->result_array();
		
		$res['total']=$this->db->select('jury.jid,jury.username,jury.telephone,jury.realname,jury.pseudonym,jury.is_normal,jury.addtime')
		->from($this->table)->where($where)->get()->num_rows();
		return $res;
	}
}
?>