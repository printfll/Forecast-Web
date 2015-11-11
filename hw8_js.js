
$(document).ready(function(){
	$("#submit").click(function(event){
		
		event.preventDefault();
		var tag=true;
		if ($("#street").val().trim()=="") {
			$("#warnStreet").text("Please enter the street address");
			tag=false;
		}else{
			$("#warnStreet").text("");
		}

		if ($("#city").val().trim()=="") {
			$("#warnCity").text("Please enter the city");
			tag=false;
		}else{
			$("#warnCity").text("");
		}

		if ($("#states").get(0).selectedIndex==0) {
			$("#warnState").text("Please select a state");
			tag=false;
		}else{
			$("#warnState").text("");
		}

		if (tag==true) {
			getContent();
		};
	});

	$("#clear").click(function(){
		$("#street").val("");
		$("#city").val("");
		$("#states").get(0).selectedIndex=0;
		$("input:radio[name='degree']")[0].checked = true;
		$("#warnStreet").text("");
		$("#warnCity").text("");
		$("#warnState").text("");
		$("#nowTable").empty();
		$("#hourTable").empty();
		$("#dayTable").empty();
		$("#basicMap").empty();
		$("#result").css("visibility","hidden");

	});	

	$("#street").change(function(){
		if ($("#street").val().trim().length>0) {
			$("#warnStreet").text("");
		}else{
			$("#warnStreet").text("Please enter the street address");
		}
		
	});

	$("#city").change(function(){
		if ($("#city").val().trim().length>0) {
			$("#warnCity").text("");
		}else{
			$("#warnCity").text("Please enter the city");
		}
		
	});

	$("#states").change(function(){
		if ($("#states").get(0).selectedIndex!=0) {
			$("#warnState").text("");
		}else{
			$("#warnState").text("Please select a state");
		}
	});

	
});


function getContent(){
	
	var add=$("#street").val()+","+$("#city").val()+","+$("#states").val();
	var request=$.ajax({
		url: "http://cs-server.usc.edu:42766/hw8/hw8_extract.php",
		type: "get",

		data:{
			req:"google",
			address:$("#street").val()+","+$("#city").val()+","+$("#states").val(),
			degree:$("input[name='degree']:checked").val(),

			key:"AIzaSyDQueJ2wi4jkvVpg0DoKyNh6SweoOlbeo0"
		},
		success: function(data){

			var obj = JSON.parse(data) ;
			if (obj.status!="OK") {
				$("#search").append("the address is not valid, please try other.");
			}else{
				lat=obj.lat;
				lng=obj.lng;
				linkVal=obj.link;
				current=obj.current_result;
				hour=obj.hour_result;
				day=obj.day_result;
				addTab();	
				addNowTable(current);
				addHourTable(hour);
				addDayTable(day);
				createMap(lat,lng);
			}
			
		},
		error: function(jqXHR, error, errorThrown) {
            if (jqXHR.status === 0) { 
             	msg = 'Network Problem'; 
            }else if (jqXHR.status == 404) { 
             	msg = 'Requested page not found. [404]'; 
            }else if (jqXHR.status == 500) {
            	msg = 'Internal Server Error [500].'; 
            }    
            alert(msg);
        },
	});
}

function addTab(){
	$("#result").css("display","block");
}

function addNowTable(current){
	
	var intro=current.summary+"&nbsp;in&nbsp;"+$("#city").val()+",&nbsp;"+$("#states").val();
	var str="<tr style='background-color:rgb(242,126,127)'><td colspan='2'><div class='col-xs-12 col-md-6 col-lg-6 lay' ><img id='nowIcon' src='"+current.icon_img+"'alt='"+current.icon+"'title='"+current.icon+"'</></div>";
	str=str+"<div class='col-xs-12 col-md-6 col-lg-6' id='nowInstr'><div style='margin-top:0;'>"+intro+"</div><br><div style='font-size:6em;' id='temp'>"+parseInt(current.temperature)+"<span id='sup' >°"+current.temperature.split("°")[1]+"</span></div><br><span style='color:blue;'>L:"+parseInt(current.temperatureMin)+"°</span><span style='color:black;'>&nbsp;|&nbsp;<span style='color:green;'>H:"+parseInt(current.temperatureMax)+"°</span></span><button  id='fbshare' onclick='share()'><img src='http://cs-server.usc.edu:42766/hw8/fb_icon.png' style='width:25px;'/></button></div></td></tr>";
	
	str+="<tr><td>Precipitation</td><td>"+current.precipIntensity+"</td></tr>";
	str+="<tr><td>Chance of Rain</td><td>"+current.precipProbability+"</td></tr>";
	str+="<tr><td>Wind Speed</td><td>"+current.windSpeed+"</td></tr>";
	str+="<tr><td>Dew Point</td><td>"+current.dewPoint+"</td></tr>";
	str+="<tr><td>Humidity</td><td>"+current.humidity+"</td></tr>";
	str+="<tr><td>Visibility</td><td>"+current.visibility+"</td></tr>";
	str+="<tr><td>Sunrise</td><td>"+current.risetime+"</td></tr>";
	str+="<tr><td>Sunset</td><td>"+current.settime+"</td></tr>";

	$("#nowTable").empty();
	$("#nowTable").append(str);
	

	//<div class="fb-share-button" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button_count"></div>
}

function addHourTable(hour){
	
	
	var str="<tr><th>Time</th><th>Summary</th><th>Cloud&nbsp;Cover</th><th>Temp(°"+hour[0].temperature.split("°")[1]+")</th><th>View Details</th></tr>";
		for (var i = 0;i<hour.length;i++) {
			var eachHour=hour[i];
			str=str+"<tr><td>"+eachHour.time+"</td><td><img style='width:80px;'alt='"+eachHour.icon+"'title='"+eachHour.icon+"'src='"+eachHour.summary+"'</></td><td>"+eachHour.cloudCover+"</td><td>"+eachHour.temperature.split("°")[0]+"</td><td><button style='border:0;background-color:white;color:rgb(51,122,183);' data-toggle='collapse' data-target='#hour"+i+"' aria-expanded='false' aria-controls='hour"+i+"' id='hourLink"+i+"'><span class='glyphicon glyphicon-plus'></span></button></td></tr>";
			str=str+"<tr><td colspan='5' class='hiddenTD ' ><div id='hour"+i+"' class='hiddenDiv collapse '><table class='hiddenTable'   ><tr ><th style='background-color: white;color: black;' class='hiddenHead"+i+"'>Wind</th><th style='background-color: white;color: black;' class='hiddenHead"+i+"'>Humidity</th><th style='background-color: white;color: black;' class='hiddenHead"+i+"'>Visibility</th><th style='background-color: white;color: black;' class='hiddenHead"+i+"'>Pressure</th></tr>";
			str=str+"<tr><td>"+eachHour.windspeed+"</td><td>"+eachHour.humidity+"</td><td>"+eachHour.visibility+"</td><td>"+eachHour.pressure+"</td></tr></table></div></td></tr>"	
		};
	$("#hourTable").empty();
	$("#hourTable").append(str);

}

function addDayTable(day){
	
	var str="<div class='row'>";
	for(var i=0;i<day.length;i++){
		var eachDay=day[i];
		str=str+"<button class='dayCol container col-md-1 col-lg-1 col-xs-12' data-toggle='modal' data-target='#dayDetail' id='dayCol"+i+"' onclick='showDayDetail(this)'><strong>"+eachDay.day+"<br>"+eachDay.month+"</strong><br><br><img style='width:50px;'src='"+eachDay.icon_img+"' alt='"+eachDay.icon+"' title='"+eachDay.icon+"'/><br>Min<br>Temp<br><br><h2>"+eachDay.minTemp.split("°")[0]+"°</h2><br>Max<br>Temp<br><br><h2>"+eachDay.maxTemp.split("°")[0]+"°</h2><br></button>";


	}
	str+="</div>"
	$("#dayTable").empty();
	
	$("#dayTable").append(str);
	$("#dayCol0").addClass("col-lg-offset-2 col-md-offset-2");

	
}

var map;
function createMap(lat, lng){
		    //Center of map
	 $("#basicMap").empty();
	 $("#basicMap").height($("#nowTable").height());
    
	 var lonlat = new OpenLayers.LonLat(lng, lat);
	 lonlat.transform(
		new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
		new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
	);


    var map = new OpenLayers.Map("basicMap");
    // Create OSM overlays
    var mapnik = new OpenLayers.Layer.OSM();
      var layer_cloud = new OpenLayers.Layer.XYZ(
        "clouds",
        "http://${s}.tile.openweathermap.org/map/clouds/${z}/${x}/${y}.png",
        {
            isBaseLayer: false,
            opacity: 0.7,
            sphericalMercator: true
        }
    );

    var layer_precipitation = new OpenLayers.Layer.XYZ(
        "precipitation",
        "http://${s}.tile.openweathermap.org/map/precipitation/${z}/${x}/${y}.png",
        {
            isBaseLayer: false,
            opacity: 0.7,
            sphericalMercator: true
        }
    );


	
	console.log("result width:"+$("#result").width()+",basicMap width:"+$("#basicMap").width()+",window width:"+$(window).width());
	
    map.addLayers([mapnik, layer_precipitation, layer_cloud]);
    map.addControl(new OpenLayers.Control.LayerSwitcher());
    
    map.setCenter(lonlat,7);

}

function displayDetail(obj){
	var linkId=$(obj).attr("id");
	var id=linkId.substring(8,linkId.length);//hourLink0,hourLink1,...
	var tableID="#hour"+id;
	$(tableID).toggle();
	var headID=".hiddenHead"+id;
	$(headID).css({"background-color":"white","color":"black"});
	
}

function showDayDetail(obj){
	var linkId=$(obj).attr("id");
	var id=linkId.substring(6,linkId.length);//dayCol0, dayCol1,...
	var dayItem=day[parseInt(id)];
	var title="Weather in "+$("#city").val()+" on "+dayItem.month;
	$("#myModalLabel").text(title);
	$("#modal-title span:first").text(dayItem.day+": ");
	$("#modal-title span:last").text(dayItem.summary);
	$("#modal-img").attr({"src":dayItem.icon_img,"title":dayItem.icon,"alt":dayItem.icon});
	$("#modal-sunrise").text(dayItem.sunrise);
	$("#modal-sunset").text(dayItem.sunset);
	$("#modal-humidity").text(dayItem.humidity);
	$("#modal-windspeed").text(dayItem.windspeed);
	$("#modal-visibility").text(dayItem.visibility);
	$("#modal-pressure").text(dayItem.pressure);
}

function share(){
	var nameInstr="Current Weather in "+$("#city").val()+", "+$("#states").val();
	
	
	FB.ui({
		method: 'feed',
		name: nameInstr,
		link: "http://forecast.io",
		picture: current.icon_img,
		caption: 'WEATHER INFORMATION FROM FORECAST.IO',
		description:current.summary+","+current.temperature,
		message: ''
	},function(response){
		if(response==null){
			alert("Not Posted");
		}else{
			alert("Posted Successfully");
		}
	});
}
