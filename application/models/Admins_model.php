<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admins_model extends Base_model{
	protected $table='admins';
	protected $pk='admin_id';
	function admin_group(){
		$field='admins.admin_id,admins.admin_name,admins.created_at,admins.is_normal,admingroup.gname';
		return $this->db->select($field)->from($this->table)->join('admingroup','admingroup.gid=admins.gid','left outer')->order_by('created_at','desc')->get()->result_array();
	}
}
?>