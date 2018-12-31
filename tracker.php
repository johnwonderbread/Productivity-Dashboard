<?php
$page = $_SERVER['PHP_SELF'];

#Seconds to refresh the webpage
$sec = "300";

#require_once("twitteroauth/twitteroauth/twitteroauth.php"); // path to Twitter oauth library
#$consumerkey = "TfpS7e2C58gTrOyfBJiTEdqoN";
#$consumersecret = "0P50iRU7g8YnQnjksVhcG44UI7VUYcimwAi5rSN12HPLyd7DjW";
#$accesstoken = "389354485-iNYvlYonPKLF9Q27dx9l18ToCHmcjH2KsBs091pG";
#$accesstokensecret = "pTAoscNcEAMl8yPP1GLcauTDUlV7nvlDoC8WqZT6uYCJP";
#$woeid = 2459115;
#$url= 'https://api.twitter.com/1.1/trends/place.json?id='.$woeid;

#function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
#    $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
#    return $connection;
#  }
   
#$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
#$statues = $connection->get($url);

$ConsumerKeyAPIKey="TfpS7e2C58gTrOyfBJiTEdqoN";
$ConsumerSecretAPISecret="0P50iRU7g8YnQnjksVhcG44UI7VUYcimwAi5rSN12HPLyd7DjW";
$access_token="389354485-iNYvlYonPKLF9Q27dx9l18ToCHmcjH2KsBs091pG";
$access_token_secret="pTAoscNcEAMl8yPP1GLcauTDUlV7nvlDoC8WqZT6uYCJP";
$NumberOfTags=15; //keep as minimum as possible
$GeoLocleID="2459115";
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$connection = new TwitterOAuth($ConsumerKeyAPIKey, $ConsumerSecretAPISecret, $access_token, $access_token_secret);
$statues = $connection->get("trends/place", ["id" => $GeoLocleID]);
$stringgerr="";
$i=1;
foreach($statues as $hash)
{    $inner=$hash->trends;
    foreach($inner as $in)
    {
        
        $value=$in->name;
        
        if (strpos($value, '#') !== false) {
        $value=$value;
        } else {
        $value="#".$value;
        }
        $stringgerr.=str_replace(" ","_",$value)." ";
        
        if($i==$NumberOfTags)
        break;
        $i++;
    }
    if($i==$NumberOfTags)
        break;
};

$testArray=
      $trelloUrl=file_get_contents("https://api.trello.com/1/lists/5c279a32c559d774922e4c96/cards?fields=name&key=3eb41f0d472d8ff304bd8c990f0024b6&token=06f3574635413aafdc7ce89989da1494e3ee88b6f32d7165f2ad2043f8534ede");
      $urlContents = file_get_contents("https://www.rescuetime.com/anapi/data?key=B63txiFZVjFySGvj_uZlaxV__NaKEDnwytERDZHm&perspective=rank&interval=hour&restrict_begin=".date('Y-m-d')."&restrict_end=".date('Y-m-d')."&format=json");
      $urlContentsY = file_get_contents("https://www.rescuetime.com/anapi/data?key=B63txiFZVjFySGvj_uZlaxV__NaKEDnwytERDZHm&perspective=rank&interval=hour&restrict_begin=".date('d.m.Y',strtotime("-1 days"))."&restrict_end=".date('d.m.Y',strtotime("-1 days"))."&format=json");
      $urlContents2 = file_get_contents("https://www.rescuetime.com/anapi/daily_summary_feed?key=B63txiFZVjFySGvj_uZlaxV__NaKEDnwytERDZHm");
      $data = json_decode($urlContents,true);
      $dataY = json_decode($urlContentsY,true); #rescuetime yesterday's data
      $data2 = json_decode($urlContents2,true); #recuetime daily summary
      $trelloData = json_decode($trelloUrl,true); #trello cards

#---------------------Test Commands-------------------
#echo "Date= ".date('Y-m-d');
#echo $data['rows'][0][3];
#print_r($data);
#print_r($data2);
#print_r($dataY);
#print_r($trelloData);
#print_r($trends);
#-----------------------------------------------------

$posTotal = 0; #Positive seconds (seconds spent on a program times the productivity value)
$negTotal = 0; #Negative seconds (seconds spent on a program times the productivity value)
$absTotal = 1;

#this for loop gets seconds spent from the json file
foreach ($data['rows'] as $key => $value) {
  $productivity = $value[1] * $value[5];
  #print_r($productivity);

  if ($productivity < 0)
{
   $negTotal = $negTotal + $productivity;
}
   $absTotal = $absTotal + abs($productivity);
}

#gets the categaories for each value
$categoriesArray = array();
$seconds = array();
$totalSeconds = 0;
foreach ($data['rows'] as $key => $value) {
  $categoriesArray[] = $value[4];
  $seconds[] = floor($value[1]/60);
  $totalSeconds = $totalSeconds + $value[1];
}

#radar graph data
$js_array = json_encode($categoriesArray);
$js_array2 = json_encode($seconds);

#-------------Unneeded Conversion-------------
#gets trending tweets
    #$categoriesArray2 = array();
    #$volume = array(); 
    #if(is_array($trends)) {
    #    foreach ($trends as $trend => $value) {
    #        $categoriesArray2[] = $value[1];
    #        $volume[] = $value[5];
    #    } 
    #};

#twitter data 
    #$js_array3 = json_encode($categoriesArray2);
    #$js_array4 = json_encode($volume); 
#---------------------------------------


#-------------Test Commands-------------
#echo "var javascript_array = ". $js_array . ";\n";
#echo "var javascript_array = ". $js_array2 . ";\n";
#echo "Negative Total= ".$negTotal;
#echo "<br>";
#echo "Absolute Total= ".$absTotal;
#---------------------------------------


#gets the categories for each value for yesterday
$categoriesArrayY = array();
$secondsY = array();
$totalSecondsY = 0;
foreach ($dataY['rows'] as $key => $value) {
  $categoriesArrayY[] = $value[4];
  $secondsY[] = floor($value[1]/60);
  $totalSecondsY = $totalSecondsY + $value[1];
   #echo "it is: ".$categoriesArrayY;
 #echo "<br>";
}

#radar graph data for Yesterday
$js_arrayY = json_encode($categoriesArrayY);
#echo "var javascript_array = ". $js_array . ";\n";
$js_array2Y = json_encode($secondsY);


#calculate productive hours
$productiveHours=array();
$distractiveHours=array();
$i=0;
foreach ($data2 as $value) {

  if ($i<7) {

    $productiveHours[]=$value['all_productive_hours'];
    $distractiveHours[]=$value['all_distracting_hours'];
  }

  $i=$i+1;
}
#print_r($productiveHours);
#print_r($distractiveHours);
$js_productiveHours = json_encode($productiveHours);
$js_distractiveHours = json_encode($distractiveHours);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
     <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

      <title>Productivity</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>

  <style type="text/css">
  /* You can edit these parts to change the dimension, width,heigh of the graphs */
  #percent {
    position: relative;
  }
  #percent #myDoughnutChart {
    position: absolute;
  }

  #overlay{
align-items: center;
    color: white;
    position: relative;
    top: 100px;
    left: 80px;
    font-size: 30px;
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
  }
  #doughnut{
    position: relative;
  }
  #textContainer{
    color: white;
    position: fixed;
bottom: 0;
right: 2%;
  }

  #todo{
    color: white;
    margin-left: 1%;
    width: 380px;
    margin-top: 0px;
  }
  #todo2{
    color: white;
    margin-left: 50%;
    width: 380px;
    margin-top: -30px;
  }
  #container{
background-color: black;
  }
canvas{

}

html{
font-size: 12px;
background-color: black;
}

.container{

font-size: 25px;

}
#doughnut{
  margin-left: 20px;
}
#LineChartContainer{
  width: 40%;
  position: absolute;
  left: 35px;
  bottom:125px;
}
  </style>


  </head>
  <body>

<div id="container" style="width:100%">
<div id="doughnut">
<div style="margin-left:10%;width:25%; float:left;">
  <div id="percent">

<canvas id="myDoughnutChart" ></canvas>
<div id="overlay" onClick="window.location.reload()"><?php echo floor(100-(abs($negTotal)*100)/$absTotal); ?></div>
</div>
</div>
</div>
<div id="container2" style="margin-left:50%; width:45%;">
<canvas id="myRadarChart" style="position:relative;" ></canvas>

</div>
<div id="textContainer">
<?php echo "Total Time (MB Pro + PC + Pixel): ".floor($totalSeconds/60)." Minutes"; ?>
</div>
<div id="todo">

<?php
$i=0;
foreach ($trelloData as $value) {

if ($i<6) {
  echo "&#x25a2";
  echo " ".$value['name'];
  echo "<br>";
}

$i=$i+1;

}

 ?>

</div>

<div id="todo2">
    <b>NYC Trending on Twitter: </b>
 <?php
    echo $stringgerr;
 ?>
</div>
<div id="LineChartContainer">
  <canvas id="myLineChart"></canvas>
</div>

    <script>

    <?php echo "var productiveHours= ". $js_productiveHours. ";\n"; ?>
    <?php echo "var distractiveHours = ". $js_distractiveHours . ";\n";?>
productiveHours.reverse();
distractiveHours.reverse();
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var d = new Date();
    var n = d.getDay();

    for (i = 0; i <=(6- d.getDay()); i++) {
    days.splice(0,0,days[6]);
    days.splice(7,1);
}
console.log(days);

    var ctx = document.getElementById("myLineChart");
    var lineChart = new Chart(ctx, {
      type:'line',
      data: {
        labels:days,
        datasets:[
{
  borderColor:"#00ff00",
  pointColor:"#fff",
  label:"Productive",
  data:productiveHours
},
{
  borderColor:"#ff0000",
  label:"Distracted",
  data:distractiveHours
}
        ]
      },
      options:{
        scales: {
      xAxes: [{
        display: true,
        gridLines: {
          color: "#FFFFFF"
        },
        scaleLabel: {
          display: false,
          labelString: 'Days'
        }
      }],
      yAxes: [{
        display: true,
        gridLines: {
          color: "#FFFFFF"
        },
        scaleLabel: {
          display: true,
          labelString: 'Hours'
        }
      }]
    }
      }
    });

    <?php echo "var categories= ". $js_array . ";\n"; ?>
    <?php echo "var secondsArray = ". $js_array2 . ";\n";?>
    var categoriesRadar = [];
    var secondsRadar = [0,0,0,0,0,0];

    for (var i=0; i<categories.length;i++){
      if (categories[i]=="Video"||categories[i]=="General Social Networking"||categories[i]=="General Entertainment"||categories[i]=="Games") {
        secondsRadar[0]=secondsRadar[0]+(secondsArray[i]);
      }
      if (categories[i]=="Editing & IDEs"||categories[i]=="General Software Development"||categories[i]=="Video Editing"||categories[i]=="Intelligence") {
        secondsRadar[1]=secondsRadar[1]+(secondsArray[i]);
      }
      if (categories[i]=="General News & Opinion") {
        secondsRadar[2]=secondsRadar[2]+(secondsArray[i]);
      }
      if (categories[i]=="General Reference & Learning"||categories[i]=="General Business"||categories[i]=="Presentation"||categories[i]=="Project Management"||categories[i]=="Design & Planning"||categories[i]=="Writing"||categories[i]=="Engineering & Drafting"||categories[i]=="Search"||categories[i]=="Engineering & Technology") {
        secondsRadar[3]=secondsRadar[3]+(secondsArray[i]);
      }
      if (categories[i]=="Email"||categories[i]=="Instant Message") {
        secondsRadar[4]=secondsRadar[4]+(secondsArray[i]);
      }
      if (categories[i]=="Business") {
        secondsRadar[5]=secondsRadar[5]+(secondsArray[i]);
      }
    }
    //console.log(secondsRadar);


//YESTERdays data

    <?php echo "var categoriesY= ". $js_arrayY . ";\n"; ?>
    <?php echo "var secondsArrayY = ". $js_array2Y . ";\n";?>
    var categoriesRadarY = [];
    var secondsRadarY = [0,0,0,0,0,0];

    for (var i=0; i<categoriesY.length;i++){
      if (categoriesY[i]=="Video"||categoriesY[i]=="General Social Networking"||categoriesY[i]=="General Entertainment"||categoriesY[i]=="Games") {
        secondsRadarY[0]=secondsRadarY[0]+(secondsArrayY[i]);
      }
      if (categoriesY[i]=="Editing & IDEs"||categoriesY[i]=="General Software Development"||categoriesY[i]=="Video Editing"||categoriesY[i]=="Intelligence") {
        secondsRadarY[1]=secondsRadarY[1]+(secondsArrayY[i]);
      }
      if (categoriesY[i]=="General News & Opinion") {
        secondsRadarY[2]=secondsRadarY[2]+(secondsArrayY[i]);
      }
      if (categoriesY[i]=="General Reference & Learning"||categoriesY[i]=="General Business"||categoriesY[i]=="Design & Planning"||categoriesY[i]=="Writing"||categoriesY[i]=="Engineering & Drafting"||categoriesY[i]=="Search") {
        secondsRadarY[3]=secondsRadarY[3]+(secondsArrayY[i]);
      }
      if (categoriesY[i]=="Email"||categoriesY[i]=="Instant Message") {
        secondsRadarY[4]=secondsRadarY[4]+(secondsArrayY[i]);
      }
      if (categories[i]=="Business") {
        secondsRadar[5]=secondsRadar[5]+(secondsArray[i]);
      }
    }
    //console.log(secondsRadar);

Chart.defaults.global.defaultFontColor = '#fff';
    var ctx = document.getElementById("myDoughnutChart");
    var negative = "<?php echo (abs($negTotal)*100)/$absTotal; ?>";
var myDoughnutChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
          labels: ['Distracted', 'Productive'],
          datasets: [{
            backgroundColor: [
        "#ff0000",
        "#00ff00"
      ],
              data: [negative,100-negative]
  }]
},
options:{
elements: { arc: { borderWidth: 0 } },
animation: {
        duration: 0
    }
  }
});
 Chart.defaults.global.defaultFontColor = '#fff';
 Chart.defaults.global.defaultBackgroundColor = '#fff';
 var chartColors = {
 	red: 'rgb(255, 99, 132)',
 	orange: 'rgb(255, 159, 64)',
 	yellow: 'rgb(255, 205, 86)',
 	green: 'rgb(75, 192, 192)',
 	blue: 'rgb(54, 162, 235)',
 	purple: 'rgb(153, 102, 255)',
 	grey: 'rgb(231,233,237)'
 };

var ctx = document.getElementById("myRadarChart");
var color = Chart.helpers.color;
var myRadarChart = new Chart(ctx, {
    type: 'polarArea',
    data:  {
    labels: ['Entertainment', 'Software Dev.', 'News', 'Learning', 'Email & Chat', 'Business'],
    datasets: [{
        label: "Minutes Spent Today",
        backgroundColor: color(chartColors.yellow).alpha(0.5).rgbString(),
        pointColor: "rgb(255,255,255)",
        borderColor:"yellow",
        data: secondsRadar
    },
    {
      label: "Minutes Spent Yesterday",
      backgroundColor: color(chartColors.grey).alpha(0.5).rgbString(),
      pointColor: "rgb(255,255,255)",
      borderColor:"grey",
      data: secondsRadarY
    }
  ]
},
options:{
  scale:{
    pointLabels:{
      fontSize:11
    },
    lineArc: true,
    position: "chartArea",

        angleLines: {
            display: false,
            color: "rgb(255,255,255)",
            lineWidth: 1
        },
        gridLines: {
          color: 'rgba(255, 255, 255, 0.4)',
          tickMarkLength: 15
        },

    // label settings
    ticks: {
        //Boolean - Show a backdrop to the scale label
        showLabelBackdrop: false,

        //String - The colour of the label backdrop
        backdropColor: "rgb(255,255,255)",

        //Number - The backdrop padding above & below the label in pixels
        backdropPaddingY: 2,

        //Number - The backdrop padding to the side of the label in pixels
        backdropPaddingX: 2,

        //Number - Limit the maximum number of ticks and gridlines
        maxTicksLimit: 11,
    },
  },
animation: {
        duration: 0
    }
  }

});




    </script>

</div>

  </body>
</html>