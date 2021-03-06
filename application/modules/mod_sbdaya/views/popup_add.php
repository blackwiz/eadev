<div class="content popup_sbdaya">
    <?= form_open("mod_sbdaya/sbdaya_add", array('id' => 'popup_sbdaya_add')); ?>
    <div class="row-fluid form-horizontal">
        <div class="span12">
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="kode_sbdaya">Kode Sumber Daya</label>
                    <div class="controls">
                        <input class="span8 text" type="text" id="kode_sbdaya" name="kode_sbdaya" value="<?= set_value('kode_sbdaya'); ?>"/>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="nama_sbdaya">Nama Sumber Daya</label>
                    <div class="controls">
                        <input class="span8 text" type="text" id="nama_sbdaya" name="nama_sbdaya" value="<?= set_value('nama_sbdaya'); ?>"/>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="satuan">Satuan</label>
                    <div class="controls">
                        <input class="span8 text" type="text" id="satuan" name="satuan" value="<?= set_value('satuan'); ?>"/>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="tipe">Type</label>
                    <div class="controls">
                        <?= form_dropdown('tipe', $tipe, set_value('tipe'), 'id="tipe"'); ?>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <div class="controls">
                        <div class="btn-group">
                            <button type="button" class="btn btn-info" name="form_sbdaya_save"><i class="icon-ok-sign icon-white"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script type="text/javascript">
    function formSbdayaAddClear() {
        $('input[name="kode_sbdaya"]').val("");
        $('input[name="nama_sbdaya"]').val("");
        $('input[name="satuan"]').val("");
        $('select[name="tipe"]').val("");
    }
    
    $(document).ready(function() {
        $('button[name="form_sbdaya_save"]').bind('click', function() {
            $.ajax({
                url : root + 'mod_sbdaya/sbdaya_add',
                type : 'post',
                dataType : 'json',
                data : $("form").serialize(),
                beforeSend : function() {
                    $(this).attr('disabled',false);
                },
                complete : function() {
                    $(this).attr('disabled',false);
                },
                success : function(json) {
                    $('div.alert').remove();
                    if (json['error']) {
                        $('.popup_sbdaya').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['error']+'</div>');
                        $('div.alert').fadeIn('slow');
                    }
                    if (json['success']) {
                        $('.popup_sbdaya').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['success']+'</div>');
                        $('div.alert').fadeIn('slow');
                        formSbdayaAddClear();
                    }
                    createAutoClosingAlert('div.alert', 3000);
                }
            });
        });
    });
</script>