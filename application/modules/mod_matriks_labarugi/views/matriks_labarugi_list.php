<div class="content">
    <?= form_open('mod_matriks_labarugi/to_excel',array('id' => 'filter')); ?>
     <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>Laporan Matriks Labarugi</span></h4></div>
                <div class="basic box_content">
                    
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span4">Pilih Periode</label>
                                <div class="span8 text">
										<?= form_dropdown('periode_year', $op_yearperiode, set_value('periode_year'), 'class="span8"'); ?>	
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span4">Pilih Periode Akunting</label>
                                <div class="span8 text">
										<div id="periode"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="mysubmit" class="btn btn-info"><i class="icon-ok-sign icon-white"></i>Submit</button>
                        <button type="button" class="btn"><i class="icon-remove"></i>Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table id="list2"></table>
    <div id="pager2"></div>
    <?= form_close(); ?>
</div>
<script type="text/javascript" src="<?= base_url(); ?>js/searching.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-4.4.1/src/grid.treegrid.js"></script>
<script type="text/javascript">
	function getDataProyek(id, id_proyek) {
        $.ajax({
            url: root + 'mod_matriks_labarugi/getDataProyek',
            type: 'post',
            data: { id: id, id_proyek:id_proyek},
            success: function(data) {
                $('select[name="kode_proyek"]').html(data);
            }
        });	
    }
    function daysInMonth(month, year) {
		return new Date(year, month, 0).getDate();
	}
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        jQuery("#list2").jqGrid({
            url: root + 'mod_matriks_labarugi/matriks_labarugi_json', 
            treeGrid: true,
			treeGridModel: 'adjacency',
			ExpandColumn: 'uraian',
			ExpandColClick: true,
			mtype : "post",
            datatype: "json", 
            colNames:['Group', 'Uraian', 'Bulan Ini', 'SD Bulan Ini'], 
            colModel:[ 
                {name:'nmlama',index:'nmlama', width:20},
                {name:'uraian',index:'uraian', width:100},
                {name:'total_ini',index:'total_ini', width:50, align:'right'}, 
                {name:'total_sd',index:'total_sd', width:50, align:'right'}],
            rowNum:0, 
            width: panjang, 
            height: lebar, 
            rownumbers: false, 
            rownumWidth: 40,
            rowList:[1,10,20,30], 
            pager: '#pager2', 
            multiselect: true,
			viewrecords: true,
			gridview: true,
			treeIcons: {leaf:'ui-icon-document'},
            sortorder: "desc"
            //caption:"List Users" 
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $('#mysubmit').click(function () {
            var str = $("form").serialize();
            var search = str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_matriks_labarugi/matriks_labarugi_json?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.cols_cari').val("");
            $('.ops_cari').val("");
            $('.text').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_matriks_labarugi/matriks_labarugi_json',
                page:1
            }).trigger("reloadGrid");
        });
        var id_unitkerja = $('select[name="unitkerja"]').val();
        var id_proyek = <?php
                        $id_proyek = set_value('kode_proyek');
                        if (!empty($id_proyek)) {
                            echo $id_proyek;
                        } else {
                            echo "0";
                        }
                        ?>;
        
        getDataProyek(id_unitkerja, id_proyek);
        $('select[name="unitkerja"]').change(function() {
            var id_unitkerja = $('select[name="unitkerja"]').val();
            getDataProyek(id_unitkerja, id_proyek);
        });
        
        for (i = new Date().getFullYear(); i >= 2007; i--)
		{
			$('#yearpicker').append($('<option />').val(i).html(i));
		}
		
		$("#form_matriks_labarugi_excel").click(function(){
			filter.submit();
		});
    });
    function getDataPeriode(id) {
        $.ajax({
            url: root + 'mod_matriks_labarugi/getDataPeriode',
            type: 'post',
            data: { id: id},
            success: function(data) {
                $('#periode').html(data);
            }
        });	
    }
    
    $('select[name="periode_year"]').change(function() {
        var id =  $('select[name="periode_year"]').val();
        getDataPeriode(id);
    });
    
    $(function() {
        var id_periodeyear = $('select[name="periode_year"]').val();
        getDataPeriode(id_periodeyear);
        
    });
</script>
