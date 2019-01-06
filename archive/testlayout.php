<?php
    #ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

    include 'apikeys.php';

    $page = $_SERVER['PHP_SELF'];

    #Seconds to refresh the webpage
    $sec = "600";

    #NewsAPI Call
    $sources = "the-new-york-times,bbc-news,abc-news,al-jazeera-english,bloomberg,business-insider,fox-news,bleacher-report,associated-press,espn,nbc-news";
    $newsContents = file_get_contents("https://newsapi.org/v2/top-headlines?sources=" . $sources . "&apiKey=" . $newsKey);
    $newsData = json_decode($newsContents, true);

    #Trello API Call
    $list = "5c279a32c559d774922e4c96";
    $trelloUrl = file_get_contents("https://api.trello.com/1/lists/" . $list . "/cards?fields=name&key=" . $trelloKey . "&token=" . $trelloToken);
    $trelloData = json_decode($trelloUrl, true); #trello cards

    #RescueTime API Call
    $urlContents = file_get_contents("https://www.rescuetime.com/anapi/data?key=".$rescuetimeKey."&perspective=rank&interval=hour&restrict_begin=" . date('Y-m-d') . "&restrict_end=" . date('Y-m-d') . "&format=json");
    $urlContentsY = file_get_contents("https://www.rescuetime.com/anapi/data?key=".$rescuetimeKey."&perspective=rank&interval=hour&restrict_begin=" . date('d.m.Y', strtotime("-1 days")) . "&restrict_end=" . date('d.m.Y', strtotime("-1 days")) . "&format=json");
    $urlContents2 = file_get_contents("https://www.rescuetime.com/anapi/daily_summary_feed?key=".$rescuetimeKey);
    $data = json_decode($urlContents, true);
    $dataY = json_decode($urlContentsY, true); #rescuetime yesterday's data
    $data2 = json_decode($urlContents2, true); #recuetime daily summary

    $posTotal = 0; #Positive seconds (seconds spent on a program times the productivity value)
    $negTotal = 0; #Negative seconds (seconds spent on a program times the productivity value)
    $absTotal = 0;
    #this for loop gets seconds spent from the json file
    if(is_array($data))
    {
      foreach ($data['rows'] as $key => $value) {
          $productivity = $value[1] * $value[5];
          #print_r($productivity);
          if ($productivity < 0) {
              $negTotal = $negTotal + $productivity;
          }
          $absTotal = $absTotal + abs($productivity);
      }
    };
    #gets the categories for each value
    $categoriesArray = array();
    $seconds = array();
    $totalSeconds = 0;
    if(is_array($data))
    {
      foreach ($data['rows'] as $key => $value) {
          $categoriesArray[] = $value[4];
          $seconds[] = floor($value[1] / 60);
          $totalSeconds = $totalSeconds + $value[1];
      }
    };
    #radar graph data
    $js_array = json_encode($categoriesArray);
    $js_array2 = json_encode($seconds);
    
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
    if(is_array($dataY))
    {
      foreach ($dataY['rows'] as $key => $value) {
          $categoriesArrayY[] = $value[4];
          $secondsY[] = floor($value[1] / 60);
          $totalSecondsY = $totalSecondsY + $value[1];
          #echo "it is: ".$categoriesArrayY;
          #echo "<br>";
      }
    };
    #radar graph data for Yesterday
    $js_arrayY = json_encode($categoriesArrayY);
    #echo "var javascript_array = ". $js_array . ";\n";
    $js_array2Y = json_encode($secondsY);
    #calculate productive hours
    $productiveHours = array();
    $distractiveHours = array();
    $i = 0;
    if(is_array($data2)) 
    {
      foreach ($data2 as $value) {
          if ($i < 7) {
              $productiveHours[] = $value['all_productive_hours'];
              $distractiveHours[] = $value['all_distracting_hours'];
          }
          $i = $i + 1;
      }
    };
    #print_r($productiveHours);
    #print_r($distractiveHours);
    $js_productiveHours = json_encode($productiveHours);
    $js_distractiveHours = json_encode($distractiveHours);

?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pi Productivity Dashboard</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>


        <style type="text/css"> 

            * {
                box-sizing: border-box;
            }

            .row::after { 
                content: ""; 
                clear: both; 
                display: table; 
            }

            [class*="col-"] {
                width: 100%;
                float: left;
            }

            @media only screen and (min-width: 800px) {
                .col-1 {width: 8.33%;}
                .col-2 {width: 16.66%;}
                .col-3 {width: 25%;}
                .col-4 {width: 33.33%;}
                .col-5 {width: 41.66%;}
                .col-6 {width: 50%;}
                .col-7 {width: 58.33%;}
                .col-8 {width: 66.66%;}
                .col-9 {width: 75%;}
                .col-10 {width: 83.33%;}
                .col-11 {width: 91.66%;}
                .col-12 {width: 100%;}
            }

            canvas{
            }

            html{
                font-size: 12px;
                background-color: black;
            }

            a:link{
                color: white; 
            }

            a:visited{
                color: grey; 
            }
            
            a:hover{
                text-decoration: underline;
            }

            .container{
                background-color: black;
            }

            #headlines{
                color: white;
                font-size: 12px;
                padding-left: 16px;
                background-color: black;
            }

            #trelloTasks{
                color: white; 
                font-size: 12px;
                padding-left: 16px;
                background-color: black;
            }

            .line{
                position: relative;
                padding-left: 16px;
            }

            .radar{
                position: relative;
                padding-left: 16px;
            }

            .doughnut{
                position: relative;
                padding-left: 16px;
            }
        </style> 
    </head>
    <body>
        <div class="container">
            <div class="row"> 
                <div id="trelloTasks" class="col-6">
                    <h4>Trello Task List</h4>
                    <?php
                        $i = 0;
                        if(is_array($trelloData))
                        {   
                            foreach ($trelloData as $value) {
                                if ($i < 6) {
                                echo "&#x25a2";
                                echo " " . $value['name'];
                                echo "<br>";
                                }
                            $i = $i + 1;
                            }
                        };
                    ?>
                </div>
                <div id="headlines" class="col-6">
                    <h4>Current Headlines</h4>
                    <?php
                        $i = 1;
                        $headLength = 50;
                        $agencyLength = 5;
                        do {
                            $agency = $newsData['articles'][$i]['source']['name'];
                            $headline = $newsData['articles'][$i]['title'];
                            $url = $newsData['articles'][$i]['url'];
                            echo nl2br("<a href='".$url."' target='blank'>[".$agency."] ".$headline."</a>\n");
                            $i = $i + 1;
                        } while ($i < 7);
                    ?>
                </div>            
            </div>
            <div class="row"> 
                <div class="col-4">
                    <div id="radar">
                        <canvas id="myRadarChart"></canvas>
                    </div>
                </div>
                <div class="col-4">
                    <div id="line">
                        <canvas id="myLineChart"></canvas>
                    </div>
                </div>
                <div class="col-4">
                    <div id="doughnut">
                        <canvas id="myDoughnutChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row"> 
                <div class="col-12">
                    <!-- Min counter & other information -->
                </div>
            </div> 
        </div>
    </body>

    <script>
        Chart.defaults.global.defaultFontColor = '#fff';
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

        //Doughnut chart
            var ctx = document.getElementById("myDoughnutChart");
            var negative = "<?php echo (abs($negTotal) * 100) / $absTotal; ?>";
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
                    elements: 
                        { arc: { borderWidth: 0 } },
                        animation: {duration: 0}
                }
            });

        //Linechart 
            <?php echo "var productiveHours= " . $js_productiveHours . ";\n"; ?>
            <?php echo "var distractiveHours = " . $js_distractiveHours . ";\n"; ?>
                productiveHours.reverse();
                distractiveHours.reverse();
                var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                var d = new Date();
                var n = d.getDay();
                for (i = 0; i <=(6- d.getDay()); i++) {
                    days.splice(0,0,days[6]);
                    days.splice(7,1);
                }
                //console.log(days);
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
        //Radar Chart
            <?php echo "var categories= " . $js_array . ";\n"; ?>
            <?php echo "var secondsArray = " . $js_array2 . ";\n"; ?>
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
            //yesterdays data
            <?php echo "var categoriesY= " . $js_arrayY . ";\n"; ?>
            <?php echo "var secondsArrayY = " . $js_array2Y . ";\n"; ?>
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

</html>