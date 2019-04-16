<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.1.0/milligram.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="http://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css"></style>
<script type="text/javascript" src="http://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
<title>LinkTest</title>
<style type="text/css">
body {
    background-color: #cccccc;
}
input[type=text] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  box-sizing: border-box;
  border: 4px solid blue;
  border-radius: 8px;
  border: none;
  border-bottom: 2px solid blue;
  color: black;
}
    ::placeholder {
      font-size: 1.3em;
      color:Grey;
      opacity: 2;
    }
    #table th {
    text-align: center;
    }
 table {
  width: 100%;
  table-layout: fixed;
}
.datatable td {
  overflow: hidden;
//  text-overflow: ellipsis; 
  white-space: nowrap;
}
</style>
<script>
    $(document).ready(function(){
        $('#table').dataTable({
            //"bJQueryUI":true,
            // "bSort":false,
//        language: { search: '', searchPlaceholder: "Search..." },
            "bPaginate":false,
            // "sPaginationType":"full_numbers",
            // "iDisplayLength": 20,
//  columns:{"width": "10%"}
    "columnDefs": [{ "width": "500px", "targets": 2 }],
    "autoWidth": false,
    "scrollX": false,
    "searching": false
        });
    });
</script>
</head>

<body style="width:100%;">
    <?php
    //error_reporting(E_ALL);
    //ini_set('display_errors', true);
    function getdb() {
        global $conn;
        global $dbUsername;
        require_once('dbconnect.php');
        return $conn;
        return $dbUsername;
        }

    if (isset($_POST['submit1'])) {
        if(!empty($_POST['getlink']) && !empty($_POST['searchcamp'])){
            echo "<script type=\"text/javascript\">
                                alert(\"Please Provide Either Link Or Campaign Name For Fired Link\");
                                window.location = \"singlelinks.php\"
                                </script>";
        }
        elseif(!empty($_POST['getlink'])) {
            $url = trim($_POST['getlink']);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $getlink = $url;
                $sqllink = "SELECT `campaign`, `country`, `link`, `server_status_code`, `timestamp`, `serial` FROM `link_test` WHERE `link` LIKE '%".$getlink."%' UNION SELECT `campaign`, `country`, `link`, `server_status_code`, `timestamp`, `serial` FROM `link_test_ios` WHERE `link` LIKE '%".$getlink."%' ORDER BY `timestamp` DESC";
            }
        }
        elseif(!empty($_POST['searchcamp'])) {
            $searchcamp = trim($_POST['searchcamp']);
            $sqlcamp = "SELECT `campaign`, `country`, `link`, `server_status_code`, `timestamp`, `serial` FROM `link_test` WHERE `campaign` = '".$searchcamp."' UNION SELECT `campaign`, `country`, `link`, `server_status_code`, `timestamp`, `serial` FROM `link_test_ios` WHERE `campaign` = '".$searchcamp."' ORDER BY `timestamp` DESC";
        }
        else{
            $ts = date("Y-m-d");
            $sqltoday = "select `campaign`, `country`, `link`, `server_status_code`, `timestamp`, `serial` FROM link_test  where timestamp like '".$ts."%' union select `campaign`, `country`, `link`, `server_status_code`, `timestamp`, `serial` from link_test_ios  where timestamp like '".$ts."%' ORDER BY `timestamp` DESC;";
            // echo $sqltoday;
        }
    }
    elseif(isset($_POST['submit']) && !empty($_POST['campaign']) && !empty($_POST['link'])) {
        $campaign = trim($_POST['campaign']);
        $url = trim($_POST['link']);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $getlink = $url;
        }
        else{
            echo "<script type=\"text/javascript\">
                                alert(\"InValid Link.\");
                                window.location = \"singlelinks.php\"
                                </script>";
        }
        $countryname = explode('-', $_POST['country']);
        $os = $_POST['os'];
        //echo $url;
        chdir ("/home/ubuntu/work/main0/");
        $cnt = $_POST['cnt'];
        for ($i=1;$i<=$cnt;$i++) { 
            if(isset($_POST['osver']) && !empty($_POST['osver'])){
                $osver= $_POST['osver'];
                exec ('PYTHON_ENV=production nohup python tester1.py -c \''.$campaign.'\' -d \''.$os.'\' -s \''.$countryname[1].'\' -n \''.$countryname[0].'\' -l \''.$getlink.'\' -os \''.$osver.'\' >> /home/ubuntu/output/res'.$i.'.out 2>&1 &');
            }
            else{
                exec ('PYTHON_ENV=production nohup python tester1.py -c \''.$campaign.'\' -d \''.$os.'\' -s \''.$countryname[1].'\' -n \''.$countryname[0].'\' -l \''.$getlink.'\' >> /home/ubuntu/output/res'.$i.'.out 2>&1 &');
            }
        }
    }
    ?>
    <div align="center" style="background-color:lightblue">
        <legend style="width: 100%;"><strong>Appanalytics Single Link's Fire</strong></legend>
    </div>
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <form action="singlelinks.php" method="post">
        <div align="left">
            <ul class="nav nav-tabs" style="width: 50%;">
                <!--<strong style="font-size: medium;">Os_Version</strong> -->
            <li class="active" span style="color:#8080ff"><a class="toggleSwitch" data-type="urlInput" style="font-size: 1.3em">Tracking Link</a></li>
            </ul>
            <input name="campaign" type="text" id="campaign" class="mainInput" autocomplete="on" placeholder="Campaign Name" style="width:10%" required>
            <input type="text" id="link" name="link" autocomplete="on" placeholder="Enter your affiliate/tracking link" style="width:20%" required>
            <input type="text" placeholder="Default o.s version " name="osver" id="osver" style="width: 20%"> 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select name="os" id ="os" style="width:10%; font-size: medium;">
            <option selected value="android" style="font-size: medium;">Android</option>
            <option value="ios" style="font-size: medium;">iPhone</option>
            </select>
            <select id ="countrylist" name = "country" style="width:10%; font-size: medium;">
            <option selected value="us-USA">United States (US)</option>
            <option value="dz-Algeria" style="font-size: medium;">Algeria (dz)</option>
            <option value="ar-Argentina" style="font-size: medium;">Argentina (ar)</option>
            <option value="au-Australia" style="font-size: medium;">Australia (au)</option>
            <option value="at-Austria" style="font-size: medium;">Austria (at)</option>
            <option value="bh-Bahrain" style="font-size: medium;">Bahrain (bh)</option>
            <option value="bd-Bangladesh" style="font-size: medium;">Bangladesh (bd)</option>
            <option value="be-Belgium" style="font-size: medium;">Belgium (be)</option>
            <option value="bo-Bolivia" style="font-size: medium;">Bolivia (bo)</option>
            <option value="br-Brazil" style="font-size: medium;">Brazil (br)</option>
            <option value="cm-Cameroon" style="font-size: medium;">Cameroon (cm)</option>
            <option value="ca-Canada" style="font-size: medium;">Canada (ca)</option>
            <option value="cl-Chile" style="font-size: medium;">Chile (cl)</option>
            <option value="cn-China" style="font-size: medium;">China (cn)</option>
            <option value="co-Colombia" style="font-size: medium;">Colombia (co)</option>
            <option value="cr-CostaRica" style="font-size: medium;">CostaRica (cr)</option>
            <option value="hr-Croatia" style="font-size: medium;">Croatia (hr)</option>
            <option value="cz-CzechRepublic" style="font-size: medium;">CzechRepublic (cz)</option>
            <option value="dk-Denmark" style="font-size: medium;">Denmark (dk)</option>
            <option value="eg-Egypt" style="font-size: medium;">Egypt (eg)</option>
            <option value="fi-Finland" style="font-size: medium;">Finland (fi)</option>
            <option value="fr-France" style="font-size: medium;">France (fr)</option>
            <option value="de-Germany" style="font-size: medium;">Germany (de)</option>
            <option value="gh-Ghana" style="font-size: medium;">Ghana (gh)</option>
            <option value="gr-Greece" style="font-size: medium;">Greece (gr)</option>
            <option value="hk-HongKong" style="font-size: medium;">HongKong (hk)</option>
            <option value="is-Iceland" style="font-size: medium;">Iceland (is)</option>
            <option value="id-Indonesia" style="font-size: medium;">Indonesia (id)</option>
            <option value="ir-Iran" style="font-size: medium;">Iran (ir)</option>
            <option value="ie-Ireland" style="font-size: medium;">Ireland (ie)</option>
            <option value="il-Israel" style="font-size: medium;">Israel (il)</option>
            <option value="it-Italy" style="font-size: medium;">Italy (it)</option>
            <option value="jp-Japan" style="font-size: medium;">Japan (jp)</option>
            <option value="jo-Jordan" style="font-size: medium;">Jordan (jo)</option>
            <option value="ke-Kenya" style="font-size: medium;">Kenya (ke)</option>
            <option value="kw-Kuwait" style="font-size: medium;">Kuwait (kw)</option>
            <option value="lv-Latvia" style="font-size: medium;">Latvia (lv)</option>
            <option value="lb-Lebanon" style="font-size: medium;">Lebanon (lb)</option>
            <option value="lu-Luxembourg" style="font-size: medium;">Luxembourg (lu)</option>
            <option value="my-Malaysia" style="font-size: medium;">Malaysia (my)</option>
            <option value="mx-Mexico" style="font-size: medium;">Mexico (mx)</option>
            <option value="ma-Morocco" style="font-size: medium;">Morocco (ma)</option>
            <option value="mm-Myanmar" style="font-size: medium;">Myanmar (mm)</option>
            <option value="‎ng-Nigeria" style="font-size: medium;">Nigeria (‎n)</option>
            <option value="no-Norway" style="font-size: medium;">Norway (no)</option>
            <option value="om-Oman" style="font-size: medium;">Oman (om)</option>
            <option value="pk-Pakistan" style="font-size: medium;">Pakistan (pk)</option>
            <option value="pe-Peru" style="font-size: medium;">Peru (pe)</option>
            <option value="ph-Philippines" style="font-size: medium;">Philippines (ph)</option>
            <option value="pl-Poland" style="font-size: medium;">Poland (pl)</option>
            <option value="ro-Romania" style="font-size: medium;">Romania (ro)</option>
            <option value="ru-Russia" style="font-size: medium;">Russia (ru)</option>
            <option value="sa-SaudiArabia" style="font-size: medium;">SaudiArabia (sa)</option>
            <option value="sn-Senegal" style="font-size: medium;">Senegal (sn)</option>
            <option value="sg-Singapore" style="font-size: medium;">Singapore (sg)</option>
            <option value="sk-Slovakia" style="font-size: medium;">Slovakia (sk)</option>
            <option value="si-Slovenia" style="font-size: medium;">Slovenia (si)</option>
            <option value="za-SouthAfrica" style="font-size: medium;">SouthAfrica (za)</option>
            <option value="kr-SouthKorea" style="font-size: medium;">KoreaSouth (kr)</option>
            <option value="es-Spain" style="font-size: medium;">Spain (es)</option>
            <option value="lk-SriLanka" style="font-size: medium;">SriLanka (lk)</option>
            <option value="sd-Sweden" style="font-size: medium;">Sweden (sd)</option>
            <option value="ch-Switzerland" style="font-size: medium;">Switzerland (ch)</option>
            <option value="tw-Taiwan" style="font-size: medium;">Taiwan (tw)</option>
            <option value="tz-Tanzania" style="font-size: medium;">Tanzania (tz)</option>
            <option value="th-Thailand" style="font-size: medium;">Thailand (th)</option>
            <option value="tn-Tunisia" style="font-size: medium;">Tunisia (tn)</option>
            <option value="tr-Turkey" style="font-size: medium;">Turkey (tr)</option>
            <option value="ug-Uganda" style="font-size: medium;">Uganda (ug)</option>
            <option value="ua-Ukraine" style="font-size: medium;">Ukraine (ua)</option>
            <option value="ae-UnitedArabEmirates" style="font-size: medium;">UnitedArabEmirates (ae)</option>
            <option value="uk-UK" style="font-size: medium;">UnitedKingdom (uk)</option>
            <option value="vn-Vietnam" style="font-size: medium;">Vietnam (vn)</option>
            <option value="ye-Yemen" style="font-size: medium;">Yemen (ye)</option>
            <option value="in-India" style="font-size: medium;">India (in)</option>
            </select>
            <select name="cnt" id ="cnt" style="width: 10%; font-size: medium;" >
            <option selected value="1" style="font-size: medium;">Repeat (ONE Default)</option>
            <option value="2" style="font-size: medium;">Two Times</option>
            <option value="3" style="font-size: medium;">Three Times</option>
            <option value="4" style="font-size: medium;">Four Times</option>
            <option value="5" style="font-size: medium;">Five Times</option>
            </select>
            <input type="submit" name="submit" id="Submit" value="Submit">
        </div>
    </form>

    <form action="singlelinks.php" method="post" name="getdata">
            <div align="left" id = "getdata"  style="width: 90%;">
                <ul class="nav nav-tabs" style="width: 90%;">
                <li class="active"><a class="toggleSwitch" data-type="urlInput" style="font-size: 1.3em">Fired Data</a></li><br>
                <input name="searchcamp" type="text" id="searchcamp" placeholder="Find Campaign Here" style="width: 20%" value="<?php if(isset($searchcamp)) echo $searchcamp;?>">
                <input type="text" id="getlink" name="getlink" placeholder="Enter your affiliate/tracking link For Fired Status" style="width: 50%" value="<?php if(isset($getlink)) echo $getlink;?>">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" name="submit1" id="Submit1" value="Check Status">
                </ul>
            </div>
    </form>
</body>
    <?php if(isset($getlink) || isset($searchcamp) || isset($sqltoday)) { ?>
    <div style="width: 100%">
        <table class="table table-hover" id="table">
            <thead style="background-color:lightblue">
                <tr>
                  <th scope="col">Serial</th>
                  <th scope="col">Campaign</th>
                  <th scope="col">Link</th>
                  <th scope="col">Country</th>
                  <th scope="col">Fired_Status</th>
                  <th scope="col">Date&Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(isset($getlink) || isset($searchcamp) || isset($sqltoday)) {
                    //sleep(2);
                    getdb();
                    if($conn) {
                        if(isset($searchcamp)){
                            $getdetail= $sqlcamp;
                        }
                        elseif(isset($getlink)) {
                            $getdetail= $sqllink;
                        }
                        elseif(isset($sqltoday)){
                            $getdetail=$sqltoday;
                        }
                        $result = mysqli_query($conn, $getdetail);
                        $count = mysqli_num_rows($result);
                        if ($count > 0){
                            $num = 1;
                            while ($row = mysqli_fetch_array($result))
                            {
                                echo "<tr>";
                                echo "<td>" .$num. "</td>";
                                echo "<td>" .$row['campaign']. "</td>";
                                echo "<td>" .$row['link']. "</td>";
                                echo "<td>" .$row['country']. "</td>";
                                if($row['server_status_code']=='10'){
                                    $stat = 'Conversion Fired';
                                }
                                elseif($row['server_status_code']=='11'){
                                    $stat = 'False_NO:tracker_reff';
                                }
                                elseif($row['server_status_code']=='12'){
                                    $stat = 'False:GotStuck';
                                }
                                elseif($row['server_status_code']=='4' || $row['server_status_code']=='5'){
                                    $stat = 'Not Working';
                                }
                                elseif($row['server_status_code']=='0'){
                                    $stat = 'On the way';
                                }
                                else{
                                    $stat = 'There\'s Some Problem!';
                                }
                                echo "<td>" .$stat. "</td>";
                                $userTimezone = $row['timestamp'];
                                $userTimezone = date("j M, g:i a", strtotime('+5 hour +30 minutes', strtotime($userTimezone)));
                                echo "<td>" .$userTimezone. "</td>";
                                echo "</tr>";
                                $num += 1;
                            }
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
<footer>
        <script type="text/javascript">
            if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        </script>
</footer>
