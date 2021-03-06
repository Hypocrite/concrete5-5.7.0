<?php  defined('C5_EXECUTE') or die("Access Denied.");
?>
<script>
    var CCM_EDITOR_SECURITY_TOKEN = "<?=Loader::helper('validation/token')->generate('editor')?>";
    $(document).ready(function(){
        var ccmReceivingEntry = '';
        var sliderEntriesContainer = $('.ccm-image-slider-entries');
        var _templateSlide = _.template($('#imageTemplate').html());
        var attachDelete = function($obj) {
            $obj.click(function(){
                var deleteIt = confirm('<?php echo t('Are you sure?') ?>');
                if(deleteIt == true) {
                    $(this).closest('.ccm-image-slider-entry').remove();
                    doSortCount();
                }
            });
        }

        var attachFileManagerLaunch = function($obj) {
            $obj.click(function(){
                var oldLauncher = $(this);
                ConcreteFileManager.launchDialog(function (data) {
                    ConcreteFileManager.getFileDetails(data.fID, function(r) {
                        jQuery.fn.dialog.hideLoader();
                        var file = r.files[0];
                        oldLauncher.html('<img src="' + file.thumbnailLevel1 + '" />');
                        oldLauncher.next('.image-fID').val(file.fID)
                    });
                });
            });
        }

        var doSortCount = function(){
            $('.ccm-image-slider-entry').each(function(index) {
                $(this).find('.ccm-image-slider-entry-sort').val(index);
            });
        };

       <?php if($rows) {
           foreach ($rows as $row) { ?>
           sliderEntriesContainer.append(_templateSlide({
                fID: '<?php echo $row['fID'] ?>',
                <?php if(File::getByID($row['fID'])) { ?>
                image_url: '<?php echo File::getByID($row['fID'])->getURL();?>',
                <?php } else { ?>
                image_url: '',
               <?php } ?>
                link_url: '<?php echo $row['linkURL'] ?>',
                title: '<?php echo $row['title'] ?>',
                description: '<?php echo str_replace(array("\t", "\r", "\n"), "", addslashes($row['description']))?>',
                sort_order: '<?php echo $row['sortOrder'] ?>'
            }));
        <?php }
        }?>

        doSortCount();

        //sliderEntriesContainer.sortable({
           // stop: function( event, ui ) {
             //   doSortCount();  // recount every time icon divs are resorted.
           // }
        //});

        $('.ccm-add-image-slider-entry').click(function(){
            var newSlide = sliderEntriesContainer.append(_templateSlide({
                fID: '',
                title: '',
                link_url: '',
                cID: '',
                description: '',
                sort_order: '',
                image_url: ''
            }));
            $(newSlide).find('.redactor-content').redactor({
                minHeight: '200'
            });
            attachDelete($(newSlide).find('.ccm-delete-image-slider-entry'));
            attachFileManagerLaunch($(newSlide).find('.ccm-pick-slide-image'));
            doSortCount();
        });
        attachDelete($('.ccm-delete-image-slider-entry'));
        attachFileManagerLaunch($('.ccm-pick-slide-image'));
        $(function() {  // activate redactors
            $('.redactor-content').redactor({
                minHeight: '200'
            });
        });
    });
</script>
<style>

    .ccm-image-slider-block-container .redactor_editor {
        padding: 20px;
    }
    .ccm-image-slider-block-container input[type="text"],
    .ccm-image-slider-block-container textarea {
        display: block;
        width: 100%;
    }
    .ccm-image-slider-block-container .btn-success {
        margin-bottom: 20px;
    }

    .ccm-image-slider-entries {
        padding-bottom: 30px;
    }

    .ccm-pick-slide-image {
        padding: 15px;
        cursor: pointer;
        background: #dedede;
        border: 1px solid #cdcdcd;
        text-align: center;
        vertical-align: center;
    }

    .ccm-pick-slide-image img {
        max-width: 100%;
    }

    .ccm-image-slider-entry {
        position: relative;
    }



    .ccm-image-slider-block-container i.fa-arrows {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>
<div class="ccm-image-slider-block-container">
    <legend><?php echo t('Navigation') ?></legend>
    <div class="form-group">
        <div class="radio">
            <label><input type="radio" name="navigationType" value="0" <?php echo $navigationType > 0 ? '' : 'checked' ?> /><?php echo t('Arrows') ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="radio">
            <label><input type="radio" name="navigationType" value="1" <?php echo $navigationType > 0 ? 'checked' : '' ?> /><?php echo t('Bullets') ?></label>
        </div>
    </div>

    <span class="btn btn-success ccm-add-image-slider-entry"><?php echo t('Add Entry') ?></span>
    <div class="ccm-image-slider-entries">

    </div>
</div>
<script type="text/template" id="imageTemplate">
    <div class="ccm-image-slider-entry well">
        <i class="fa fa-arrows"></i>
        <div class="form-group">
            <label><?php echo t('Image') ?></label>
            <div class="ccm-pick-slide-image">
                <% if (image_url.length > 0) { %>
                    <img src="<%= image_url %>" />
                <% } else { %>
                    <i class="fa fa-picture-o"></i>
                <% } %>
            </div>
            <input type="hidden" name="fID[]" class="image-fID" value="<%=fID%>" />
        </div>
        <div class="form-group">
            <label><?php echo t('Title') ?></label>
            <input type="text" name="title[]" value="<%=title%>" />
        </div>
        <div class="form-group">
            <label><?php echo t('Description') ?></label>
            <div class="redactor-edit-content"></div>
            <textarea style="display: none" class="redactor-content" name="description[]"><%=description%></textarea>
        </div>
        <div class="form-group">
            <label><?php echo t('URL') ?></label>
            <textarea name="linkURL[]"><%=link_url%></textarea>
        </div>
        <input class="ccm-image-slider-entry-sort" type="hidden" name="sortOrder[]" value="<%=sort_order%>"/>
        <div class="form-group">
            <span class="btn btn-danger ccm-delete-image-slider-entry"><?php echo t('Delete Entry'); ?></span>
        </div>
    </div>
</script>
