<div class="content">
    <?= form_open('mod_listjurnal/to_excel',array('id' => 'filter')); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> List Jurnal</h4></div>
                <div class="basic box_content form_search" ></div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <table id="list2"></table>
            <div id="pager2"></div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<?= $searchform; ?>
<script type="text/javascript" src="<?= base_url(); ?>js/searching.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-4.4.1/src/grid.treegrid.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/msdropdown/css/msdropdown/dd.css" />
<script src="<?= base_url(); ?>assets/msdropdown/js/msdropdown/jquery.dd.js"></script>
<script type="text/javascript">
    function getperiod(id, selectedvalue) {
        selectedvalue = typeof selectedvalue !== 'undefined' ? selectedvalue : '';

        $.ajax({
            url: root + "main/getjsonperiod",
            dataType: 'json',
            type: 'post',
            success: function(json) {
                $('#ivansearch_val_' + id).msDropDown({byJson:{data:json, name:'vals[]'}}).data("dd").setIndexByValue(selectedvalue);
            }
        });	
    }
    function getBoolean(id, selectedvalue) {
        selectedvalue = typeof selectedvalue !== 'undefined' ? selectedvalue : '';
        $.ajax({
            url: root + "main/getBoolean",
            dataType: 'json',
            type: 'post',
            success: function(json) {
                $('#ivansearch_val_' + id).msDropDown({byJson:{data:json, name:'vals[]'}}).data("dd").setIndexByValue(selectedvalue);
            }
        });	
    }
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        jQuery("#list2").jqGrid({
            url: root + 'mod_listjurnal/listjurnal_json', 
			mtype : "post",
            datatype: "json", 
            colNames:['No','Tanggal', 'Nomor Bukti', 'Nomor Referensi', 'Proyek', 'Keterangan', 'COA', 'Rekanan', 'Debit', 'Kredit'], 
            colModel:[ 
                {name:'nomor',index:'id_jurnal', sortable: false, width:25, align:"center"}, 
                {name:'tanggal',index:'tanggal', sortable: false, width:80, formatter:'date', formatoptions:{srcformat:"Y-m-d",newformat:"d M Y"}, align:"center"},  
                {name:'nobukti',index:'nobukti', sortable: false, width:100},
                {name:'no_dokumen',index:'no_dokumen', sortable: false, width:100},
                {name:'kode_proyek',index:'kode_proyek', sortable: false, width:100},
                {name:'keterangan',index:'keterangan', sortable: false, width:140},
                {name:'coa',index:'coa', sortable: false, width:100},
                {name:'rekanan',index:'rekanan', sortable: false, width:100},
                {name:'debit',index:'debit', sortable: false, width:100, align:"right"},
                {name:'kredit',index:'kredit', sortable: false, width:80, align:"right"}],
            rowNum:10, 
            width: panjang, 
            height: lebar, 
            rownumWidth: 40,
            rowList:[10,20,30], 
            pager: '#pager2'
            //caption:"List Users" 
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $('#button_search').click(function () {
            var str = $("form").serialize();
            var search = "_search=true&"+ str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_listjurnal/listjurnal_json?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.cols_cari').val("");
            $('.ops_cari').val("");
            $('.text').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_listjurnal/listjurnal_json',
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#form_listjurnal_excel").click(function(){
			filter.submit();
		});
		
    });
</script>
