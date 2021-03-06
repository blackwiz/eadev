<script>
    function getDataProyek(id) {
        $.ajax({
            url: root + 'mod_transaksi/getDataProyek',
            type: 'post',
            data: { id: id},
            success: function(data) {
                $('select[name="subunit_proyek"]').html(data);
                cleanTransaksi();
            }
        });	
    }
    
    function cleanTransaksi() {
        $.ajax({
            url: root + 'mod_transaksi/cleanTransaksi',
            type: 'post',
            success: function() {
                jQuery("#list2").jqGrid('setGridParam',{
                    url: root + mod + '/sess2json',
                    page:1
                }).trigger("reloadGrid");
                clean();
            }
        });
    }
    
    function kodePicker(json) {
        $('input[name="kode_perkiraan"]').val(json.kdperkiraan);
        $('input[name="nama_perkiraan"]').val(json.nmperkiraan);
        $(pkname).dialog('close');
    }
    
    function RekananPicker(json) {
        $('input[name="kode_nasabah"]').val(json.kode_rekanan);
        $('input[name="nama_nasabah"]').val(json.nama_rekanan);
        $(pkname).dialog('close');
    }
    
    function sbdayaPicker(json) {
        $('input[name="kode_sbdaya"]').val(json.kode_sbdaya);
        $('input[name="nama_sbdaya"]').val(json.sbdaya);
        $(pkname).dialog('close');
    }
    
    function clean() {
        $('input[name="id"]').val("");
        $('input[name="kode_perkiraan"]').val("");
        $('input[name="nama_perkiraan"]').val("");
        $('input[name="kode_nasabah"]').val("");
        $('input[name="nama_nasabah"]').val("");
        $('input[name="kode_sbdaya"]').val("");
        $('input[name="nama_sbdaya"]').val("");
        $('input[name="volume"]').val("");
        $('input[name="memo"]').val("");
        $('input[name="debet"]').val("");
        $('input[name="kredit"]').val("");
    }
    
    function wew(id) {
        var ret = jQuery("#list2").jqGrid('getRowData',id); 
        $('input[name="id"]').val(ret.id);
        $('input[name="kode_perkiraan"]').val(ret.kode_perkiraan);
        $('input[name="nama_perkiraan"]').val(ret.nama_perkiraan);
        $('input[name="kode_nasabah"]').val(ret.kode_nasabah);
        $('input[name="nama_nasabah"]').val(ret.nama_nasabah);
        $('input[name="kode_sbdaya"]').val(ret.kode_sbdaya);
        $('input[name="nama_sbdaya"]').val(ret.nama_sbdaya);
        $('input[name="volume"]').val(ret.volume);
        $('input[name="memo"]').val(ret.uraian);
        $('input[name="debet"]').val(ret.debet);
        $('input[name="kredit"]').val(ret.kredit);
    }
        
    $(document).ready(function() {
        //        jQuery("#list2").jqGrid().trigger('reloadGrid');

        $(".item_delete").click(function() {
            var id = jQuery("#list2").jqGrid('getGridParam','selarrrow'); 
            if(id){
                $.ajax({
                    url: root + 'mod_transaksi/deletejurnal',
                    type: 'post',
                    data: { id: id},
                    success: function(data) {
                        jQuery("#list2").jqGrid().trigger('reloadGrid');
                    }
                });	
            } else {
                alert("Please Select Row to delete!");
            }
        });
        
        $('button[name="save"]').click(function() {
            var tanggal = $('input[name="tanggal"]').val();
            var tipe_transaksi = $('select[name="tipe_transaksi"]').val();
            var kode_proyek = $('select[name="subunit_proyek"]').val();
            
            $.ajax({
                url: 'mod_transaksi/addJurnal',
                dataType: 'json',
                type: 'post',
                data: { 
                    tanggal:tanggal, 
                    tipe_transaksi:tipe_transaksi, 
                    kode_proyek:kode_proyek
                },
                beforeSend: function() {
                    $('button[name="save"]').attr('disabled',true);
                },	
                complete: function() {
                    $('button[name="save"]').attr('disabled',false);
                },			
                success: function(json) {
                    $('div.alert').remove();
                    
                    if (json['error']) {
                        $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '+json['error']+'</div>');
                        $('div.alert').fadeIn('slow');
                    }
                                        
                    if (json['success']) {
                        $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button> '+json['success']+'</div>');
                        $('div.alert').fadeIn('slow');
                        jQuery("#list2").jqGrid('setGridParam',{
                            url: root + mod + '/sess2json', 
                            page:1
                        }).trigger("reloadGrid");
                    }
                }
            });	
        });
        
        $('select[name="subunit_proyek"]').change(function() {
            cleanTransaksi();
        });
        
        $( ".datepicker" ).datepicker({
            showOn: "button",
            buttonImage: root + "images/calendar.gif",
            dateFormat : 'yy-mm-dd',
            buttonImageOnly: true
        });

        var id_unitkerja = $('select[name="unitkerja"]').val();
        getDataProyek(id_unitkerja);

        $('select[name="unitkerja"]').change(function() {
            var id =  $('select[name="unitkerja"]').val();
            getDataProyek(id);
        });

        $("#cancel").click(function() {
            clean();
        });

        $(".price_format").priceFormat({
            prefix: '',
            centsSeparator: '.',
            thousandsSeparator: ','
        });
        
        var lebar = $('.inbody').height() - 320;
        var panjang = $('.content').width() - 20;

        $("#add").click(function() {
            var id = $('input[name="id"]').val();
            var tanggal = $('input[name="tanggal"]').val();
            var kode_perkiraan = $('input[name="kode_perkiraan"]').val();
            var nama_perkiraan = $('input[name="nama_perkiraan"]').val();
            var kode_nasabah = $('input[name="kode_nasabah"]').val();
            var nama_nasabah = $('input[name="nama_nasabah"]').val();
            var kode_sbdaya = $('input[name="kode_sbdaya"]').val();
            var nama_sbdaya = $('input[name="nama_sbdaya"]').val();
            var volume = $('input[name="volume"]').val();
            var memo = $('input[name="memo"]').val();
            var debet = $('input[name="debet"]').val();
            var kredit = $('input[name="kredit"]').val();

            $.ajax({
                url: 'mod_transaksi/add',
                dataType: 'json',
                type: 'post',
                data: { 
                    id:id, 
                    tanggal:tanggal, 
                    kode_perkiraan:kode_perkiraan, 
                    nama_perkiraan:nama_perkiraan, 
                    kode_nasabah:kode_nasabah, 
                    nama_nasabah:nama_nasabah, 
                    kode_sbdaya:kode_sbdaya, 
                    nama_sbdaya:nama_sbdaya, 
                    volume:volume, 
                    memo:memo, 
                    debet:debet,
                    kredit:kredit
                },
                beforeSend: function() {
                    $('button[name="add"]').attr('disabled',true);
                },	
                complete: function() {
                    $('button[name="add"]').attr('disabled',false);
                },	
                success: function(json) {
                    $('div.alert').remove();
                    
                    if (json['error']) {
                        $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '+json['error']+'</div>');
                        $('div.alert').fadeIn('slow');
                    }
                                        
                    if (json['success']) {
                        $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['success']+'</div>');
                        $('div.alert').fadeIn('slow');
                        jQuery("#list2").jqGrid('setGridParam',{
                            url: root + mod + '/sess2json', 
                            page:1
                        }).trigger("reloadGrid");
                        clean();
                    }
                }
            });	
            
        });


        jQuery("#list2").jqGrid({
            url: root + mod + '/sess2json', 
            mtype : "post",
            datatype: "json",
            colNames:['#','','','','','','','','COA','Rekanan', 'Sumber Daya','Volume','Debet','Kredit','Uraian'], 
            colModel:[
                {name:'act',index:'act', width:8,sortable:false},
                {name:'id',key : true, index:'id', hidden:true,width:50},
                {name:'kode_perkiraan', index:'kode_perkiraan',hidden:true,width:50},
                {name:'nama_perkiraan', index:'nama_perkiraan',hidden:true,width:50},
                {name:'kode_nasabah',index:'kode_nasabah',hidden:true,width:50},
                {name:'nama_nasabah',index:'nama_nasabah',hidden:true,width:50},
                {name:'kode_sbdaya',index:'kode_sbdaya',hidden:true,width:50},
                {name:'nama_sbdaya',index:'nama_sbdaya', hidden:true,width:50},
                {name:'perkiraan',index:'perkiraan', width:100},
                {name:'nasabah',index:'nasabah', width:100},
                {name:'sbdaya',index:'sbdaya', width:100},
                {name:'volume',index:'volume', width:30, align:"right"},
                {name:'debet',index:'debet', width:50, align:"right",formatter:'currency',formatoptions:{thousandsSeparator:","}},
                {name:'kredit',index:'kredit', width:50, align:"right",formatter:'currency',formatoptions:{thousandsSeparator:","}},
                {name:'uraian',index:'uraian', width:100}
            ],
            scroll: true,
            width: panjang,
            height: lebar,
            rownumbers: true,
            rowNum:1000,
            rownumWidth: 40,
            multiselect: true,
            pager: '#pager2',
            viewrecords: true,
            footerrow : true, 
            userDataOnFooter : true, 
            altRows : true,
            gridComplete: function(){ 
                var ids = jQuery("#list2").jqGrid('getDataIDs'); 
                for(var i=0;i < ids.length;i++){ 
                    var cl = ids[i]; 
                    ce = "<a href=\"#\" onclick=\"wew("+ids[i]+");\" class=\"link_edit\"><img  src=\"<?= base_url(); ?>media/edit.png\" /></a>"; 
                    jQuery("#list2").jqGrid('setRowData',ids[i],{act:ce}); 
                } 
            }
            //caption:"List Proyek"
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});

    });
</script>
<div class="content">
    <?= form_open(); ?>
<!--    <div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> Best check yo self, you're not looking too good.</div>-->
    <input type="hidden" name="id" value="" />

    <div class="row-fluid">
        <div class="span6">
            <div class="box">
                <div class="box_title"><h4><span>#</span></h4></div>
                <div class="box_content">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Tanggal</label>
                                <input class="span8 datepicker" type="text" name="tanggal" id="tanggal" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Kas / Bank</label>
                                <select class="span9" name="tipe_transaksi">
                                    <option value="B">Kas Bank</option>
                                    <option value="M">Memorial</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Kode Perkiraan</label>
                                <input type="text" class="span4 text" name="kode_perkiraan" id="kode_perkiraan" readonly/>
                                <input type="text" class="span4 text" name="nama_perkiraan" id="nama_perkiraan" readonly/>
                                <button type="button" class="btn btn-info pklist " title="Kode Perkiraan" rel="type=iframe&src=<?= site_url('mod_kdperkiraan/popup_kdperkir'); ?>&width=600&height=400&func=kodePicker"><i class="icon-search icon-white"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Kode Nasabah</label>
                                <input type="text" class="span4 text" name="kode_nasabah" id="kode_nasabah" readonly/>
                                <input type="text" class="span4 text" name="nama_nasabah" id="nama_nasabah" readonly/>
                                <button type="button" class="btn btn-info pklist " title="Search Nasabah" rel="type=iframe&src=<?= site_url('mod_rekanan/popup_rekanan'); ?>&width=600&height=400&func=RekananPicker&id_proyek=true"><i class="icon-search icon-white"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Memo</label>
                                <input class="span9 text" type="text" name="memo" value="" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Debet / Kredit</label>
                                <input class="span4 price_format" type="text" name="debet" id="debet" /> / 
                                <input class="span4 price_format" type="text" name="kredit" id="kredit" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3"></label>
                                <button type="button" name="add" id="add" class="btn btn-primary"><i class="icon-ok icon-white"></i> Add</button>
                                <button type="button" name="cancel" id="cancel" class="btn btn-primary"><i class="icon-remove icon-white"></i> Cancel</button>
                                <button type="button" name="save" id="save" class="btn btn-primary"><i class="icon-arrow-right icon-white"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="box">
                <div class="box_title"><h4><span>#</span></h4></div>
                <div class="box_content">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Level User</label>
                                <label class="form-label span8"><?= $level; ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Unit Kerja</label>
                                <?= form_dropdown('unitkerja', $unitkerja, set_value('unitkerja'), 'class="span9"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Sub Unit / Proyek</label>
                                <select class="span9 text" name="subunit_proyek"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Kode Sumber Daya</label>
                                <input type="text" class="span4 text" name="kode_sbdaya" id="kode_sbdaya" readonly/>
                                <input type="text" class="span4 text" name="nama_sbdaya" id="nama_sbdaya" readonly/>
                                <button type="button" class="btn btn-info pklist " title="Search Sumber Daya" rel="type=iframe&src=<?= site_url('mod_sbdaya/popup_sbdaya'); ?>&width=600&height=400&func=sbdayaPicker&id_proyek=true"><i class="icon-search icon-white"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Volume</label>
                                <input class="span9 text" type="text" name="volume" value="0" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Work Item</label>
                                <input type="text" class="span4 text" name="kode_workitem" id="kode_workitem" readonly/>
                                <input type="text" class="span4 text" name="nama_workitem" id="nama_workitem" readonly/>
                                <button type="button" class="btn btn-info pklist " title="Search Work Item" ><i class="icon-search icon-white"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <label class="form-label span3">Kode Kontrak Alat</label>
                                <input type="text" class="span4 text" name="kode_kontrakalat" id="kode_kontrakalat" readonly/>
                                <input type="text" class="span4 text" name="nama_kontrakalat" id="nama_kontrakalat" readonly/>
                                <button type="button" class="btn btn-info pklist " title="Search Kontrak Alat" ><i class="icon-search icon-white"></i></button>
                            </div>
                        </div>
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

<!--    <table border="0">
        <tr>
            <td valign="top" width="40%" nowrap>
                <table>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><input type="text" class="inputlist datepicker" name="tanggal" id="tanggal" readonly/></td>
                    </tr>
                    <tr>
                        <td>Type Transaksi</td>
                        <td>:</td>
                        <td>
                            <select name="tipe_transaksi">
                                <option value="B">Kas Bank</option>
                                <option value="M">Memorial</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Kode Perkiraan</td>
                        <td>:</td>
                        <td>
                            <input type="text" class="inputlist" name="kode_perkiraan" id="kode_perkiraan" readonly/>
                            <input type="text" class="inputlist" name="nama_perkiraan" id="nama_perkiraan" readonly/>
                            <button type="button" class="button pklist" title="Kode Perkiraan" rel="type=iframe&src=<?= site_url('mod_kdperkiraan/popup_kdperkir'); ?>&width=600&height=400&func=kodePicker"><span class="bt1"><span class="bt2"><b class="item_find"></b></span></span></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Kode Nasabah</td>
                        <td>:</td>
                        <td>
                            <input type="text" class="inputlist" name="kode_nasabah" id="kode_nasabah" readonly/>
                            <input type="text" class="inputlist" name="nama_nasabah" id="nama_nasabah" readonly/>
                            <button type="button" class="button pklist" title="Search Nasabah" rel="type=iframe&src=<?= site_url('mod_rekanan/popup_rekanan'); ?>&width=600&height=400&func=RekananPicker&id_proyek=true"><span class="bt1"><span class="bt2"><b class="item_find"></b></span></span></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Kode Sumber Daya</td>
                        <td>:</td>
                        <td>
                            <input type="text" class="inputlist" name="kode_sbdaya" id="kode_sbdaya" readonly/>
                            <input type="text" class="inputlist" name="nama_sbdaya" id="nama_sbdaya" readonly/>
                            <button type="button" class="button pklist" title="Search Sumber Daya" rel="type=iframe&src=<?= site_url('mod_sbdaya/popup_sbdaya'); ?>&width=600&height=400&func=sbdayaPicker&id_proyek=true"><span class="bt1"><span class="bt2"><b class="item_find"></b></span></span></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Volume</td>
                        <td>:</td>
                        <td><input type="text" name="volume" id="volume" size="50"/></td>
                    </tr>
                    <tr>
                        <td>Memo</td>
                        <td>:</td>
                        <td><input type="text" name="memo" id="memo" size="50"/></td>
                    </tr>
                    <tr>
                        <td>Debet / Kredit</td>
                        <td>:</td>
                        <td><input type="text" name="debet" id="debet" class="price_format"/> / <input type="text" name="kredit" id="kredit" class="price_format"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>:</td>
                        <td><input type="button" name="add" value="Add" id="add"/>
                            <input type="button" name="cancel" value="Cancel" id="cancel"/>
                            <input type="button" name="save" value="Save" id="save"/></td>
                    </tr>
                </table>
            </td>
            <td valign="top" width="10%" nowrap></td>

        </tr>
    </table>-->

    <?= form_close(); ?>
</div>