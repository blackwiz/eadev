<div class="content">
    <?= form_open("mod_proyek/proyek_edit"); ?>
    <input type="hidden" name="id" value="<?= $detail["id_proyek"]; ?>" />
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Master Proyek Edit</h4></div>
                <div class="basic box_content form-horizontal">
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="kode_proyek">Kode Proyek</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="kode_proyek" name="kode_proyek" value="<?= $detail["kode_proyek"]; ?>" autocomplete="off" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="id_katproyek">Kategori Proyek</label>
                            <div class="controls">
                                <?= form_dropdown('id_katproyek', $id_katproyek, $detail["id_katproyek"], 'id="id_katproyek"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="jenisproyek_id">Jenis Proyek</label>
                            <div class="controls">
                                <?= form_dropdown('jenisproyek_id', $jenisproyek_id, $detail["proyek_jenisproyek_id"], 'id="jenisproyek_id"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="id_subunitkerja">Unit Kerja</label>
                            <div class="controls">
                                <?= form_dropdown('id_subunitkerja', $id_subunitkerja, $detail["id_subunitkerja"], 'id="id_subunitkerja"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="nama_proyek">Nama Proyek</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="nama_proyek" name="nama_proyek" value="<?= $detail["nama_proyek"]; ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info" name="form_proyekadd_save"><i class="icon-ok-sign icon-white"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script type="text/javascript">
    function formProyekAddClear() {
        $('input[name="kode_proyek"]').val("");
        $('select[name="id_katproyek"]').val("");
        $('select[name="jenisproyek_id"]').val("");
        $('select[name="id_subunitkerja"]').val("");
        $('input[name="nama_proyek"]').val("");
    }
    
    $(document).ready(function() {
        $('button[name="form_proyekadd_save"]').bind('click', function() {
            var _id = $('input[name="id"]').val();
            var _kode_proyek = $('input[name="kode_proyek"]').val();
            var _id_katproyek = $('select[name="id_katproyek"]').val();
            var _jenisproyek_id = $('select[name="jenisproyek_id"]').val();
            var _id_subunitkerja = $('select[name="id_subunitkerja"]').val();
            var _nama_proyek = $('input[name="nama_proyek"]').val();
            
            $.ajax({
                url : root + 'mod_proyek/proyek_edit',
                type : 'post',
                dataType : 'json',
                data: {
                    id:_id,
                    kode_proyek:_kode_proyek,
                    id_katproyek:_id_katproyek,
                    jenisproyek_id:_jenisproyek_id,
                    id_subunitkerja:_id_subunitkerja,
                    nama_proyek:_nama_proyek
                },
                beforeSend : function() {
                    $(this).attr('disable', true);
                },
                complete : function() {
                    $(this).attr('disabled',false);
                },
                success : function(json) {
                    $('div.alert').remove();
                    if (json['error']) {
                        $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['error']+'</div>');
                        $('div.alert').fadeIn('slow');
                    }
                    if (json['success']) {
                        $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+json['success']+'</div>');
                        $('div.alert').fadeIn('slow');
                        formProyekAddClear();
                    }
                    if (json['redirect']) {
                        location = json['redirect'];
                    }
                }
            });
        });
    });
</script>