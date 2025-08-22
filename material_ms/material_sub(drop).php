<?php


//error_reporting(E_ALL); 
//ini_set('display_errors', '1');


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if( $detect->isMobile() && !$detect->isTablet() ){
	$isMobile = 1;
} else {
	$isMobile = 0;
}


$fm = $_GET['fm'];

$sure_to_delete = getlang("您確定要刪除此筆資料嗎?");

$dataTable_de = getDataTable_de();
$Prompt = getlang("提示訊息");
$Confirm = getlang("確認");
$Cancel = getlang("取消");


$list_view=<<<EOT
<div class="w-100 px-3 py-2">
	<table class="table table-bordered border-dark w-100" id="material_sub_table" style="min-width:1720px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:12%;">工程概況</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:12%;">工程說明</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:8%;">預訂完工日</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:10%;">代工單位</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:50%;">進退料狀況</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:8%;">物資時程</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="6" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
	</table>
</div>
EOT;



$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}


$show_material_sub=<<<EOT
<style>
#material_sub_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}
</style>

$list_view

<script>
	var oTable;
	$(document).ready(function() {
		$('#material_sub_table').dataTable( {
			"processing": false,
			"serverSide": true,
			"responsive":  {
				details: true
			},//RWD響應式
			"scrollX": '$scroll',
			"paging": false,
			"searching": false,  //禁用原生搜索
			"ordering": false,
			"ajaxSource": "/smarty/templates/$site_db/$templates/sub_modal/project/func06/material_ms/server_material_sub.php?site_db=$site_db&case_id=$case_id",
			"info": false,
			"language": {
						"sUrl": "$dataTable_de"
					},
			"fixedHeader": true,
			"fixedColumns": {
        		left: 1,
    		},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) { 

				//工程概況
				var engineering_overview = "";
				if (aData[2] != null && aData[2] != "")
					engineering_overview = aData[2];

				$('td:eq(0)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+engineering_overview+'</div>' );

				//工程概況
				var eng_description = "";
				if (aData[3] != null && aData[3] != "")
					eng_description = aData[3];

				$('td:eq(1)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+eng_description+'</div>' );

				//代工單位
				var builder_id = '<div id="builder_id'+aData[0]+'"></div>';
				//進退料狀況
				var return_list = '<div id="return_list'+aData[0]+'"></div>';

				xajax_returnValue(aData[0],aData[13],aData[1]);


				//預訂完工日
				var scheduled_completion_date = "";
				if (aData[6] != null && aData[6] != "" && aData[6] != "0000-00-00")
					scheduled_completion_date = aData[6];

				$('td:eq(2)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+scheduled_completion_date+'</div>' );

				//代工單位
				$('td:eq(3)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+builder_id+'</div>' );

				//進退料狀況
				$('td:eq(4)', nRow).html( '<div style="height:auto;min-height:32px;">'+return_list+'</div>' );


				//物資時程
				var url2 = "openfancybox_edit('/index.php?ch=overview_material_sub&seq="+aData[0]+"&case_id="+aData[1]+"&fm=$fm',1200,'96%','');";

				var show_btn2 = '';
					show_btn2 = '<div class="btn-group text-nowrap">'
						+'<button type="button" class="btn btn-light" onclick="'+url2+'" title="物資時程"><i class="bi bi-person-arms-up"></i>&nbsp;物資時程</button>'
						+'</div>';

				$('td:eq(5)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;">'+show_btn2+'</div>' );


				return nRow;
			}
			
		});
	
		/* Init the table */
		oTable = $('#material_sub_table').dataTable();
		
	} );
	

var material_sub_myDel = function(auto_seq) {

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
			xajax_DeleteRow(auto_seq);
		}
	});

};


var material_sub_myDraw = function(){
	var oTable;
	oTable = $('#material_sub_table').dataTable();
	oTable.fnDraw(false);
}

</script>

EOT;

?>