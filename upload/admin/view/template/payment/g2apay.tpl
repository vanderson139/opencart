<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-skrill" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-skrill" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_environment; ?></label>
            <div class="col-sm-10">
              <?php if (!$g2apay_environment) { ?>
                <?php echo $g2apay_environment_live; ?> <input type="radio" name="g2apay_environment" value="1" id="input-secret" />
                <?php echo $g2apay_environment_test; ?> <input type="radio" name="g2apay_environment" value="0" id="input-secret" checked="checked" />
              <?php } else { ?>
                <?php echo $g2apay_environment_live; ?> <input type="radio" name="g2apay_environment" value="1" id="input-secret" checked="checked" />
                <?php echo $g2apay_environment_test; ?> <input type="radio" name="g2apay_environment" value="0" id="input-secret" />
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-secret"><?php echo $entry_secret; ?></label>
            <div class="col-sm-10">
              <input type="text" name="g2apay_secret" value="<?php echo $g2apay_secret; ?>" placeholder="<?php echo $entry_secret; ?>" id="input-email" class="form-control" />
              <?php if ($error_secret) { ?>
              <div class="text-danger"><?php echo $error_secret; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-secret"><?php echo $entry_api_hash; ?></label>
            <div class="col-sm-10">
              <input type="text" name="g2apay_api_hash" value="<?php echo $g2apay_api_hash; ?>" placeholder="<?php echo $entry_api_hash; ?>" id="input-email" class="form-control" />
              <?php if ($error_api_hash) { ?>
              <div class="text-danger"><?php echo $error_api_hash; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-secret"><?php echo $entry_ipn_uri; ?></label>
            <div class="col-sm-10">
              <input type="text" name="g2apay_ipn_uri" value="<?php echo $g2apay_ipn_uri; ?>" placeholder="<?php echo $entry_ipn_uri; ?>" id="input-email" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="g2apay_status" id="input-status" class="form-control">
                <?php if ($g2apay_status) { ?>
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
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="g2apay_order_status_id" id="input-order-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $g2apay_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 