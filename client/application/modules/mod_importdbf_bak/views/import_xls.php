<div class="content form_importdbf">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="blue box_title"><h4><span>#</span> Import DBF</h4></div>
                <div class="blue box_content form-horizontal">
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_importdbf_fileupload">Upload</label>
                            <div class="controls">
                                <input type="file" name="form_importdbf_fileupload" id="form_importdbf_fileupload" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="button" name="form_importdbf_proses" class="btn btn-primary"><i class="cus-table-add"></i>Proses</button>
                                </div>
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
    <?= form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 120;
        var panjang = $('.content').width() - 20;
        jQuery("#list2").jqGrid({
            url: root + 'mod_importdbf/import_dbf', 
            mtype : "post",
            datatype: "json",
            colNames:['TX_CODE','DATE','TX_NO','IT_NO','DESC','AMOUNT','D_CODE','C_CODE','W_ITEM'], 
            colModel:[
                {name:'TX_CODE',index:'TX_CODE', hidden:true, width:300},
                {name:'date',index:'date', width:50},
                {name:'TX_NO',index:'TX_NO', width:300},
                {name:'IT_NO',index:'IT_NO', width:300},
                {name:'DESC',index:'DESC', width:300},
                {name:'AMOUNT',index:'AMOUNT', width:300},
                {name:'D_CODE',index:'D_CODE', width:300},
                {name:'C_CODE',index:'C_CODE', width:300},
                {name:'W_ITEM',index:'W_ITEM', width:300}
            ],
            scroll: true,
            width: panjang,
            height: lebar,
            rownumbers: true,
            rowNum:1000,
            rownumWidth: 40,
            multiselect: false,
            pager: '#pager2',
            viewrecords: true,
            //shrinkToFit: false,
            forceFit : true, 
            cellEdit: true, 
            cellsubmit: 'clientArray', 
            afterEditCell: function (id,name,val,iRow,iCol){ 
                if(name=='date') { 
                    jQuery("#"+iRow+"_date","#list2").datepicker({dateFormat:"yy-mm-dd"}); 
                } 
            }
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
    });
</script>