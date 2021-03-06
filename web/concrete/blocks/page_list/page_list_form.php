<?php defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
$form = Loader::helper('form/page_selector');
?>
<div class="row pagelist-form">
    <div class="col-xs-6">

        <input type="hidden" name="pageListToolsDir" value="<?= Loader::helper('concrete/urls')->getBlockTypeToolsURL($bt) ?>/"/>

        <fieldset>
            <legend><?= t('Settings') ?></legend>
        </fieldset>

        <div class="form-group">
            <label class='control-label'><?= t('Display') ?></label>

            <div class="input-group">
                <input type="text" name="num" value="<?= $num ?>" class="form-control">
                <span class="input-group-addon"> <?= t('Pages') ?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?= t('Page Type') ?></label>
            <?php
            $ctArray = PageType::getList();

            if (is_array($ctArray)) {
                ?>
                <select class="form-control" name="ptID" id="selectPTID">
                    <option value="0">** <?php echo t('All') ?> **</option>
                    <?php
                    foreach ($ctArray as $ct) {
                        ?>
                        <option
                            value="<?= $ct->getPageTypeID() ?>" <? if ($ptID == $ct->getPageTypeID()) { ?> selected <? } ?>>
                            <?= $ct->getPageTypeName() ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
            ?>
        </div>

        <legend><?= t('Filters') ?></legend>
        <div class="checkbox">
            <label>
                <input <? if (!is_object($cadf)) { ?> disabled <? } ?> type="checkbox" name="displayFeaturedOnly"
                                                                       value="1" <? if ($displayFeaturedOnly == 1) { ?> checked <? } ?>
                                                                       style="vertical-align: middle"/>
                <?= t('Featured pages only.') ?>
            </label>
            <? if (!is_object($cadf)) { ?>
                <span class="help-block"><?=
                    t(
                        '(<strong>Note</strong>: You must create the "is_featured" page attribute first.)'); ?></span>
            <? } ?>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="displayAliases"
                       value="1" <? if ($displayAliases == 1) { ?> checked <? } ?> />
                <?= t('Display page aliases.') ?>
            </label>
        </div>

        <legend><?= t('Pagination') ?></legend>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="paginate" value="1" <? if ($paginate == 1) { ?> checked <? } ?> />
                <?= t('Display pagination interface if more items are available than are displayed.') ?>
            </label>
        </div>

        <legend><?= t('Location') ?></legend>
        <div class="radio">
            <label>
                <input type="radio" name="cParentID" id="cEverywhereField"
                       value="0" <? if ($cParentID == 0) { ?> checked<? } ?> />
                <?= t('Everywhere') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="cParentID" id="cThisPageField"
                       value="<?= $c->getCollectionID() ?>" <? if ($cParentID == $c->getCollectionID() || $cThis) { ?> checked<? } ?>>
                <?= t('Beneath this page') ?>
            </label>
         </div>
        <div class="radio">
            <label>
                <input type="radio" name="cParentID" id="cOtherField"
                       value="OTHER" <? if ($isOtherPage) { ?> checked<? } ?>>
                <?= t('Beneath another page') ?>
            </label>
        </div>

        <div class="ccm-page-list-page-other" <? if (!$isOtherPage) { ?> style="display: none" <? } ?>>

            <div class="form-group">
                <?= $form->selectPage('cParentIDValue', $isOtherPage ? $cParentID : false); ?>
            </div>
        </div>

        <div class="ccm-page-list-all-descendents"
             style="<?php echo (!$isOtherPage && !$cThis) ? ' display: none;' : ''; ?>">
            <div class="form-group">
                <div class="checkbox">
                <label>
                    <input type="checkbox" name="includeAllDescendents" id="includeAllDescendents"
                           value="1" <?php echo $includeAllDescendents ? 'checked="checked"' : '' ?> />
                    <?php echo t('Include all child pages') ?>
                </label>
                </div>
            </div>
        </div>

        <legend><?= t('Sort') ?></legend>
        <div class="form-group">
            <select name="orderBy" class="form-control">
                <option value="display_asc" <? if ($orderBy == 'display_asc') { ?> selected <? } ?>>
                    <?= t('Sitemap order') ?>
                </option>
                <option value="chrono_desc" <? if ($orderBy == 'chrono_desc') { ?> selected <? } ?>>
                    <?= t('Most recent first') ?>
                </option>
                <option value="chrono_asc" <? if ($orderBy == 'chrono_asc') { ?> selected <? } ?>>
                    <?= t('Earliest first') ?>
                </option>
                <option value="alpha_asc" <? if ($orderBy == 'alpha_asc') { ?> selected <? } ?>>
                    <?= t('Alphabetical order') ?>
                </option>
                <option value="alpha_desc" <? if ($orderBy == 'alpha_desc') { ?> selected <? } ?>>
                    <?= t('Reverse alphabetical order') ?>
                </option>
            </select>
        </div>

        <legend><?= t('Output') ?></legend>
        <div class="form-group">
            <label class="control-label"><?= t('Provide RSS Feed') ?></label>
            <div class="radio">
                <label>
                    <input type="radio" name="rss" class="rssSelector"
                           value="0" <?= ($rss ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <input id="ccm-pagelist-rssSelectorOn" type="radio" name="rss" class="rssSelector"
                           value="1" <?= ($rss ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                </label>
             </div>
            <div id="ccm-pagelist-rssDetails" <?= ($rss ? "" : "style=\"display:none;\"") ?>>
                <div class="form-group">
                    <label class="control-label"><?= t('RSS Feed Title') ?></label>
                    <input class="form-control" id="ccm-pagelist-rssTitle" type="text" name="rssTitle"
                           value="<?= $rssTitle ?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= t('RSS Feed Description') ?></label>
                    <textarea name="rssDescription" class="form-control"><?= $rssDescription ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?= t('Include Page Description') ?></label>
            <div class="radio">
                <label>
                    <input type="radio" name="includeDescription" class="rssSelector"
                           value="0" <?= ($includeDescription ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="includeDescription"
                           value="1" <?= ($includeDescription ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                </label>
            </div>
            <div class="ccm-page-list-truncate-description" <?= ($includeDescription ? "" : "style=\"display:none;\"") ?>>
                <label class="control-label"><?=t('Display Truncated Description')?></label>
                <div class="input-group">
                <span class="input-group-addon">
                    <input id="ccm-pagelist-truncateSummariesOn" name="truncateSummaries" type="checkbox"
                           value="1" <?= ($truncateSummaries ? "checked=\"checked\"" : "") ?> />
                </span>
                    <input class="form-control" id="ccm-pagelist-truncateChars" <?= ($truncateSummaries ? "" : "disabled=\"disabled\"") ?>
                           type="text" name="truncateChars" size="3" value="<?= intval($truncateChars) ?>" />
                <span class="input-group-addon">
                    <?= t('characters') ?>
                </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?= t('Use Button For Link') ?></label>
            <div class="radio">
                <label>
                    <input type="radio" name="useButtonForLink" class="rssSelector"
                           value="0" <?= ($useButtonForLink ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="useButtonForLink"
                           value="1" <?= ($useButtonForLink ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                </label>
            </div>
            <div class="ccm-page-list-button-text" <?= ($useButtonForLink ? "" : "style=\"display:none;\"") ?>>
                <div class="form-group">
                    <label class="control-label"><?= t('Button Text') ?></label>
                    <input class="form-control" type="text" name="buttonLinkText" value="<?=$buttonLinkText?>" />
                </div>
            </div>
        </div>

        <div class="loader">
            <i class="fa fa-cog fa-spin"></i>
        </div>
    </div>

    <div class="col-xs-6" id="ccm-tab-content-page-list-preview">
        <div class="preview">
            <fieldset>
                <legend><?= t('Included Pages') ?></legend>
            </fieldset>
            <div class="render">

            </div>
            <div class="cover"></div>
        </div>
    </div>

</div>

<style type="text/css">
    div.pagelist-form div.loader {
        position: absolute;
        line-height: 34px;
    }

    div.pagelist-form div.cover {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    div.pagelist-form div.render .ccm-page-list-title {
        font-size: 12px;
        font-weight: normal;
    }

    div.pagelist-form label.checkbox,
    div.pagelist-form label.radio {
        font-weight: 300;
    }

</style>
<script type="application/javascript">
    Concrete.event.publish('pagelist.edit.open');
</script>

