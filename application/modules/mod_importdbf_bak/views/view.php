<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>jQuery Uploadify Demo</title>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/uploadify/uploadify.css" />
        <script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
        <script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?= base_url(); ?>assets/uploadify/jquery.uploadify.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){                                                         
                $("#upload").uploadify({
                    uploader: '<?php echo base_url(); ?>assets/uploadify/uploadify.swf',
                    script: '<?php echo base_url(); ?>assets/uploadify/uploadify.php',
                    cancelImg: '<?php echo base_url(); ?>assets/uploadify/uploadify-cancel.png',
                    folder: '/uploads',
                    scriptAccess: 'always',
                    multi: true,
                    'onError' : function (a, b, c, d) {
                        if (d.status == 404)
                            alert('Could not find upload script.');
                        else if (d.type === "HTTP")
                            alert('error '+d.type+": "+d.status);
                        else if (d.type ==="File Size")
                            alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
                        else
                            alert('error '+d.type+": "+d.text);
                    },
                    'onComplete'   : function (event, queueID, fileObj, response, data) {
                        $.post('<?php echo site_url('mod_importdbf/uploadify'); ?>',{filearray: response},function(info){
                            $("#target").append(info);                                                                                                                                                
                        });                                                                                     
                    }
                });                             
            });
        </script>
    </head>

    <body>
        <h1>Uploadify Example</h1>
        <?= form_open_multipart('mod_importdbf/index'); ?>
        <p>
            <label>File Upload</label>
            <input type="file" name="Filedata" id="upload" />
        </p>
        <p>
            <a href="javascript:$('#upload').uploadifyUpload();">Upload File(s)</a>
        </p>

        <?= form_close(); ?>
        <div id="target"></div>
    </body>
</html>