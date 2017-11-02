<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>添加标记</title>
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=4L2BZ-LQDKF-GXUJY-NYQ5O-CIPQT-KEBSD"></script>
<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/jquery-2.js"></script>
<style type="text/css">
*{
    margin:0px;
    padding:0px;
}
body, button, input, select, textarea {
    font: 12px/16px Verdana, Helvetica, Arial, sans-serif;
}
#info{
    width:603px;
    padding-top:3px;
    overflow:hidden;
}
.btn{
    width:112px;
}
#container{
	min-width:600px;
	min-height:767px;
}
</style>
</head>
<body>
<div style="display:none" id="raw_positions">
<?php
	include 'adminLogicController.php';
	$raw_positions = get_admin_positions();
	echo json_encode($raw_positions);
?>
</div>
<div>
	<label>起始时间</label>
	<input id="start_time" type="text" value="2017-09-01 19:00">
	<label>终止时间</label>
	<input id="end_time" type="text" value="2017-09-27 20:00">
	<input type="button" value="筛选" onclick="init()">
</div>
<div id="container"></div>
<script>

var markersArray = [];
var center = new qq.maps.LatLng(23.1025,113.319);
var map = new qq.maps.Map(document.getElementById('container'),{
	center: center,
	zoom: 13
});

init();

function init(){
	
	//删除覆盖物
	if (markersArray) {
		for (var i=0;i<markersArray.length;i++) {
			markersArray[i].setMap(null);
		}
		markersArray.length = 0;
	}
	
	var start_time = Date.parse(new Date($('#start_time').val().trim())) / 1000;
	var end_time = Date.parse(new Date($('#end_time').val().trim())) / 1000;
    var positions = JSON.parse($('#raw_positions').html().trim());
	var colorMap = new Array();
	var filter_positions = new Array();
	for(var i=0;i<positions.length;i++){
		if(positions[i]['timestamp'] >= start_time && positions[i]['timestamp'] <= end_time){
			if(!colorMap.hasOwnProperty(positions[i]['user_id'])){
				colorMap[positions[i]['user_id']] = getRandomColor();
			}
			filter_positions.push(positions[i]);
		}
	}
	
	for(var i=0;i<filter_positions.length;i++){
		var pos = new qq.maps.LatLng(parseFloat(filter_positions[i]['latitude']),parseFloat(filter_positions[i]['longitude']));
		/*var marker = new qq.maps.Marker({
			position: pos,
			map: map
		});*/

		var cirle = new qq.maps.Circle({
			center: pos, 
			radius: 200,
			map: map,
			fillColor: colorMap[filter_positions[i]['user_id']],
			strokeWeight:3
		});
		markersArray.push(cirle);
	}  
}

function getRandomColor(){
	var unitArray = new Array('0','1','2','3','4','5','6','7','8','9','10','A','B','C','D','E','F');
	var color = "#";
	for(var i = 0;i<3;i++){
		var idx = parseInt((Math.random()*16));
		color = color + unitArray[idx];
	}
	return color;
}

</script>
</body>
</html>