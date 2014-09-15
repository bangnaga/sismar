<div id="page-wrapper">
   <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Privilege Setup - <span style="color:#428bca"><?php echo $jabatan;?><span></b>
                </div>
                <!-- /.panel-heading -->
                 <div class="panel-body">
                    <div class="table-responsive">
                     <form id="form1" method="post" action="<?php echo base_url();?>jabatan/privilege/save">
                          <input type="hidden" name="jabatan_id" value="<?php echo $id;?>">
                          <table id="data-table" class="table table-bordered table-hover" >
                           <thead>
                            <tr>
                                <th>Modul<br/>&nbsp;</th>
                                <th>View<br><input value="1" type="checkbox" onClick="checkAll('View', this.checked)"></th>
	                            <th>New<br><input value="1" type="checkbox" onClick="checkAll('Add', this.checked)"></th>
	                            <th>Edit<br><input value="1" type="checkbox" onClick="checkAll('Edit', this.checked)"></th>
	                            <th>Delete<br><input value="1" type="checkbox" onClick="checkAll('Remove', this.checked)"></th>
                                <th>Cetak<br><input value="1" type="checkbox" onClick="checkAll('Cetak', this.checked)"></th>
	                            
                            </tr>
                            </thead>
                            <tbody>
                              <?php echo $tr;?>
                            </tbody>
                       </table>
                       <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                        <a href="<?php echo base_url();?>jabatan" class="btn btn-danger"><i class="fa fa-times"></i> Cancel</a>
                    
                      </form>
                   </div>
                   <!-- /.table-responsive -->
                     <div class="pull-right">
                        <ul class="pagination"></ul>    
                     </div> 
                     
                       
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div><!-- /#page-wrapper -->
<script>
    
    function checkAll(type, condition)
	{	
		
		formz = document.forms['form1'];
		len = formz.elements.length;
		for( i=0 ; i<len ; i++)
		{
			if(formz.elements[i] && formz.elements[i].type == 'checkbox')
			{
				if(formz.elements[i].id==type)formz.elements[i].checked=condition;
			}
		}
	}

</script>
