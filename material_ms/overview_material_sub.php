<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = "0";
} else {
	$isMobile = "1";
}

@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();


$xajax->registerFunction("DeleteRow");
function DeleteRow($auto_seq,$case_id,$memberID){

	$objResponse = new xajaxResponse();
	
	$mDB = "";
	$mDB = new MywebDB();
	$mDB2 = "";
	$mDB2 = new MywebDB();

	//刪除主資料
	$Qry="delete from overview_material_sub where auto_seq = '$auto_seq'";
	$mDB->query($Qry);

	// 更新主檔
    $Qry2="UPDATE CaseManagement 
           SET last_modify8 = NOW(), makeby8 = '$memberID' 
           WHERE case_id = '$case_id'";
    $mDB2->query($Qry2);
	$mDB2->remove();
	
	$mDB->remove();
	
    $objResponse->script("oTable = $('#overview_material_sub_table').dataTable();oTable.fnDraw(false)");
	$objResponse->script("autoclose('提示', '資料已刪除！', 1500);");

	return $objResponse;
	
}


$xajax->processRequest();


$fm = $_GET['fm'];
$case_id = $_GET['case_id'];
$seq = $_GET['seq'];
$seq2 = $_GET['seq2'];

$today = date("Y-m-d");

$dataTable_de = getDataTable_de();
$Prompt = getlang("提示訊息");
$Confirm = getlang("確認");
$Cancel = getlang("取消");


//取得棟別及工程說明
$overview_material_building_row = getkeyvalue2($site_db.'_info','overview_material_building',"auto_seq = '$seq2'",'building,eng_description');
$building = $overview_material_building_row['building'];
$eng_description = $overview_material_building_row['eng_description'];


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

</style>

EOT;


$show_modify_btn=<<<EOT
<div class="inline ms-5">
	<div class="btn-group" role="group" style="margin-top:-2px;">
		<button type="button" class="btn btn-danger text-nowrap" onclick="openfancybox_edit('/index.php?ch=overview_material_sub_add&case_id=$case_id&seq=$seq&seq2=$seq2&fm=$fm',800,'96%','');"><i class="bi bi-plus-circle"></i>&nbsp;新增資料</button>
		<button type="button" class="btn btn-success text-nowrap" onclick="overview_material_sub_Draw();"><i class="bi bi-arrow-repeat"></i>&nbsp;重整</button>
	</div>
</div>
EOT;



$list_view=<<<EOT
<div class="w-100 m-auto p-1 mb-5 bg-white">
	<div style="position: relative;margin: 0 0 -30px 170px;">
		<div class="inline size14 me-5">棟別：<span class="blue02 weight">$building</span></div>
		<div class="inline size14">工程說明：<span class="blue02 weight">$eng_description</span></div>
	</div>
	<table class="table table-bordered border-dark w-100" id="overview_material_sub_table" style="min-width:1160px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">樓層進料</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">進料項目</th>
				<th class="text-center text-nowrap" style="width:10%;padding: 10px;background-color: #CBF3FC;">進料類別</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">預估進料<br>開始時間</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">進料<br>開始日期</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">進料<br>結束日期</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">退料項目</th>
				<th class="text-center text-nowrap" style="width:10%;padding: 10px;background-color: #CBF3FC;">退料類別</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">預估退料<br>開始時間</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">退料<br>開始日期</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">退料<br>結束日期</th>
				<th class="text-center text-nowrap" style="width:8%;padding: 10px;background-color: #CBF3FC;">處理</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="12" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
	</table>
</div>
EOT;

$show_savebtn=<<<EOT
<div class="btn-group" role="group" style="margin-top:10px;">
	<button id="close" class="btn btn-danger" type="button" onclick="parent.material_sub_myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}
	
	
$show_center=<<<EOT

$style_css

<style type="text/css">
#overview_material_sub_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}

</style>
<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start" style="margin-top: 5px;">
			物資時程 $show_modify_btn
		</div>
		<div class="float-end" style="margin-top: -5px;">
			$show_savebtn
		</div>
	</div>
	<div id="full" class="card-body p-3" data-overlayscrollbars-initialize>
		<div id="info_container">
			$list_view
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	var oTable;
	$(document).ready(function() {
		$('#overview_material_sub_table').dataTable( {
			"processing": true,
			"serverSide": true,
			"responsive":  {
				details: true
			},//RWD響應式
			"scrollX": '$scroll',
			/*"scrollY": 600,*/
			"paging": true,
			"pageLength": 50,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"pagingType": "full_numbers",  //分页样式： simple,simple_numbers,full,full_numbers
			"searching": true,  //禁用原生搜索
			"ordering": false,
			"ajaxSource": "/smarty/templates/$site_db/$templates/sub_modal/project/func06/material_ms/server_overview_material_sub.php?site_db=$site_db&case_id=$case_id&seq=$seq&seq2=$seq2&fm=$fm",
			"language": {
						"sUrl": "$dataTable_de"
						/*"sUrl": '//cdn.datatables.net/plug-ins/1.12.1/i18n/zh-HANT.json'*/
					},
			"fixedHeader": true,
			"fixedColumns": {
        		left: 1,
    		},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) { 

				//樓層進料
				var floor = "";
				if (aData[3] != null && aData[3] != "")
					floor = aData[3];

				$('td:eq(0)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+floor+'</div>' );

				//進料項目
				var fees_item = "";
				if (aData[13] != null && aData[13] != "")
					fees_item = aData[13];

				$('td:eq(1)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+fees_item+'</div>' );

				//進料類別
				var feed_type = "";
				if (aData[4] != null && aData[4] != "")
					feed_type = aData[4];

				$('td:eq(2)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+feed_type+'</div>' );

				//預估進料開始時間
				var est_feed_start_date = "";
				if (aData[14] != null && aData[14] != "" && aData[14] != "0000-00-00")
					est_feed_start_date = aData[14];

				$('td:eq(3)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+est_feed_start_date+'</div>' );

				//進料開始日期
				var feed_start_date = "";
				if (aData[5] != null && aData[5] != "" && aData[5] != "0000-00-00")
					feed_start_date = aData[5];

				$('td:eq(4)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+feed_start_date+'</div>' );

				//進料結束日期
				var feed_end_date = "";
				if (aData[6] != null && aData[6] != "" && aData[6] != "0000-00-00")
					feed_end_date = aData[6];

				$('td:eq(5)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+feed_end_date+'</div>' );

				//退料項目
				var return_item = "";
				if (aData[7] != null && aData[7] != "")
					return_item = aData[7];

				$('td:eq(6)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+return_item+'</div>' );

				//退料類別
				var return_type = "";
				if (aData[16] != null && aData[16] != "")
					return_type = aData[16];

				$('td:eq(7)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+return_type+'</div>' );

				//預估退料開始時間
				var esti_return_start_date = "";
				if (aData[15] != null && aData[15] != "" && aData[15] != "0000-00-00")
					esti_return_start_date = aData[15];

				$('td:eq(8)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+esti_return_start_date+'</div>' );

				//退料開始日期
				var return_start_date = "";
				if (aData[8] != null && aData[8] != "" && aData[8] != "0000-00-00")
					return_start_date = aData[8];

				$('td:eq(9)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+return_start_date+'</div>' );

				//退料結束日期
				var return_end_date = "";
				if (aData[9] != null && aData[9] != "" && aData[9] != "0000-00-00")
					return_end_date = aData[9];

				$('td:eq(10)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+return_end_date+'</div>' );


				//處理
				var url1 = "openfancybox_edit('/index.php?ch=overview_material_sub_modify&auto_seq="+aData[0]+"&fm=$fm',800,'96%','');";
				var mdel = "myDel(" + aData[0] + ", '$case_id', '$memberID');";

				var show_btn = '';

				show_btn = '<div class="btn-group text-nowrap">'
					+'<button type="button" class="btn btn-light" onclick="'+url1+'" title="修改"><i class="bi bi-pencil-square"></i></button>'
					+'<button type="button" class="btn btn-light" onclick="'+mdel+'" title="刪除"><i class="bi bi-trash"></i></button>'
					+'</div>';

				$('td:eq(11)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+show_btn+'</div>' );


				return nRow;
			
			}
			
		});
	
		/* Init the table */
		oTable = $('#overview_material_sub_table').dataTable();
		
	} );

var myDel = function(auto_seq,case_id,memberID) {

	Swal.fire({
	title: "您確定要刪除此筆資料嗎?",
	text: "此項作業會刪除所有與此筆案件記錄有關的資料",
	icon: "question",
	showCancelButton: true,
	confirmButtonColor: "#3085d6",
	cancelButtonColor: "#d33",
	cancelButtonText: "取消",
	confirmButtonText: "刪除"
	}).then((result) => {
		if (result.isConfirmed) {
			xajax_DeleteRow(auto_seq, case_id, memberID);
		}
	});

};

var overview_material_sub_Draw = function(){
	var oTable;
	oTable = $('#overview_material_sub_table').dataTable();
	oTable.fnDraw(false);
}

	
</script>

EOT;

?>