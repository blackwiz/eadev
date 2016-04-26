<div class="content">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Saldo Kode Perkiraan <?php echo $period; ?></h4></div>
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
	function createAutoClosingAlert(selector, delay) {
        var alert = $(selector).alert();
        window.setTimeout(function() { alert.alert('close') }, delay);
    }
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        var KprevVal;
        var DprevVal;
        jQuery("#list2").jqGrid({
            url: root + 'mod_saldo/saldo_json/<?php echo $key; ?>', 
            mtype : "post",
            datatype: "json", 
            colNames:["1","<div id='jq_checkbox_head_added'><div>","Kode Akun", "Nama Akun","Saldo"], 
            colModel:[ 
                {name:'id',index:'id', width:100, hidden:true, sortable:false},
                {name:'check',index:'check', width:20, sortable:false, align:"center"},
                {name:'kdperkiraan',index:'kdperkiraan', width:150}, 
                {name:'nmperkiraan',index:'nmperkiraan', width:300},
                {name:'rupiah',index:'rupiah', width:150, align:"right", formatter: 'currency', formatoptions: {thousandsSeparator: ","}, sortable:false, editable:true,
					editoptions: { dataInit: function (elem) {
						   setTimeout(function(){
							   $(elem).number(true, 2);
						   }, 100);
						}
					}
				}
            ], 
            rowNum:20, 
            width: panjang, 
            height: lebar, 
            rownumbers: true, 
            rownumWidth: 40,
            rowList:[20,30,40,50], 
            pager: '#pager2', 
            multiselect: false,
            viewrecords: true, 
            shrinkToFit: false,
            cellEdit: true, 
            cellsubmit: 'clientArray',
            /*afterEditCell: function (id,name,val,iRow,iCol) {
				KprevVal = jQuery("#list2").jqGrid('getCell',id,5);
				DprevVal = jQuery("#list2").jqGrid('getCell',id,6);
				//alert('After Edit');
				if(name=='kredit') {
					jQuery("#list2").jqGrid('setCell',id,"debit","0","");
				}
				if(name=='debit') {
					jQuery("#list2").jqGrid('setCell',id,"kredit","0","");
				}
			},
			afterRestoreCell: function(id,name,val,iRow,iCol){
				//alert(KprevVal+' - '+DprevVal);
				jQuery("#list2").jqGrid('setCell',id,"kredit",KprevVal,"");
				jQuery("#list2").jqGrid('setCell',id,"debit",DprevVal,"");
			},*/
            afterSaveCell: function(id,name,val,iRow,iCol) {
				//alert('After Save');
				var id = jQuery("#list2").jqGrid('getCell',id,3);
				var rupiah = jQuery("#list2").jqGrid('getCell',id,5);
				$.ajax({
                        url: root + "mod_saldo/save/<?php echo $key; ?>",
                        dataType: 'json',
                        type: 'post',
                        data: {
                            id:id,
                            rupiah:rupiah
                        },
                        beforeSend: function() {
                            $(this).attr('disabled',true);
                        },	
                        complete: function() {
                            $(this).attr('disabled',false);
                        },	
                        success: function(json) {
                            $('div.alert').remove();
                            if (json['error']) {
                                $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['error']+'</div>');
                            }
                            if (json['success']) {
                                return false;
                            }
                            createAutoClosingAlert('div.alert', 2000);
                        }
                    });
			}
        }); 
        
        jQuery("#list2").jqGrid('setGroupHeaders', { 
            useColSpanStyle: true, 
            groupHeaders:[
                {startColumnName: 'kredit', numberOfColumns: 2, titleText: 'Saldo'} 
            ] 
        });
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'uncheckbox.gif" /></div>');
        $('div#jq_checkbox_head_added').removeClass('selected');
        
        $('div#jq_checkbox_head_added').click(function() {
            $('.checkicon_add').remove();
            
            if($('div#jq_checkbox_head_added').hasClass('selected')) {
                $('div#jq_checkbox_head_added').removeClass('selected');
                $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'uncheckbox.gif" /></div>');
                $('.jq_checkbox_added').each(function() {
                    this.checked = false;
                });
            }
            else {
                $('div#jq_checkbox_head_added').addClass('selected')
                $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'checkbox.gif" /></div>');
                $('.jq_checkbox_added').each(function() {
                    this.checked = true;
                });
            }
        });
        
        $('#button_search').click(function () {
            var str = $("form").serialize();
            var search = "_search=true&"+ str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_saldo/saldo_json/<?php echo $key; ?>?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.cols_cari').val("");
            $('.ops_cari').val("");
            $('.text').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_saldo/saldo_json/<?php echo $key; ?>',
                page:1
            }).trigger("reloadGrid");
        });
        
        $('button[name="form_saldo_save"]').bind('click', function() {
            $.ajax({
                url: root + 'mod_saldo/import/<?php echo $key; ?>',
                dataType: 'json',
                type: 'post',
                data: $("form").serialize(),
                beforeSend: function() {
                    $(this).attr('disabled',true);
                },	
                complete: function() {
                    $(this).attr('disabled',false);
                },			
                success: function(json) {
                    $('div.alert').remove();
                    if (json['error']) {
                        $('.form_importdbf').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['error']+'</div>');
                        $('div.alert').fadeIn('slow');
                    }
                                        
                    if (json['success']) {
                        
                    } 
                    createAutoClosingAlert('div.alert', 3000);
                }
            });
        });
    });
</script>
