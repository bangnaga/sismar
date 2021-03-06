<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lampu extends CI_Controller{
	
	private $_mod_url;
	
	public function __construct(){
		
		parent::__construct();
		if(!$this->session->userdata('is_login'))redirect('login');
		if(!$this->general->privilege_check(LAMPU,'view'))
		    $this->general->no_access();
		$this->session->set_userdata('menu','product');
		$this->load->model('lampu_model');
		$this->_mod_url = base_url().'product/lampu';
		$this->load->helper('form');
	}
	
	private function _render($view,$data = array()){
	    
	    $this->load->view('header',$data);
	    $this->load->view('sidebar');
	    $this->load->view($view,$data);
	    $this->load->view('footer');
	}
	
	public function index(){
	  
	    $data = array('title'=>'Lampu - Tekno Power');
	    $this->_render('lampu',$data);
	}
	
	public function get_data(){
	    	    
	    $limit = $this->config->item('limit');
	    $offset= $this->uri->segment(4,0);
	    $q     = isset($_POST['q']) ? $_POST['q'] : '';	    
	    $data  = $this->lampu_model->get_data($offset,$limit,$q);
	    $rows  = $paging = '';
	    $total = $data['total'];
	    
	    if($data['data']){
	        
	        $i= $offset+1;
	        $j= 1;
	        foreach($data['data'] as $r){
	            
	            $rows .='<tr>';
	                
	                $rows .='<td>'.$i.'</td>';
	                $rows .='<td width="15%">'.$r->sku.'</td>';
	                $rows .='<td width="20%">'.$r->deskripsi.'</td>';
	                $rows .='<td width="5%">'.$r->derajat.'</td>';
	                $rows .='<td width="7%">'.$r->fitting.'</td>';
	                $rows .='<td width="7%">'.$r->daya.'</td>';
	                $rows .='<td width="7%">'.$r->tegangan.'</td>';
	                $rows .='<td width="10%">'.$r->warna.'</td>';
	                $rows .='<td width="7%">'.$r->umur.'</td>';
	                $rows .='<td width="40%" align="center">';
	                
	                $rows .='<a title="Edit" class="a-success" href="'.$this->_mod_url.'/detail/'.$r->id_lamp.'">
	                            <i class="fa fa-lightbulb-o"></i> Detail
	                        </a> ';
	                $rows .='<a title="Edit" class="a-warning" href="'.$this->_mod_url.'/edit/'.$r->id_lamp.'">
	                            <i class="fa fa-pencil"></i> Edit
	                        </a> ';
	                $rows .='<a title="Delete" class="a-danger" href="'.$this->_mod_url.'/delete/'.$r->id_lamp.'">
	                                <i class="fa fa-times"></i> Delete
	                            </a> ';
	                
	               $rows .='</td>';
	            
	            $rows .='</tr>';
	            
	            ++$i;
	            ++$j;
	        }
	        
	        $paging .= '<li><span class="page-info">Displaying '.($j-1).' Of '.$total.' items</span></i></li>';
            $paging .= $this->_paging($total,$limit);
	        	       	        
	    	    
	    }else{
	        
	        $rows .='<tr>';
	            $rows .='<td colspan="10">No Data</td>';
	        $rows .='</tr>';
	        
	    }
	    
	    echo json_encode(array('rows'=>$rows,'total'=>$total,'paging'=>$paging));
	}
	
	
	private function _paging($total,$limit){
	
	    $config = array(
                
            'base_url'  => $this->_mod_url.'/get_data/',
            'total_rows'=> $total, 
            'per_page'  => $limit,
			'uri_segment'=> 4
        
        );
        $this->pagination->initialize($config); 

        return $this->pagination->create_links();
	}
	
	private function _select_jns(){
	    
	    return $this->db->get('jns_prod_lampu')->result();
	}
	private function _select_tipe(){
	    return $this->db->get('tipe_lamp')->result();
	}
	private function _status($take=''){
	    
	    $status = array('A'=>'Active','C'=>'On Catalogue','D'=>'Discontinue');
	    if($take)
	        return $status[$take];
	    return $status;
	}
	private function _warranty(){
	    
	    $warranty = array('0','1','2','3','4','5');
	    return $warranty;
	}
	public function add(){
	    
	    if(!$this->general->privilege_check(LAMPU,'add'))
		    $this->general->no_access();
	    
	    $data = array('title'=>'Add Lampu - Tekno Power',
	                    'select_jns'=>$this->_select_jns(),
	                    'select_tipe'=>$this->_select_tipe(),
	                    'status'=>$this->_status(),
	                    'warranty'=>$this->_warranty()
	                    );
        $this->_render('add_lampu',$data);		
	    	   
	}
	public function detail(){
	    
	    $id = $this->uri->segment(4);
	    $seg5 = $this->uri->segment(5,0); //pass from katalog
	    if($seg5==='katalog-lampu'){
	        
	        $this->session->set_userdata('menu','katalog');
	    }
	    $detail = $this->lampu_model->get_detail($id);
	    $detail['status'] = $this->_status($detail['status']);
	    $data = array(
	                'title'=>'Detail Lampu - Tekno Power',
	                'detail'=>$detail
	            );
	    $this->_render('detail_lampu',$data);
	}
	public function edit(){
	    
	    if(!$this->general->privilege_check(LAMPU,'edit'))
		    $this->general->no_access();
	    	    
	    $id = $this->uri->segment(4);
	    $detail = $this->lampu_model->get_detail($id);
	    if(!$detail)
	        show_404();
	        
	    $data = array(
	                'title'=>'Edit Lampu - Tekno Power',
	                'select_tipe'=>$this->_select_tipe(),
	                'warranty'=>$this->_warranty(),
	                'status'=>$this->_status(),
	                'detail'=>$detail
	            );
        $this->_render('edit_lampu',$data);		
	    	   
	}
	
	public function save(){
	    
	    $data = $this->input->post(null,true);
	    $cek = $this->db->select('id_lamp')->where('sku',$data['sku'])
                            ->get('prod_lamp')->num_rows();
        //if already exists
        if($cek){
           
           redirect('product/lampu'); //smntara ajalah
           break;
        }
	    
        $flag=0;
        $rename_file = array();
        for($i=0;$i<count($_FILES['pic']['name']);$i++){
           
            if($_FILES['pic']['name'][$i]){
               
               $rename_file[$i] = 'pic'.($i+1).'_'.$_FILES['pic']['name'][$i];
               $flag++;
            }else{
                
                $rename_file[$i] = '';
            }
        }
        

        //if files are selected
        if($flag > 0){
            
           
            $this->load->library('upload');
            $this->upload->initialize(array(
                "file_name"     => $rename_file,
                'upload_path'   => './assets/images/lampu/',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size'      => '2000' //Max 2MB
            ));
            
            
		    if ($this->upload->do_multi_upload("pic")){
					
			    $info = $this->upload->get_multi_upload_data();
			    
			    foreach($info as $in){			
			       
			       $picx = substr($in['file_name'],0,4);
	               $data[$picx] = $in['file_name'];
	               
	            }
		    }
		    else{
		
			    
			    $error = array('error' => $this->upload->display_errors());
                echo "Errors Occured : "; //sini aja lah
                print_r($error);
			
		    }
	    }
	     
	  //print_r($data);exit;
	   $send = $this->lampu_model->save($data);
	   if($send)
	      redirect('product/lampu');
	}
	
	public function update(){
	    
	    $data = $this->input->post(null,true);
	    				
		$flag=0;
        $rename_file = array();
        for($i=0;$i<count($_FILES['pic']['name']);$i++){
           
            if($_FILES['pic']['name'][$i]){
               
              $rename_file[$i] = 'pic'.($i+1).'_'.trim($_FILES['pic']['name'][$i]);
               $flag++;
               
            }else{
                
                $rename_file[$i] = '';
            }
        }
              
        //if files are selected
        if($flag > 0){
            
            
            $this->load->library('upload');
            $this->upload->initialize(array(
                "file_name"     => $rename_file,
                'upload_path'   => './assets/images/lampu/',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size'      => '2000' //Max 2MB
            ));
            
            
		    if ($this->upload->do_multi_upload("pic")){
					
			    $info = $this->upload->get_multi_upload_data();
			    
			    foreach($info as $in){			
			       
			       $picx = substr($in['file_name'],0,4);
	               $data[$picx] = $in['file_name'];
	               
	            }
			   
		    }
		    else{
		
			    
			    $error = array('error' => $this->upload->display_errors());
                echo "Errors Occured : "; //sini aja lah
                print_r($error);
			
		    }
	    }

	  
	   $send = $this->lampu_model->update($data);
	   if($send)
	      redirect('product/lampu');
	}
	
	
	//remove files
	public function unlink(){
	
	    $data = $this->input->post(null,true);
	    
	    if(unlink('./assets/images/lampu/'.$data['img'])){
	        
	        $column = substr($data['img'],0,4); //pic1,pic2 etc...
	        $this->db->update('prod_lamp',array($column=>''),array('id_lamp'=>$data['id_lamp']));
	        
	       echo json_encode(array('status'=>true)); 
	    }
	    
	}
	
	public function delete(){
	
	    if(!$this->general->privilege_check(LAMPU,'remove'))
		    $this->general->no_access();
		
		$id = $this->uri->segment(4);
		$del = $this->lampu_model->delete($id);
		
		if($del)
		    redirect('product/lampu');
	}
	
    
  
}
