<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


//載入公用函數
@include_once '/website/include/pub_function.php';

//連結資料
@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	$memberID				= trim($aFormValues['memberID']);
	$auto_seq				= trim($aFormValues['auto_seq']);
	$fees_item				= trim($aFormValues['fees_item']);
	$feed_type_list			= trim($aFormValues['feed_type_list']);
	$est_feed_start_date	= trim($aFormValues['est_feed_start_date']);
	$feed_start_date		= trim($aFormValues['feed_start_date']);
	$feed_end_date			= trim($aFormValues['feed_end_date']);
	$return_item			= trim($aFormValues['return_item']);
	$return_type_list		= trim($aFormValues['return_type_list']);
	$est_return_start_date	= trim($aFormValues['est_return_start_date']);
	$return_start_date		= trim($aFormValues['return_start_date']);
	$return_end_date		= trim($aFormValues['return_end_date']);
	$case_id				= trim($aFormValues['case_id']);
	
	/*
	if (trim($aFormValues['engineering_overview']) == "")	{
		$objResponse->script("jAlert('警示', '請輸入工程概況', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	*/

	//存入實體資料庫中
	$mDB = "";
	$mDB = new MywebDB();
	$mDB2 = "";
	$mDB2 = new MywebDB();

	$Qry="UPDATE overview_material_sub set
			 fees_item			= '$fees_item'
			,feed_type			= '$feed_type_list'
			,est_feed_start_date	= '$est_feed_start_date'
			,feed_start_date	= '$feed_start_date'
			,feed_end_date		= '$feed_end_date'
			,return_item 		= '$return_item'
			,return_type		= '$return_type_list'
			,est_return_start_date	= '$est_return_start_date'
			,return_start_date	= '$return_start_date'
			,return_end_date	= '$return_end_date'
			,makeby				= '$memberID'
			,last_modify		= now()
			where auto_seq = '$auto_seq'";
			
	$mDB->query($Qry);

	// 更新主檔
    $Qry3="UPDATE CaseManagement 
           SET last_modify8 = NOW(), makeby8 = '$memberID' 
           WHERE case_id = '$case_id'";
    $mDB2->query($Qry3);
	$mDB->remove();
	$mDB2->remove();

	$objResponse->script("setSave();");
	$objResponse->script("parent.overview_material_sub_Draw();");

	$objResponse->script("art.dialog.tips('已存檔!',1);");
	$objResponse->script("parent.$.fancybox.close();");
		
	
	return $objResponse;
}

$xajax->processRequest();


$auto_seq = $_GET['auto_seq'];


$mDB = "";
$mDB = new MywebDB();


$fm = $_GET['fm'];

$mess_title = $title;

$feed_type_list = array();
$return_type_list = array();

$Qry="SELECT * FROM overview_material_sub
WHERE auto_seq = '$auto_seq'";
$mDB->query($Qry);


$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$case_id = $row['case_id'];
	$seq = $row['seq'];
	$floor = $row['floor'];
	$fees_item = $row['fees_item'];
	$feed_type = $row['feed_type'];
	$est_feed_start_date = $row['est_feed_start_date'];
	$feed_start_date = $row['feed_start_date'];
	$feed_end_date = $row['feed_end_date'];
	$return_item = $row['return_item'];
	$return_type = $row['return_type'];
	$est_return_start_date = $row['est_return_start_date'];
	$return_start_date = $row['return_start_date'];
	$return_end_date = $row['return_end_date'];

	$feed_type_list = explode(',', $feed_type);
	$return_type_list = explode(',', $return_type);

}


//載入 進料項目
$Qry="select caption from items where pro_id = 'in_return_items' order by pro_id,orderby";
$mDB->query($Qry);
$select_fees_item = "";
$select_fees_item .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_caption = $row['caption'];
		$select_fees_item .= "<option value=\"$ch_caption\" ".mySelect($ch_caption,$fees_item).">$ch_caption</option>";
	}
}

//載入 退料項目
$Qry="select caption from items where pro_id = 'in_return_items' order by pro_id,orderby";
$mDB->query($Qry);
$select_return_item = "";
$select_return_item .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_caption = $row['caption'];
		$select_return_item .= "<option value=\"$ch_caption\" ".mySelect($ch_caption,$return_item).">$ch_caption</option>";
	}
}


$series_feed_type_list = json_encode($feed_type_list);
$series_return_type_list = json_encode($return_type_list);

$select_list = array();

$Qry="select * from items where pro_id = 'feed_type' order by orderby";
$mDB->query($Qry);
if ($mDB->rowCount() > 0) {
    //已找到符合資料
	while ($row=$mDB->fetchRow(2)) {
		$caption = $row['caption'];
		$orderby = $row['orderby'];

		$select_list[] = $caption;

	}

}

$select_return_type_list = array();

$Qry="select * from items where pro_id = 'feed_type' order by orderby";
$mDB->query($Qry);
if ($mDB->rowCount() > 0) {
    //已找到符合資料
	while ($row=$mDB->fetchRow(2)) {
		$caption = $row['caption'];
		$orderby = $row['orderby'];

		$select_return_type_list[] = $caption;

	}

}



$mDB->remove();

$series_select_list = json_encode($select_list);
$series_select_return_type_list = json_encode($select_return_type_list);



$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="save" class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-check-circle"></i>&nbsp;存檔</button>
	<button id="cancel" class="btn btn-secondary display_none" type="button" onclick="setCancel();" style="padding: 5px 15px;"><i class="bi bi-x-circle"></i>&nbsp;取消</button>
	<button id="close" class="btn btn-danger" type="button" onclick="parent.overview_material_sub_Draw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;
	
$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	max-width: 800px; !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:200px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:500px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

</style>

EOT;

} else {
	$isMobile = 1;

$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

</style>
EOT;

}



$show_center=<<<EOT
<!-- Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

$style_css
<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start" style="margin-top: 5px;">
			$mess_title
		</div>
		<div class="float-end" style="margin-top: -5px;">
			$show_savebtn
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="modifyForm" name="modifyForm" enctype="multipart/form-data" action="javascript:void(null);">
			<div class="w-100 mb-5">
				<div class="field_container3">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">樓層進料:</div>
								<div class="field_div2">
									<div style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$floor</div>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">進料項目:</div> 
								<div class="field_div2">
									<select id="fees_item" name="fees_item" placeholder="請選擇進料項目" class="w-100" style="max-width:250px;">
										$select_fees_item
									</select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">進料類別:</div> 
								<div class="field_div2">
									<select class="form-select form-select-lg select2" multiple="multiple" id="feed_type" name="feed_type" data-placeholder="請選擇進料類別" data-width="400px" onchange="setEdit();"></select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">預估進料開始時間:</div> 
								<div class="field_div2">
									<div class="input-group" id="est_feed_start_date" style="width:100%;max-width:250px;">
										<input type="text" class="form-control" name="est_feed_start_date" placeholder="請輸入預估進料開始日期" aria-describedby="est_feed_start_date" value="$est_feed_start_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#est_feed_start_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#est_feed_start_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">進料開始日期:</div> 
								<div class="field_div2">
									<div class="input-group" id="feed_start_date" style="width:100%;max-width:250px;">
										<input type="text" class="form-control" name="feed_start_date" placeholder="請輸入進料開始日期" aria-describedby="feed_start_date" value="$feed_start_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#feed_start_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#feed_start_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">進料結束日期:</div> 
								<div class="field_div2">
									<div class="input-group" id="feed_end_date" style="width:100%;max-width:250px;">
										<input type="text" class="form-control" name="feed_end_date" placeholder="請輸入進料結束日期" aria-describedby="feed_end_date" value="$feed_end_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#feed_end_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#feed_end_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">退料項目:</div> 
								<div class="field_div2">
									<select id="return_item" name="return_item" placeholder="請選擇退料項目" class="w-100" style="max-width:250px;">
										$select_return_item
									</select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">退料類別:</div> 
								<div class="field_div2">
									<select class="form-select form-select-lg select3" multiple="multiple" id="return_type" name="return_type" data-placeholder="請選擇退料類別" data-width="400px" onchange="setEdit();"></select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">預估退料開始時間:</div> 
								<div class="field_div2">
									<div class="input-group" id="est_return_start_date" style="width:100%;max-width:250px;">
										<input type="text" class="form-control" name="est_return_start_date" placeholder="請輸入預估退料開始日期" aria-describedby="est_return_start_date" value="$est_return_start_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#est_return_start_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#est_return_start_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">退料開始日期:</div> 
								<div class="field_div2">
									<div class="input-group" id="return_start_date" style="width:100%;max-width:250px;">
										<input type="text" class="form-control" name="return_start_date" placeholder="請輸入退料開始日期" aria-describedby="return_start_date" value="$return_start_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#return_start_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#return_start_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">退料開始日期:</div> 
								<div class="field_div2">
									<div class="input-group" id="return_end_date" style="width:100%;max-width:250px;">
										<input type="text" class="form-control" name="return_end_date" placeholder="請輸入退料開始日期" aria-describedby="return_end_date" value="$return_end_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#return_end_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#return_end_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div> 
							</div> 
						</div>
					</div>
					<div>
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="memberID" value="$memberID" />
						<input type="hidden" name="auto_seq" value="$auto_seq" />
						<input type="hidden" name="case_id" value="$case_id" />
						<input type="hidden" id="feed_type_list" name="feed_type_list" value="$feed_type_list" />
						<input type="hidden" id="return_type_list" name="return_type_list" value="$return_type_list" />
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {

	// 獲取 select 元素
	var selectedValue1 = $('#feed_type').val();
	$('#feed_type_list').val(selectedValue1);

	// 獲取 select 元素
	var selectedValue2 = $('#return_type').val();
	$('#return_type_list').val(selectedValue2);

	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function setEdit() {
	$('#close', window.document).addClass("display_none");
	$('#cancel', window.document).removeClass("display_none");
}

function setCancel() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
	document.forms[0].reset();
}

function setSave() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
}

</script>
<script>

	var series_select_list = JSON.parse('$series_select_list');

	$( '.select2' ).select2( {
		theme: "bootstrap-5",
		data: series_select_list,
		width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
		placeholder: $( this ).data( 'placeholder' ),
		closeOnSelect: false,
		selectionCssClass: 'select2--large',
    	dropdownCssClass: 'select2--large',
	} );	

	var series_feed_type_list = JSON.parse('$series_feed_type_list');
	$("#feed_type").val(series_feed_type_list).select2();

</script>
<script>

	var series_select_return_type_list = JSON.parse('$series_select_return_type_list');

	$( '.select3' ).select2( {
		theme: "bootstrap-5",
		data: series_select_return_type_list,
		width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
		placeholder: $( this ).data( 'placeholder' ),
		closeOnSelect: false,
		selectionCssClass: 'select2--large',
    	dropdownCssClass: 'select2--large',
	} );	

	var series_return_type_list = JSON.parse('$series_return_type_list');
	$("#return_type").val(series_return_type_list).select2();

</script>
EOT;

?>