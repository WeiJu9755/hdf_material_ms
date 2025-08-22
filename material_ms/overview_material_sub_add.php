<?php

session_start();
$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


function isValidDate($date) {
    if (empty($date) || $date === null) {
        return false; // 空的或 null
    }
    
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    return $dateObj && $dateObj->format('Y-m-d') === $date;
}

require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");

function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	$bError = false;

	$floor_list	= trim($aFormValues['floor_list']);
	
	if (trim($aFormValues['floor_list']) == "")	{
		$objResponse->script("jAlert('警示', '請選擇樓層進料', 'red', '', 2000);");
		return $objResponse;
		exit;
	}

	if (!$bError) {
		$fm					= trim($aFormValues['fm']);
		$site_db			= trim($aFormValues['site_db']);
		$memberID			= trim($aFormValues['memberID']);
		$case_id			= trim($aFormValues['case_id']);
		$seq				= trim($aFormValues['seq']);
		$seq2				= trim($aFormValues['seq2']);
		
		//存入實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();
	  
		$Qry="insert into overview_material_sub (case_id,seq,seq2,floor,makeby,last_modify) values ('$case_id','$seq','$seq2','$floor_list','$memberID',now())";
		$mDB->query($Qry);
		//再取出auto_seq
		$Qry="select auto_seq from overview_material_sub where case_id = '$case_id' and seq = '$seq' and seq2 = '$seq2' order by auto_seq desc limit 0,1";
		$mDB->query($Qry);
		if ($mDB->rowCount() > 0) {
			//已找到符合資料
			$row=$mDB->fetchRow(2);
			$auto_seq = $row['auto_seq'];
		}

        $mDB->remove();

		if (!empty($auto_seq)) {
			$objResponse->script("myDraw();");
			//$objResponse->script("art.dialog.tips('已新增，請繼續輸入其他資料...',2);");
			$objResponse->script("window.location='/?ch=overview_material_sub_modify&auto_seq=$auto_seq&fm=$fm';");
			//$objResponse->script("parent.$.fancybox.close();");
		} else {
			//$objResponse->script("art.dialog.alert('發生不明原因的錯誤，資料未新增，請再試一次!');");
			$objResponse->script("parent.$.fancybox.close();");
		}
	};
	
	return $objResponse;	
}

$xajax->processRequest();


$fm = $_GET['fm'];
$case_id = $_GET['case_id'];
$seq = $_GET['seq'];
$seq2 = $_GET['seq2'];


//先判斷是否已有資料，沒有資料則直接帶入實際進場日，有資料則取得上一筆的日期
$mDB = "";
$mDB = new MywebDB();

$select_list = array();

$Qry="select * from items where pro_id = 'floor' order by orderby";
$mDB->query($Qry);
if ($mDB->rowCount() > 0) {
    //已找到符合資料
	while ($row=$mDB->fetchRow(2)) {
		$caption = $row['caption'];
		$orderby = $row['orderby'];

		$select_list[] = $caption;
	}

}

$mDB->remove();

$series_select_list = json_encode($select_list);


$mess_title = $title;



if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;

$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 800px !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:200px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:450px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.maxwidth {
    width: 100%;
    max-width: 250px;
}
</style>
EOT;

} else {
	$isMobile = 1;
$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.maxwidth {
    width: 100%;
}
</style>
EOT;

}


$show_center=<<<EOT
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>

$style_css
<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start">
			$mess_title
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="addForm" name="addForm" enctype="multipart/form-data" action="javascript:void(null);">
				<div class="field_container3">
					<div>
						<div class="field_div1">樓層進料:</div> 
						<div class="field_div2">
							<select class="form-control select2" multiple="multiple" id="floor" name="floor" style="width: 100%;"></select>
						</div> 
					</div>
				</div>
				<div class="form_btn_div mt-5">
					<input type="hidden" name="fm" value="$fm" />
					<input type="hidden" name="site_db" value="$site_db" />
					<input type="hidden" name="memberID" value="$memberID" />
					<input type="hidden" name="case_id" value="$case_id" />
					<input type="hidden" name="seq" value="$seq" />
					<input type="hidden" name="seq2" value="$seq2" />
					<input type="hidden" id="floor_list" name="floor_list" value="$floor_list" />
					<button class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 10px;margin-right: 10px;"><i class="bi bi-check-lg green"></i>&nbsp;確定新增</button>
					<button class="btn btn-danger" type="button" onclick="parent.$.fancybox.close();" style="padding: 10px;"><i class="bi bi-power"></i>&nbsp關閉</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {

	// 獲取 select 元素
	var selectedValue = $('#floor').val();

	$('#floor_list').val(selectedValue);

	xajax_processform(xajax.getFormValues('addForm'));
	thisform.submit();
}

var myDraw = function(){
	var oTable;
	oTable = parent.$('#overview_material_sub_table').dataTable();
	oTable.fnDraw(false);
}
	
</script>
<script>

	var series_select_list = JSON.parse('$series_select_list');

	$('.select2').select2({
		data: series_select_list,
		tags: true,
		maximumSelectionLength: 100,
		tokenSeparators: [',', ' '],
		placeholder: "請選擇樓別",
		//minimumInputLength: 1,
		//ajax: {
		//   url: "you url to data",
		//   dataType: 'json',
		//  quietMillis: 250,
		//  data: function (term, page) {
		//     return {
		//         q: term, // search term
		//    };
		//  },
		//  results: function (data, page) { 
		//  return { results: data.items };
		//   },
		//   cache: true
		// }
	});

//	var series_floor_list = JSON.parse('$series_floor_list');
//	$("#floor").val(series_floor_list).select2();


</script>

EOT;

//}

?>