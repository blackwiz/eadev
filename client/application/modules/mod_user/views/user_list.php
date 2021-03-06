<div class="content">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Master User</h4></div>
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
<script type="text/javascript">
    
    function getGroupdata(id) {
        $('#ivansearch_val_' + id).remove();  
        var _rowval =  $('#row-val-'+ id );
        var _textautocomplete = $('<input type="text" class="span12 text ivansearch_val" id="ivansearch_val_'+ id +'" name="vals[]" />');
        _textautocomplete.autocomplete({
            minLength: 2,
            source: root + "mod_group/listgroupaksesjson", 
            select: function( event, ui ) {
                if(ui.item.id != 0 && ui.item.id != "") {
                    _textautocomplete.val(ui.item.desc);
                } else {
                    return false;
                }
            }
        });
        _rowval.append(_textautocomplete);
    }
    
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        jQuery("#list2").jqGrid({
            url: root + 'mod_user/user_json', 
            mtype : "post",
            datatype: "json", 
            colNames:['ID Users','Username', 'Nama Lengkap','Group Akses', 'Group Data',''], 
            colModel:[ 
                {name:'user_id',index:'user_id',hidden:true, width:100}, 
                {name:'username',index:'username', width:150}, 
                {name:'fullname',index:'fullname', width:150}, 
                {name:'nama_group',index:'nama_group', width:150}, 
                {name:'unit_kerja',index:'unit_kerja', width:150},
                {name:'aksi',width:30, align:"center"}], 
            rowNum:10, 
            autowidth:true,
            //            width: panjang, 
            height: lebar, 
            rownumbers: true, 
            rownumWidth: 40,
            rowList:[20,40,60], 
            pager: '#pager2', 
            multiselect: true,
            viewrecords: true, 
            sortorder: "desc",
            shrinkToFit: false

        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $('#button_search').click(function () {
            var str = $("form").serialize();
            var search = "_search=true&"+ str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_user/user_json?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.ivansearch_field').val("");
            $('.ivansearch_ops').val("");
            $('.ivansearch_val').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_user/user_json',
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#form_user_delete").click(function() {
            var id = jQuery("#list2").jqGrid('getGridParam','selarrrow'); 
            if(id){
                $.ajax({
                    url: root + 'mod_user/user_delete',
                    type: 'post',
                    data: { id: id},
                    success: function() {
                        jQuery("#list2").jqGrid().trigger('reloadGrid');
                    }
                });	
            } else {
                alert("Please Select Row to delete!");
            }
        });
    });
</script>
