<div class="content popup_sbdaya">
    <?= form_open(); ?>
    <div class="row-fluid form-horizontal">
        <div class="span12">
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="popup_kodesbdaya">Kode Sumber Daya</label>
                    <div class="controls">
                        <input type="text" name="popup_kodesbdaya" id="popup_kodesbdaya"/> 
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="popup_namasbdaya">Nama Sumber Daya</label>
                    <div class="controls">
                        <input type="text" name="popup_namasbdaya" id="popup_namasbdaya"/> 
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <div class="controls">
                        <button type="button" name="popup_carisbdaya" class="btn"><i class="cus-zoom"></i> Cari</button>
                    </div>
                </div>
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
<script type="text/javascript">
	function popupsbdaya_accept(id) {
        $.ajax({
            url: root + 'mod_sbdaya/getsbdaya',
            type:'POST',
            dataType:'json',
            data:{
                item: id
            },
            beforeSend: function() {
                $(this).attr('disabled',true);
            },
            complete: function() {
                $(this).attr('disabled',true);
            },
            success: function(json) {
                parent.pkcaller(json);
            }
        });
    }
    function popup_sbdaya_refresh_grid() {
        jQuery("#list2").jqGrid('setGridParam', {
            url: root + "mod_sbdaya/popup_json",
            page: 1
        }).trigger("reloadGrid");
    }

    function popupsbdaya_edit(id) {
        showUrlInDialog(root + "mod_sbdaya/popup_view/" + id, "popup_sbdaya_refresh_grid", "Edit Buku Bantu SB Daya", "popups_sbdaya_edit", 600, 500);
    }

    $(document).ready(function() {
        $('button[name="popup_carisbdaya"]').bind('click', function() {
            var kodesbdaya = $('input[name="popup_kodesbdaya"]').val();
            var namasbdaya = $('input[name="popup_namasbdaya"]').val();

            var search = "_search=true&kodesbdaya=" + kodesbdaya + "&namasbdaya=" + namasbdaya;
            jQuery("#list2").jqGrid('setGridParam', {
                url: root + 'mod_sbdaya/popup_json?' + search,
                page: 1
            }).trigger("reloadGrid");
        });

        jQuery("#list2").jqGrid({
            url: root + 'mod_sbdaya/popup_json',
            mtype: "post",
            datatype: "json",
            colNames: ['#', '#', 'Kode SBDaya', 'Nama SBDaya'],
            colModel: [
                {name: 'act', index: 'act', width: 80, align: "center", sortable: false},
                {name: 'id_sbdaya', index: 'id_sbdaya', hidden: true},
                {name: 'kode_sbdaya', index: 'kode_sbdaya', width: 150},
                {name: 'sbdaya', index: 'sbdaya', width: 300}
            ],
            rowNum: 10,
            width: 530,
            height: 250,
            rownumbers: true,
            rownumWidth: 40,
            rowList: [10, 20, 30],
            pager: '#pager2',
            viewrecords: true,
            shrinkToFit: false,
            gridComplete: function() {
                var ids = jQuery("#list2").jqGrid('getDataIDs');
                for (var i = 0; i < ids.length; i++) {
                    var cl = ids[i];
                    ce = "<a href=\"#\" onclick=\"popupsbdaya_accept("+ids[i]+");\" class=\"link_edit tooltips\" data-placement=\"right\" data-toggle=\"tooltip\" data-original-title=\"Pilih Data Buku Bantu Sumber Daya\"><img  src=\"<?= base_url(); ?>media/application_add.png\" /></a>"; 
                    co = "<a href=\"#\" onclick=\"popupsbdaya_edit(" + ids[i] + ");\" class=\"link_edit tooltips\" data-placement=\"right\" data-toggle=\"tooltip\" data-original-title=\"Edit Data Buku Bantu Sumber Daya\"><img  src=\"<?= base_url(); ?>media/edit2_32x32.png\" /></a>";
                    jQuery("#list2").jqGrid('setRowData', ids[i], {act: co+ce});
                }
                $(".tooltips").tooltip();
            }
        });
        jQuery("#list2").jqGrid('navGrid', '#pager2', {edit: false, add: false, del: false, search: false});
    });
</script>
