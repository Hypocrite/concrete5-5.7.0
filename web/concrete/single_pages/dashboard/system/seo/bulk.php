<?php  defined('C5_EXECUTE') or die('Access Denied');
$pageSelector = Loader::helper('form/page_selector');
$nh = Loader::helper('navigation');
$th = Loader::helper('text');
?>

<style>
    table.ccm-search-results-table {
        border-top: 1px solid #ccc;
    }

    table.ccm-search-results-table tr td {
        vertical-align: top;
    }

    table.ccm-search-results-table tr td .submit-changes {
        margin-top: 26px;
    }

    .ccm-ui .form-inline .radio input[type="radio"], .ccm-ui .form-inline .checkbox input[type="checkbox"] {
        margin-left: -20px;
    }

    .ccm-ui .form-inline .checkbox, .ccm-ui .form-inline .radio {
        margin-right: 15px;
    }
</style>
<div class="ccm-dashboard-content-full">
    <div data-search-element="wrapper">
        <form role="form" action="<?=$controller->action('view')?>" class="form-inline ccm-search-fields">
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('keywords', t('Search'))?>
                    <div class="ccm-search-field-content">
                        <div class="ccm-search-main-lookup-field">
                            <i class="fa fa-search"></i>
                            <?=$form->search('keywords', array('placeholder' => t('Keywords')))?>
                            <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('channel', t('Number of Pages to Display'))?>
                    <div class="ccm-search-field-content">
                        <?=$form->select('numResults', array(
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100',
                            '500' => '500'
                        ), Loader::helper('text')->specialchars($searchRequest['numResults']))?>
                    </div>
                </div>
            </div>
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('cParentIDSearchField', t('Parent Page'))?>
                    <div class="ccm-search-field-content">
                        <?php echo $pageSelector->selectPage('cParentIDSearchField', $cParentIDSearchField ? $cParentIDSearchField : false);?>
                    </div>
                </div>
            </div>
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('cParentAll', t('How Many Levels Below Parent?'))?>
                    <div class="ccm-search-field-content">
                        <div class="radio">
                            <label><?=$form->radio('cParentAll', 0, false)?><?=t('First Level')?></label>
                       </div>
                       <div class="radio">
                           <label><?=$form->radio('cParentAll', 1, false)?><?=t('All Levels')?></label>
                       </div>
                    </div>
                </div>
            </div>
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('cParentAll', t('Filter By:'))?>
                    <div class="ccm-search-field-content">
                        <div class="checkbox">
                            <label> <?php echo $form->checkbox('noDescription', 1, $descCheck);  ?><?=t('No Meta Description'); ?></label>
                        </div>
                        <div class="checkbox">
                            <label> <?php echo $form->checkbox('noKeywords', 1, $keywordCheck);  ?><?=t('No Meta Keywords'); ?></label>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <div data-search-element="results">
    <?php
    if (count($pages) > 0) { ?>
        <table class="ccm-search-results-table">
            <tbody>
            <?php $i = 0;
                foreach($pages as $cobj) {
                    $cpobj = new Permissions($cobj);
                    $i++;
                    $cID = $cobj->getCollectionID();
                    ?>
                <tr class="ccm-seoRow-<?php echo $cID; ?> ccm-seo-rows">
                        <td>
                            <strong><?php echo t('Page Name'); ?></strong><br/>
                            <?php echo $cobj-> getCollectionName() ? $cobj->getCollectionName() : ''; ?><br/><br/>
                            <strong><?php echo t('Page Type'); ?></strong><br/>
                            <?php echo $cobj->getPageTypeName() ? $cobj->getPageTypeName() : t('Single Page'); ?><br/><br/>
                            <strong><?php echo t('Modified'); ?></strong><br/>
                            <?php echo $cobj->getCollectionDateLastModified() ? $cobj->getCollectionDateLastModified(DATE_APP_GENERIC_MDYT) : ''; ?>
                        </td>
                        <td>
                            <div class="form-group">
                                <label><?php echo t('Meta Title'); ?></label>
                                <?php $seoPageTitle = $cobj->getCollectionName();
                                $seoPageTitle = htmlspecialchars($seoPageTitle, ENT_COMPAT, APP_CHARSET);
                                $autoTitle = sprintf(PAGE_TITLE_FORMAT, SITE, $seoPageTitle);
                                $titleInfo = array('title' => $cID);
                                if(strlen($cobj->getAttribute('meta_title')) <= 0) {
                                    $titleInfo[style] = 'background: whiteSmoke';
                                }
                                echo $form->text('meta_title', $cobj->getAttribute('meta_title') ? $cobj->getAttribute('meta_title') : $autoTitle, $titleInfo);
                                echo $titleInfo[style] ? '<span class="help-inline">' . t('Default value. Click to edit.') . '</span>' : '' ?>
                            </div>
                            <div class="form-group">
                                <label><?php echo t('Meta Description'); ?></label>
                                <?php $pageDescription = $cobj->getCollectionDescription();
                                $autoDesc = htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET);
                                $descInfo = array('title' => $cID);
                                if(strlen($cobj -> getAttribute('meta_description')) <= 0) {
                                    $descInfo[style] = 'background: whiteSmoke';
                                }
                                echo $form->textarea('meta_description', $cobj->getAttribute('meta_description') ? $cobj->getAttribute('meta_description') : $autoDesc, $descInfo);
                                echo $descInfo[style] ? '<span class="help-inline">' . t('Default value. Click to edit.') . '</span>' : '';
                                ?>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label><?php echo t('Meta Keywords'); ?></label>
                                <?php echo $form->textarea('meta_keywords', $cobj->getAttribute('meta_keywords'), array('title' => $cID)); ?>
                            </div>
                            <? if ($cobj->getCollectionID() != HOME_CID) { ?>

                                <div class="form-group">
                                    <label><?php echo t('Slug'); ?></label>
                                    <?php echo $form->text('collection_handle', $cobj->getCollectionHandle(), array('title' => $cID, 'class' => 'collectionHandle')); ?>
                                    <?php
                                    Page::rescanCollectionPath($cID);
                                    $path = $cobj->getCollectionPath();
                                    $tokens = explode('/', $path);
                                    $lastkey = array_pop(array_keys($tokens));
                                    $tokens[$lastkey] = '<strong class="collectionPath">' . $tokens[$lastkey] . '</strong>';
                                    $untokens = implode('/', $tokens);
                                    ?><a class="help-inline url-path" href="<?php echo $nh->getLinkToCollection($cobj); ?>" target="_blank"><?php echo BASE_URL . DIR_REL . $untokens; ?></a><?php
                                    ?>
                                </div>
                            <? } ?>
                        </td>
                        <td style="position: relative;">
                            <form id="seoForm<?php echo $cID; ?>" action="<?php echo View::url('/dashboard/system/seo/page_data/', 'saveRecord')?>" method="post" class="pageForm">
                                <a class="btn btn-default submit-changes" data-cID="<?php echo $cobj->getCollectionID() ?>"><?php echo t('Save') ?></a>
                            </form>
                            <img style="display: none; position: absolute; top: 20px; right: 20px;" id="throbber<?php echo $cID ?>"  class="throbber" src="<?php echo ASSETS_URL_IMAGES . '/throbber_white_32.gif' ?>" />
                        </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button id="allSeoSubmit" class="btn pull-right btn-success"><?php echo t('Save All') ?></button>
            </div>
        </div>
        <?php } else { ?>
        <div class="ccm-results-list-none"><?php echo t('No pages found.')?></div>
        <?php  } ?>
        <script type="text/javascript">
        $(document).ready(function() {
            $('.ccm-seo-rows').each(function(){
               $(this).find('input, textarea').change(function(){
                  $(this).addClass('hasChanged');
                  $(this).closest('tr').find('.btn').addClass('btn-success');
               });
            });
            $('.submit-changes').click(function(event) {
                event.preventDefault();
                var iterator = $(this).attr('data-cID');
                var throbber = $('.ccm-seoRow-'+iterator+' .throbber');
                console.log(throbber);
                throbber.show();
                var data = {};
                data.cID = iterator;
                data.meta_title = $('.ccm-seoRow-'+iterator+' input[name="meta_title"].hasChanged').val();
                data.meta_description = $('.ccm-seoRow-'+iterator+' textarea[name="meta_description"]').val();
                data.meta_keywords = $('.ccm-seoRow-'+iterator+' textarea[name="meta_keywords"]').val();
                data.collection_handle = $('.ccm-seoRow-'+iterator+' input[name="collection_handle"]').val();

                $.ajax({
                    url: '<?php echo $view->action("saveRecord") ?>',
                    dataType: 'json',
                    type: 'post',
                    data: data,
                    success:function(res) {
                        if(res.success) {
                            var cID = res.cID;
                            throbber.hide();
                            $('.ccm-seoRow-'+cID+' .collectionPath').html(res.newPath);
                            $('.ccm-seoRow-'+cID+' .collectionHandle').val(res.cHandle);
                            $('.hasChanged').removeClass('.hasChanged');
                            $('tr .btn-success').removeClass('btn-success');
                        } else {
                            alert('<?php echo t('An error occured while saving.'); ?>');
                        }
                    }
                });
                throbber.show();
            });

            $('#allSeoSubmit').click(function() {
                $('.submit-changes.btn-success').click();
            });

            $('.ccm-search-results-table tr td input, .ccm-search-results-table tr td textarea').not('.collectionHandle').click(function(){
                $(this).css({'background' : 'white'});
                $(this).next('.help-inline').hide();
            })
        });
        </script>
    </div>
    <? if ($pagination) { ?>
    <div style="text-align: center">
        <?=$pagination?>
    </div>
    <? } ?>
</div>