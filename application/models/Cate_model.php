<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cate_model extends Base_model{
	protected $table='cate';
	protected $pk='cate_id';
	/*查询子孙树*/
	function get_tree($pid=0,$lev=0){
		static $data=array();
		static $tree=array();
		if(empty($data)){
			$data=$this->fetch_all();
		}
		foreach($data as $v){
			if($v['pid'] == $pid){
				$v['lev']=$lev;
				$tree[]=$v;
				$this->get_tree($v['cate_id'],$lev+1);
			}
		}
		return $tree;
	}
	/*查询家谱树*/
	function get_famtree($cate_id){
		static $data=array();
		static $famtree=array();
		if(empty($data)){
			$data=$this->fetch_all();
		}
		foreach($data as $v){
			if($v['cate_id'] == $cate_id){
				array_unshift($famtree,$v);
				if($v['pid'] > 0){
					$this->get_famtree($v['pid']);
				}
			}
		}
		return $famtree;
	}
	/*查询指定分类的下级分类*/
	function get_sub($cate_id){
		 return $this->fetch_all(array('pid'=>$cate_id),'cate_id,cate_name,cate_introl,pid');
	}
}
?>