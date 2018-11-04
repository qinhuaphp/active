<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customermng extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
		$this->load->model('customer_model','customer');
	}
	function addcustomer(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('cu_name','客服名称','trim|required');
		$this->form_validation->set_rules('cu_qq','客服QQ','trim|required|numeric|is_unique[customer.cu_qq]');
		$this->form_validation->set_rules('cu_remark','备注','trim|required');
		if($this->form_validation->run() == FALSE){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addcustomer.html');
		}else{
			$data['cu_name']=trim($this->input->post('cu_name',true));
			$data['cu_qq']=trim($this->input->post('cu_qq',true));
			$data['cu_remark']=trim($this->input->post('cu_remark',true));
			$res=$this->customer->add($data);
			if($res){
				$dat['tip']='添加客服成功';
			}else{
				$dat['tip']='添加客服失败';
			}
			$dat['time']='1600';
			$this->load->view('admin/sysinfo.html',$dat);
		}
		//$this->output->enable_profiler(true);
	}
	function customerlist(){
		$data['res']=$this->customer->fetch_all(0,'cu_id,cu_name,cu_qq');
		//print_r($data['res']);
		$this->load->view('admin/customerlist.html',$data);
		//$this->output->enable_profiler(true);
	}
	function editcustomer(){//编辑模板不设置重新填充
		$this->load->model('node_model','node');
		$curent=$this->node->fetch_one('nid',array('nname'=>'admin/customermng/editcustomer'));
		if(in_array($curent['nid'],$_SESSION['admin']['nids'])){
			$cu_id=$this->uri->segment(4)+0;
			$data['row']=$this->customer->fetch_row('cu_id,cu_name,cu_qq,cu_remark',$cu_id);
			//print_r($data['row']);
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cu_name','客服名称','trim|required');
			$this->form_validation->set_rules('cu_qq','客服QQ','trim|required|numeric|max_length[15]|is_unique[customer.cu_qq]');
			$this->form_validation->set_rules('cu_remark','备注','trim|required');
			if($this->form_validation->run() == FALSE){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/editcustomer.html',$data);
			}else{
				$cu_id=$this->input->post('cu_id',true);
				$dat['cu_name']=trim($this->input->post('cu_name',true));
				$dat['cu_qq']=trim($this->input->post('cu_qq',true));
				$dat['cu_remark']=trim($this->input->post('cu_remark',true));
				$res=$this->customer->renew($cu_id,$dat);
				if($res > 0){
					$da['tip']='编辑客服成功';
				}else{
					$da['tip']='编辑客服失败';
				}
				$da['time']='1600';
				$this->load->view('admin/sysinfo.html',$da);
			}
			$this->output->enable_profiler(false);
		}else{
			$da['tip']='请联系管理员开通此权限';
			$da['time']='1800';
			$this->load->view('admin/sysinfo.html',$da);
		}
	}
}
?>