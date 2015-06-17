<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-kyash" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-kyash"
                      class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label"
                               for="public_api_id"><?php echo $entry_public_api_id; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="kyash_public_api_id" value="<?php echo $public_api_id; ?>"
                                   placeholder="<?php echo $entry_public_api_id; ?>" id="public_api_id"
                                   class="form-control"/>
                            <?php if ($error_public_api_id) { ?>
                            <div class="text-danger"><?php echo $error_public_api_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label"
                               for="api_secrets"><?php echo $entry_api_secrets; ?></label>

                        <div class="col-sm-10">
                            <input type="password" name="kyash_api_secrets" value="<?php echo $api_secrets; ?>"
                                   placeholder="<?php echo $entry_api_secrets; ?>" id="api_secrets"
                                   class="form-control"/>
                            <?php if ($error_api_secrets) { ?>
                            <div class="text-danger"><?php echo $error_api_secrets; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label"
                               for="callback_secret"><?php echo $entry_callback_secret; ?></label>

                        <div class="col-sm-10">
                            <input type="password" name="kyash_callback_secret" value="<?php echo $callback_secret; ?>"
                                   placeholder="<?php echo $entry_callback_secret; ?>" id="callback_secret"
                                   class="form-control"/>
                            <?php if ($error_callback_secret) { ?>
                            <div class="text-danger"><?php echo $error_callback_secret; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="hmac_secret"><?php echo $entry_hmac_secret; ?></label>

                        <div class="col-sm-10">
                            <input type="password" name="kyash_hmac_secret" value="<?php echo $hmac_secret; ?>"
                                   placeholder="<?php echo $entry_hmac_secret; ?>" id="hmac_secret" class="form-control"/>
                            <?php if ($error_hmac_secret) { ?>
                            <div class="text-danger"><?php echo $error_hmac_secret; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_callback_url; ?></label>

                        <div class="col-sm-10"><?php echo $callback_url; ?></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="pg_text"><span data-toggle="tooltip"
                                                   title="<?php echo $help_pg_text; ?>"><?php echo $entry_pg_text; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="kyash_pg_text" id="pg_text"
                                   placeholder="<?php echo $entry_pg_text; ?>" class="form-control" value="<?php echo $pg_text; ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="instructions"><?php echo $entry_instructions; ?></label>

                        <div class="col-sm-10">
                            <textarea cols="80" rows="8" name="kyash_instructions"
                                      class="form-control"><?php echo $instructions; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip"
                                                                                      title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="kyash_total" value="<?php echo $kyash_total; ?>"
                                   placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>

                        <div class="col-sm-10">
                            <select name="kyash_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] === $kyash_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                        selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>

                        <div class="col-sm-10">
                            <select name="kyash_status" id="input-status" class="form-control">
                                <?php if ($kyash_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-sort-order"><?php echo $entry_sort_order; ?></label>

                        <div class="col-sm-10">
                            <input type="text" name="kyash_sort_order" value="<?php echo $kyash_sort_order; ?>"
                                   placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order"
                                   class="form-control"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?> 
