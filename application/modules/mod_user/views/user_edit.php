<div class="content">
    <?= form_open("mod_user/user_edit"); ?>
    <input type="hidden" name="id" value="<?= $detail["user_id"]; ?>" />
    <input type="hidden" name="userdata_id" value="<?= $detail["userdata_id"]; ?>" />
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> User Edit</h4></div>
                <div class="basic box_content form-horizontal">
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="username">Username</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="username" name="username" value="<?= $detail["username"]; ?>" READONLY />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="password">Password</label>
                            <div class="controls">
                                <button type="button" class="btn" id="form_user_edit_changepassword">Change Password</button>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="fullname">Nama Lengkap</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="fullname" name="fullname" value="<?= $detail["fullname"]; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="email">Email</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="email" name="email" value="<?= $detail["email"]; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="enabled">Enabled</label>
                            <div class="controls">
                                <?= form_dropdown('enabled', $active, $detail["enabled"], 'id="enabled"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="username">Aktif</label>
                            <div class="controls">
                                <?= form_dropdown('active', $active, $detail["active"], 'id="active"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="id_group">Group Akses</label>
                            <div class="controls">
                                <?= form_dropdown('id_group', $id_group, $detail["id_group"], 'id="id_group"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="username">Kategori Unit Kerja</label>
                            <div class="controls">
                                <?= form_dropdown('is_proyek', $is_proyek, $detail["is_proyek"]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" id="label_response"></label>
                            <div class="controls" id="response">
                                <div id="form_user_add"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info" name="form_useradd_save"><i class="icon-ok-sign icon-white"></i> Save</button>
<!--                                    <button type="button" class="btn"><i class="icon-remove"></i>Cancel</button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/msdropdown/css/msdropdown/dd.css" />
<script src="<?= base_url(); ?>assets/msdropdown/js/msdropdown/jquery.dd.min.js"></script>
<script type="text/javascript">
    function formUserAddClear() {
        $('input[name="username"]').val("");
        $('input[name="password"]').val("");
        $('input[name="repassword"]').val("");
        $('input[name="fullname"]').val("");
        $('input[name="email"]').val("");
        $('select[name="enabled"]').val("");
        $('select[name="active"]').val("");
        $('select[name="id_group"]').val("");
        $('select[name="is_proyek"]').val("");
        $('select[name="id_relasi"]').val("");
    }

    function getUnitProyek(selectedvalue) {
        selectedvalue = typeof selectedvalue !== 'undefined' ? selectedvalue : '';
        var id = $('select[name="is_proyek"]').val();
        $.ajax({
            url: root + 'mod_user/getUnitProyek',
            dataType: 'json',
            type: 'post',
            data: {id: id},
            success: function(json) {
                $('label#label_response').html(json['label']);
                $("#form_user_add").msDropDown({byJson: {data: json['data'], name: 'id_relasi'}}).data("dd").setIndexByValue(selectedvalue);
            }
        });
    }

    $(document).ready(function() {
        var selectedid = <?= $detail["id_relasi"]; ?>;
        getUnitProyek(selectedid);

        $('button[id="form_user_edit_changepassword"]').click(function() {
            var id = $('input[name="id"]').val();
            showUrlInDialog(root + "mod_user/user_changepass?id=" + id, "form_chage_password", "Change Password", "form_chage_password", 500, 300);
        });

        $('select[name="is_proyek"]').change(function() {
            var id = $('select[name="is_proyek"]').val();
            $("#form_user_add").remove();
            $('div#response').append('<div id="form_user_add"></div>');
            $.ajax({
                url: root + 'mod_user/getUnitProyek',
                dataType: 'json',
                type: 'post',
                data: {id: id},
                success: function(json) {
                    $('label#label_response').html(json['label']);
                    $("#form_user_add").msDropDown({byJson: {data: json['data'], name: 'id_relasi'}}).data("dd");
                }
            });
        });

        $('button[name="form_useradd_save"]').bind('click', function() {
            var _id = $('input[name="id"]').val();
            var _userdata_id = $('input[name="userdata_id"]').val();
            var _username = $('input[name="username"]').val();
            var _password = $('input[name="password"]').val();
            var _repassword = $('input[name="repassword"]').val();
            var _fullname = $('input[name="fullname"]').val();
            var _email = $('input[name="email"]').val();
            var _enabled = $('select[name="enabled"]').val();
            var _active = $('select[name="active"]').val();
            var _id_group = $('select[name="id_group"]').val();
            var _is_proyek = $('select[name="is_proyek"]').val();
            var _id_relasi = $('select[name="id_relasi"]').val();

            $.ajax({
                url: root + "mod_user/user_edit",
                dataType: 'json',
                type: 'post',
                data: {
                    id: _id,
                    userdata_id: _userdata_id,
                    username: _username,
                    password: _password,
                    repassword: _repassword,
                    fullname: _fullname,
                    email: _email,
                    enabled: _enabled,
                    active: _active,
                    id_group: _id_group,
                    is_proyek: _is_proyek,
                    id_relasi: _id_relasi
                },
                beforeSend: function() {
                    $(this).attr('disabled', true);
                },
                complete: function() {
                    $(this).attr('disabled', false);
                },
                success: function(json) {
                    $('div.alert').remove();

                    if (json['error']) {
                        $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['error'] + '</div>');
                        $('div.alert').fadeIn('slow');
                    }

                    if (json['success']) {
                        $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['success'] + '</div>');
                        $('div.alert').fadeIn('slow');
                        formUserAddClear();
                    }

                    if (json['redirect']) {
                        location = json['redirect'];
                    }
                }
            });
        });
    });
</script>