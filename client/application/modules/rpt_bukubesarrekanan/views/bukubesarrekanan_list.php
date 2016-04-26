<div class="content buku_besar">
    <?= form_open('rpt_bukubesarrekanan/to_excel',array('id' => 'filter')); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Buku Besar Rekanan</h4></div>
                <div class="basic box_content form_search" ></div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <table id="bukubesar_list"></table>
            <div id="bukubesar_page"></div>
        </div>
    </div>
    <?= form_close(); ?> 
</div>
<?= $searchform; ?>
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/msdropdown/css/msdropdown/dd.css" />
<script src="<?= base_url(); ?>assets/msdropdown/js/msdropdown/jquery.dd.js"></script>
<script type="text/javascript">
    function getperiod() {
        $.ajax({
            url: root + "rpt_bukubesarrekanan/getjsonperiod",
            dataType: 'json',
            type: 'post',
            success: function(json) {
                $("#ivansearch_val_0").msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd");
                $("#ivansearch_val_1").msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd");
            }
        });
    }
    function getdperkir() {
        $.ajax({
            url: root + "rpt_bukubesarrekanan/getjsondperkir",
            dataType: 'json',
            type: 'post',
            success: function(json) {
                $("#ivansearch_val_2").msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd");
                var oDropdown = $("#ivansearch_val_2").msDropdown().data("dd");
				oDropdown.set("selectedIndex", 2);
            }
        });
    }

    function getbukubantu(dperkir) {
		//$('#ivansearch_val_2').ready(function() {
			$.ajax({
				url: root + "rpt_bukubesarrekanan/getjsonbukubantu",
				dataType: 'json',
				type: 'post',
				data: { dperkir_id: dperkir },
				success: function(json) {
					//alert(json);
					//$("#ivansearch_val_3").msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd");
					$("#ivansearch_val_3").children('select').remove();
					$("#ivansearch_val_3").append('<select id="dropDownDest" name="vals[]"></select>');
					$.each(json,function(key,value){
						var option = $('<option />').val(value.value).text(value.description);
						$("#dropDownDest").append(option);
					});
				}
			});
		//});
    }
    $(document).ready(function() {

        getperiod();
        getdperkir();
		//getbukubantu(0);
		
		$("#ivansearch_val_2").on("change", function() {
			var oDropdown = $("#ivansearch_val_2").msDropdown().data("dd");
			var index = oDropdown.getData();
			//alert(index.data.value);
			getbukubantu(index.data.value);
		});
		
		/*$("#ivansearch_val_2").change(function(){
			var oDropdown = $("#ivansearch_val_2").msDropdown().data("dd");
			var index = oDropdown.getData();
			//alert(index.data.value);
			getbukubantu(index.data.value);
		})*/

        $('#button_search').click(function() {
            var str = $("form").serialize();
            var search = "_search=true&" + str;
            jQuery("#bukubesar_list").jqGrid('setGridParam', {
                url: root + mod + '/jsonBukuBesar?' + search,
                page: 1
            }).trigger("reloadGrid");
        });

        var panjang = $('.inbody').height() - 220;
        var lebar = $('.content').width() - 20;
        jQuery("#bukubesar_list").jqGrid({
            url: root + mod + '/jsonBukuBesar',
            mtype: "post",
            datatype: "json",
            colNames: ['No.', 'Tanggal', 'Nomor Bukti', 'Rekanan', 'Uraian', 'Lawan', 'Debet', 'Kredit', 'Saldo'],
            colModel: [
                {name: 'no', index: 'id_jurnal', width: 25, sortable: false, align: "center"},
                {name: 'tanggal', index: 'tanggal', width: 100, sortable: false, align: "center"},
                {name: 'no_bukti', index: 'no_bukti', width: 200, sortable: false, align: "center"},
                {name: 'rekanan', index: 'rekanan', width: 150, sortable: false},
                {name: 'uraian', index: 'uraian', width: 350, sortable: false},
                {name: 'coa', index: 'coa', width: 150, sortable: false, align: "center"},
                {name: 'debet', index: 'debet', width: 150, sortable: false, align: "right"},
                {name: 'kredit', index: 'kredit', width: 150, sortable: false, align: "right"},
                {name: 'saldo', index: 'saldo', width: 150, sortable: false, align: "right"}
            ],
            rownumbers: true,
            rownumWidth: 40,
            shrinkToFit: false,
            width: lebar,
            height: panjang,
            pager: '#bukubesar_page',
            viewrecords: true,
            rowList: [50, 100, 150, 200, 250, 500],
            rowNum: 50
        });
        jQuery("#bukubesar_list").jqGrid('navGrid', '#bukubesar_page', {edit: false, add: false, del: false, search: false});
        
        $("#bukubesarrekanan_export_xls").click(function(){
			filter.submit();
		});
	});
</script>
