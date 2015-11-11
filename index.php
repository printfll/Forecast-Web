<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<?php 

	$location=array(
		'AL'=> 'Alabama',
		'AK'=> 'Alaska',
		'AZ'=> 'Arizona',
		'AR'=> 'Arkansas',
		'CA'=> 'California',
		'CO'=> 'Colorado',
		'CT'=> 'Connecticut',
		'DE'=> 'Delaware',
		'DC'=> 'District Of Columbia',
		'FL'=> 'Florida',
		'GA'=> 'Georgia',
		'HI'=> 'Hawaii',
		'ID'=> 'Idaho',
		'IL'=> 'Illinois',
		'IN'=> 'Indiana',
		'IA'=> 'Iowa',
		'KS'=> 'Kansas',
		'KY'=> 'Kentucky',
		'LA'=> 'Louisiana',
		'ME'=> 'Maine',
		'MD'=> 'Maryland',
		'MA'=> 'Massachusetts',
		'MI'=> 'Michigan',
		'MN'=> 'Minnesota',
		'MS'=> 'Mississippi',
		'MO'=> 'Missouri',
		'MT'=> 'Montana',
		'NE'=> 'Nebraska',
		'NV'=> 'Nevada',
		'NH'=> 'New Hampshire',
		'NJ'=> 'New Jersey',
		'NM'=> 'New Mexico',
		'NY'=> 'New York',
		'NC'=> 'North Carolina',
		'ND'=> 'North Dakota',
		'OH'=> 'Ohio',
		'OK'=> 'Oklahoma',
		'OR'=> 'Oregon',
		'PA'=> 'Pennsylvania',
		'RI'=> 'Rhode Island',
		'SC'=> 'South Carolina',
		'SD'=> 'South Dakota',
		'TN'=> 'Tennessee',
		'TX'=> 'Texas',
		'UT'=> 'Utah',
		'VT'=> 'Vermont',
		'VA'=> 'Virginia',
		'WA'=> 'Washington',
		'WV'=> 'West Virginia',
		'WI'=> 'Wisconsin',
		'WY'=> 'Wyoming'
	);
?>

<html>
	<head>
		<META http-equiv="content-type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
		<script type="text/javascript" src="http://jqueryvalidation.org/files/lib/jquery-1.11.1.js"></script>
		<script src="http://openlayers.org/api/OpenLayers.js"></script>
		<script src="http://openweathermap.org/js/OWM.OpenLayers.1.3.4.js" ></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="http://cs-server.usc.edu:42766/hw8/hw8_css.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>	
		<script type="text/javascript" src="http://cs-server.usc.edu:42766/hw8/hw8_js.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:url"           content="localhost/hw8.php" />
	    <meta property="og:type"          content="website" />
	    <meta property="og:title"         content="my title" />
	    <meta property="og:description"   content="Your description" />
	    <meta property="og:image"         content="localhost/clear.png" />
		<title>Forecast Search</title>
	

	</head>

	<body >
		<script>
		  window.fbAsyncInit = function() {
		    FB.init({
		      appId      : '1663189363949005',
		      xfbml      : true,
		      cookie	 :true,
		      status     :true,
		      version    : 'v2.5'
		    });
		  };

		  (function(d, s, id){
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "//connect.facebook.net/en_US/sdk.js";
		     fjs.parentNode.insertBefore(js, fjs);
		   }(document, 'script', 'facebook-jssdk'));

		</script>



		<h2 >Forecast Search</h2>
		<div class="container" style="padding:0px;" id="search">
			<form id="searchForm" class="row" style="background-color:rgba(150,150,150,0.8);">
				<div class="form-group col-lg-3 col-md-6 col-sm-12 col-xs-12" >
					<label for="street">Street Address:&nbsp;<span>*</span></label>
					</br>
					<input id="street" class="form-control" name="street" type="text"  placeholder="Enter the Street Address" >
					
					<div id="warnStreet" class="warnLabel"></div>
				</div>
				
				<div class="form-group col-lg-2 col-md-6 col-sm-12 col-xs-12">
					<label for="city">City:&nbsp;<span >*</span></label>
					</br>
					<input id="city" name="city" class="form-control" type="text"  placeholder="Enter the City name">
					
					<div id="warnCity" class="warnLabel"></div>
				</div>
				
				<div class="form-group col-lg-2 col-md-6 col-sm-12 col-xs-12">
					<label for="states">State:&nbsp;<span>*</span></label>
					</br>
					<select id="states" name="states" class="form-control" style="color:black;height:34px;">
								<option value="init" id="init" >Select your state...</option>
								<?php 
									foreach ($location as $key => $value) {?>
										<option value=<?php echo $key ?> ><?php echo $value?></option>
								<?php
									}
								?>
					</select>
					
					<div id="warnState" class="warnLabel"></div>
				</div>
				
				<div class="form-group col-lg-2 col-md-6 col-sm-12 col-xs-12">

					<label for="degree">Degree:&nbsp;<span>*</span></label>
					</br>
					<input type="radio" id="Fah" name="degree" value="Fahrenheit" checked="checked">Fahrenheit</input>
					<input type="radio" id="Cel" name="degree" value="Celsius">Celsius</input>
				</div>

				<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12" >
					<div style="float:right;">
						<button  id="submit" type="submit" name="submit" class="btn btn-primary"  >
							<span class="glyphicon glyphicon-search"></span>&nbsp;Search
						</button>
						<button  id="clear" type="button" name="clear" class="btn btn-default"  >
							<span class="glyphicon glyphicon-refresh"></span>
							&nbsp;Clear
						</button>
					</div>
					<br>
					<div id="powerBy" >
					<h4>Powered by<a href="http://forecast.io/" style="margin: 0 auto;"><img src="http://cs-server.usc.edu:42766/hw8/forecast_logo.png" style="width:80px;"></a></h4>

					</div>
				</div>

				
			</form>
			
			<hr style="width:100%;color:white;">
			
		</div>
		

		<div id="result" class="container" style="display:none;">
			
				 <ul class="nav nav-pills " role="tablist" id="tabs">
				    <li role="presentation" class="active"><a href="#now" id="tab-1" aria-controls="now" role="tab" data-toggle="tab">Right Now</a></li>
				    <li role="presentation"><a href="#hour" aria-controls="hour" role="tab" data-toggle="tab">Next 24 Hours</a></li>
				    <li role="presentation"><a href="#day" aria-controls="day" role="tab" data-toggle="tab">Next 7 Days</a></li>
				    
				</ul>

				  <!-- Tab panes -->
				  <div class="tab-content">
				  	 <div role="tabpanel" class="tab-pane active" id="now">
					  	<div class="row" style="margin:0px;">
					  		<div class="col-xs-12 col-md-6 col-sm-12 col-lg-6" style="padding:0px;">
					  			<table class="table table-striped" id="nowTable">

					    		</table>
					  		</div>
					  		<div class="col-xs-12 col-md-6 col-sm-12 col-lg-6" id="basicMap">


					  		</div>
					  	</div>
				  	</div>
				 
				    
				    <div role="tabpanel" class="tab-pane table-responsive" id="hour">
				    	
				    		<table class="table container" id="hourTable">

				    		</table>
				    </div>
				    
				    <div role="tabpanel" class="tab-pane" id="day" >
					    <div class="row container" id="dayTable">
						</div>
				    	<!-- modal begin-->
				    	<div class="modal fade" id="dayDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						  	<div class="modal-dialog" role="document">
						    	<div class="modal-content">
						      		<div class="modal-header">
						        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel"></h4>
						     		</div>
						      	<div class="modal-body container"  style="text-align:center;"> 
						        	<div>
						        		<img src="" id="modal-img" title="" alt=""/>
						        	</div>
						        	<div style="color:black;">
						        		<div class="row">
							        		<h3 id="modal-title"><span></span><span style="color:rgb(255,165,0);"></span></h3>
							        		<div class="modal-tag  col-lg-4 col-xs-12">
								        		<label class="tag-title">Sunrise Time</label>
								        		<br>
								        		<label class="tag-val" id="modal-sunrise"></label>
							        		</div>

							        		<div class="modal-tag col-lg-4 col-xs-12">
								        		<label class="tag-title">Sunset Time</label>
								        		<br>
								        		<label class="tag-val" id="modal-sunset"></label>
							        		</div>

							        		<div class="modal-tag col-lg-4 col-xs-12">
								        		<label class="tag-title">Humidity</label>
								        		<br>
								        		<label class="tag-val" id="modal-humidity"></label>
							        		</div>
						        		</div>

						        		<div class="row">
							        		<div class="modal-tag col-lg-4 col-xs-12">
								        		<label class="tag-title">Wind Speed</label>
								        		<br>
								        		<label class="tag-val" id="modal-windspeed"></label>
							        		</div>

							        		<div class="modal-tag col-lg-4 col-xs-12">
								        		<label class="tag-title">Visibility</label>
								        		<br>
								        		<label class="tag-val" id="modal-visibility"></label>
							        		</div>

							        		<div class="modal-tag col-lg-4 col-xs-12">
								        		<label class="tag-title">Pressure</label>
								        		<br>
								        		<label class="tag-val" id="modal-pressure"></label>
							        		</div>
						        		</div>
						        	</div>
						      	</div>
							    
							    <div class="modal-footer">

							        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							    </div>
						    </div>
						  </div>
						</div>
				    	<!-- modal end-->
				    </div>
				    
				  </div>
			
		</div>
		

		
		
		<noscript>
		</body>
</html>