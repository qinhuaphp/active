<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Node_model extends Base_model{
	protected $table='node';
	protected $pk='nid';
	function fetch_nodes($array,$pid=0){
		$nodes=array();
		//static $res=array();
/* 		if(empty($res)){
			$res=$this->fetch_all($where,'nid,nname,remark,level,pid');
		} */
		if(!empty($array)){
			$nodes=$this->fetch_tree($array,$pid);
			if(!empty($nodes)){
				foreach($nodes as $k=>$v){
					$subnode=$this->fetch_nodes($array,$v['nid']);
					if(!empty($subnode)){
						$nodes[$k]['subnode']=$subnode;
					}
				}
			}
		}
		//print_r($res);
		return $nodes;
	}
	function fetch_tree($array,$pid=0){
		$subnode=array();
		foreach($array as $k=>$v){
			if($v['pid']==$pid){
				$subnode[]=$v;
			}
		}
		//print_r($subnode);
		return $subnode;
	}
	function fetch_privliges($name){
		$nids=$this->db->select('admins.admin_name,admins.gid,admingroup.gname,admingroup.nid')->from('admingroup')->join('admins','admins.gid=admingroup.gid','left outer')
			->where(array('admins.admin_name'=>$name))->get()->row_array();
		$data['nid']=explode(',',$nids['nid']);
		$array=$this->db->select('nid,nname,remark,level,pid')->from($this->table)->where(array('level !='=>'3'))->where_in($this->pk,$data['nid'])->get()->result_array();
		
		$data['nodelist']=$this->fetch_nodes($array);
		// $nodelist['nids']=$nids['nid'];
		return 	$data;
	}
}
?>