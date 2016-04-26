<div class="content">
    <?= form_open('mod_matriks_neraca/to_excel',array('id' => 'filter')); ?>
     <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>Laporan Matriks Neraca</span></h4></div>
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
    
    <div class="row-fluid">
    <div class="span6">
		<table id="list2"></table>
		<div id="pager2"></div>
    </div>
    <div class="span6">
		<table id="list3"></table>
		<div id="pager3"></div>
    </div>
    </div>
    
    <?= form_close(); ?>
</div>
<script type="text/javascript" src="<?= base_url(); ?>js/searching.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-4.4.1/src/grid.treegrid.js"></script>
<script type="text/javascript">
	function getDataProyek(id, id_proyek) {
        $.ajax({
            url: root + 'mod_matriks_neraca/getDataProyek',
            type: 'post',
            data: { id: id, id_proyek:id_proyek},
            success: function(data) {
                $('select[name="kode_proyek"]').html(data);
            }
        });	
    }
    
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() / 2.08;
        jQuery("#list2").jqGrid({
            url: root + 'mod_matriks_neraca/matriks_neraca_assets_json', 
            treeGrid: true,
			treeGridModel: 'adjacency',
			ExpandColumn: 'uraian',
			ExpandColClick: true,
			mtype : "post",
            datatype: "json", 
            colNames:['Uraian', 'RP'], 
            colModel:[ 
                {name:'uraian',index:'uraian', width:100},
                {name:'total',index:'total', width:50, align:'right'}],
            rowNum:10, 
            width: panjang, 
            height: lebar, 
            rownumbers: false, 
            rownumWidth: 40,
            rowList:[1,10,20,30], 
            pager: '#pager2', 
            multiselect: true,
			viewrecords: true,
			gridview: true,
            footerrow : true, 
            userDataOnFooter : true, 
            altRows : true,
			treeIcons: {leaf:'ui-icon-document'},
            sortorder: "desc",
            gridComplete: function(){
                var rows = $("#list2").getDataIDs(); 
				for (var i = 0; i < rows.length; i++)
				{
					var uraian = $("#list2").getCell(rows[i],"uraian");
					if(uraian.indexOf("href") != -1)
					{
						$("#list2").jqGrid('setRowData',rows[i],false, {  color:'black',background:'lightgrey'});            
					}
				} 
            },
            caption:"Assets" 
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        jQuery("#list3").jqGrid({
            url: root + 'mod_matriks_neraca/matriks_neraca_ekuitas_json', 
            treeGrid: true,
			treeGridModel: 'adjacency',
			ExpandColumn: 'uraian',
			ExpandColClick: true,
			mtype : "post",
            datatype: "json", 
            colNames:['Uraian', 'RP'], 
            colModel:[ 
                {name:'uraian',index:'uraian', width:100},
                {name:'total',index:'total', width:50, align:'right'}],
            rowNum:10, 
            width: panjang, 
            height: lebar, 
            rownumbers: false, 
            rownumWidth: 40,
            rowList:[1,10,20,30], 
            pager: '#pager3', 
            multiselect: true,
			viewrecords: true,
			gridview: true,
            footerrow : true, 
            userDataOnFooter : true, 
            altRows : true,
			treeIcons: {leaf:'ui-icon-document'},
            sortorder: "desc",
            gridComplete: function(){
                var rows = $("#list3").getDataIDs(); 
				for (var i = 0; i < rows.length; i++)
				{
					var uraian = $("#list3").getCell(rows[i],"uraian");
					if(uraian.indexOf("href") != -1)
					{
						$("#list3").jqGrid('setRowData',rows[i],false, {  color:'black',background:'lightgrey'});            
					}
				} 
            },
            caption:"Ekuitas" 
        }); 
        jQuery("#list3").jqGrid('navGrid','#pager3',{edit:false,add:false,del:false,search:false});
        
        $('#mysubmit').click(function () {
            var str = $("form").serialize();
            var search = str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_matriks_neraca/matriks_neraca_assets_json?'+ search,
                page:1
            }).trigger("reloadGrid");
            jQuery("#list3").jqGrid('setGridParam',{
                url: root + 'mod_matriks_neraca/matriks_neraca_ekuitas_json?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.cols_cari').val("");
            $('.ops_cari').val("");
            $('.text').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_matriks_neraca/matriks_neraca_assets_json',
                page:1
            }).trigger("reloadGrid");
            jQuery("#list3").jqGrid('setGridParam',{
                url: root + 'mod_matriks_neraca/matriks_neraca_ekuitas_json',
                page:1
            }).trigger("reloadGrid");
        });
        $("#form_matriks_neraca_excel").click(function(){
			filter.submit();
		});
    });
    function getDataPeriode(id) {
        $.ajax({
            url: root + 'mod_matriks_neraca/getDataPeriode',
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
