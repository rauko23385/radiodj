<?php
/*Request script with wait time between selects.Made by: Jhonny Date: 12-21-2016 Edited by: rauko Date: 09-18-2018*/

########### edit settings #########

$dataip = "http://localhost"; // your external IP address not the IP address the router assigns your computer.
$database = "test"; // Databse name for mysql database containing music info.
$username = "root";  //database user name for requests not 'root' user name.
$userpass = "changeme"; // database password for requests not 'root' password.
$dataport = "3306";  // database port number for mysql.
$timezone = "Europe/london";  // Your time zone where you live,see http://http://php.net/manual/en/timezones.php for timezone info
$reqLimit = '5'; // Request limit per day.
$req_wait = '10'; // waiting minutes between requests.
$req_event = '0'; // showing how many minutes between requests will be played in event. 0 = don't show.
$reqsubcat = ''; // exclude subcats use id numbers like 1,2,3  Empty for show all.
$page_limit = '15'; // How many tracks showing in the page.
$track_repeat = '300'; // Same track can't play in minutes.
$artist_repeat = '60'; // Same artist can't play in minutes.

###### End Edit settings #######

##### Standard #####

$adjacents = '3'; // paginations blocks

#### language part ########

$lang = array();
$lang['ALT_REQ']= "Select song";
$lang['GO_BACK']= "Go Back";
$lang['COL_NUM']= "No:";
$lang['COL_ARTIST']= "Artist name";
$lang['COL_DURATION']= "Duration";
$lang['COL_REQ']= "Select";
$lang['COL_TITLE']= "Title of the track";
$lang['ERROR_FIND']= "Did not find what you were looking for! Search again.";
$lang['ERROR_LIMITREACHED']= "Sorry, but you've reached the request limit for one day.";
$lang['ERROR_LIMITREACHED2']= "You've now reach the request limit for this day.";
$lang['ERROR_REQUEST']= "The song can't be requested it is already played and has a waiting time.";
$lang['ERROR_SONGREQ']= "The selected song is already requested.<br />Please try again later, or select another song!";
$lang['ERROR_TRACKID']= "Please select a song in order to send the request! Go Back";
$lang['ERROR_TRACKREQ']= "Sorry, this track is in the request list and not played yet.";
$lang['ERROR_UNKNOWN']= "Unknown error occurred! Try again ...";
$lang['ERROR_USERNAME']= "Please enter your name in order to send a request!";
$lang['ERROR_TRACKREQ_TIME']= "Sorry, the time between your next requests is not over yet, you have to wait ";
$lang['ERROR_TRACKREQ_TIME2']= " more minutes.<br>(You can click om F5 to set your special request after the waiting time, and click enter).";
$lang['MSG_NORESULTS']= "No results to display, on your search...";
$lang['MSG_REQSUCCESS']= "Your request for this song, was successfully placed, it's! ";
$lang['REQ_BUTTON']= "Send Selection";
$lang['REQ_DESCRIPTION']= "Please enter your selection details bellow";
$lang['REQ_MESSAGE']= "Message (Optional):";
$lang['REQ_NAME']= "Your Name:";
$lang['REQ_PLAYED']= "This song is played or requested, be for your selection, sorry.";
$lang['REQ_PLAYNO']= "This track can't be selected now, try it later.";
$lang['REQ_SELECT']= "Select";
$lang['REQ_SELECTED']= "You've selected:";
$lang['REQUEST_DIS']= "You can request a song to put in the top playlist of the station";
$lang['REQ_A_TRACK']= "Track Requests";
$lang['REQUEST']= "Request a song";
$lang['SEARCH_BUTTON']= "Search";
$lang['SEARCH_TXT']= "Search on artist name<br>(or part of the name): <br>";
$lang['SEE_RULS_A']= "Select rules: no more than";
$lang['SEE_RULS_B']= "per day on IP address";
$lang['NAV_NEXT']= "NEXT";
$lang['NAV_PREV']= "PREVIOUS";
$lang['COL_YEAR']= "Year";
$lang['MSG_REQSUCCESS_MADE'] = "You made a total request of ";
$lang['MSG_REQSUCCESS_TOT'] =  "of the ";
$lang['MSG_REQSUCCESS_DAY'] = "limited per day.";
$lang['WAIT'] = "You have to wait ";
$lang['WAIT2'] = " Min. before you can make a new request.";
$lang['REQ_LISTED'] = "Track is in the request list and not played yet.";
$lang['SELECTED'] = "Is selected";
$lang['REQ_EVENT'] = "Every ";
$lang['REQ_EVENT2'] = "minutes a request is played!";
$lang['REQUESTED_TRACK_LIMITS'] ="Requests Are Limited To ( ";
$lang['REQUESTED_TRACK_LIMITS1'] =" ) Per Day And ( ";
$lang['REQUESTED_TRACK_LIMITS2'] =" ) Minutes Between.";
$lang['NOREQ'] = "<b>XXXXX</b>";
$lang['OFFLINE'] = "The station is OFFLINE";

########### Don't edit below ##########

date_default_timezone_set($timezone);
 ### Connection ###
$conn = mysqli_connect($dataip,$username,$userpass,$database,$dataport);

########### functions ###################

function convertTime($seconds) { $sec = $seconds;        // Time conversion
        $hours = intval(intval($sec) / 3600);
        $padHours = True;
        $hms = ($padHours)                ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'                : $hours. ':';
        $minutes = intval(($sec / 60) % 60);
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
        $seconds = intval($sec % 60);
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
 return $hms;
}

function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){ $ip=$_SERVER['HTTP_CLIENT_IP']; //check ip from share internet
        }else {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){  $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; //to check ip is pass from proxy
        } else {            $ip=$_SERVER['REMOTE_ADDR'];        }
        return $ip;
}

function track_can_play($tr_played, $art_played){ global $track_repeat, $artist_repeat; //this should work on any php version

 $date1 = strtotime($tr_played);
 $date2 = time();
 $subTime = $date1 - $date2;
 $tr_min = round(abs($subTime/60));

 $date1 = strtotime($art_played);
 $date2 = time();
 $subTime = $date1 - $date2;
 $ar_min = round(abs($subTime/60));
 if($tr_min > $track_repeat && $ar_min > $artist_repeat){  return true; }else{  return false; }
}

?>
<!DOCTYPE html>
<html>
 <head>
<meta charset="utf-8" />
 <style>
<body>
{
    font-family: Verdana, Geneva;
    color: #000000;
    background-color: #DDDDDD;
}

/* Default link colors */
a {    color: #2E8CB8;}
a:hover,
a:focus {    color: #004666;}
.paginate {
    font-family:Arial, Helvetica;
    padding: 3px;
    margin: 3px;
}
.paginate a {
    padding:2px 5px 2px 5px;
    margin:2px;
    border:1px solid #999;
    text-decoration:none;
    color: #666;
}
.paginate a:hover, .paginate a:active {
    border: 1px solid #999;
    color: #990033;
}

.paginate span.current {
    margin: 2px;
    padding: 2px 5px 2px 5px;
    border: 1px solid #999;
    border-radius: 25px;
    font-weight: bold;
    background-color: #CC6633;
    color: #FFFF33;
}

.paginate span.disabled {
    padding:2px 5px 2px 5px;
    margin:2px;
    border:1px solid #eee;
    color:#E0FFFF;
}

.requestcontainer{
    width: 70%;
    border: 1px;
    color: #000000;
    background-color: #FFCC66;
    font-size: medium;
}
.errordiv{
    align-content: center;
    background-color: #990000;
    width: 70%;
    color: #FFFF00;
    font-weight: bold;
    font-size: medium;
}
.errordiv2{
    align-content: center;
    background-color: #666600;
    width: 70%;
    color: #FFFF00;
    font-weight: bold;
    font-size: medium;
}
.errordiv3{
    align-content: center;
    background-color: #666600;
    width: 90%;
    color: #FFFF00;
    font-weight: bold;
    font-size: medium;
}
.noticediv{
    align-content: center;
    background-color: #FFFF33;
    width: 70%;
    color: #333333;
    font-weight: bold;
    font-size: medium;
}
 </style>
 </head>
<body>
<?php
if (!$conn){
     Echo "<center><table class=\"table\" 
style=\"width: 300px; text-align: center\"><td>".$lang
['OFFLINE']."</td></table></center>";
}else{
?>

<center><table class="table" style="width: 

700px"><td><center><?php echo $lang['REQUESTED_TRACK_LIMITS'].''.

$reqLimit.''.$lang['REQUESTED_TRACK_LIMITS1'].''.

$req_wait.''.$lang['REQUESTED_TRACK_LIMITS2'];  ?> 

</center></td></table>

<?php
if (!$req_event == 0){
echo "<table class=\"table\" style=\"width: 400px; text-align: center\"><td>".$lang['REQ_EVENT']." ".$req_event." ",$lang['REQ_EVENT2']."</td></table>";
}
?>
<p><table class="table"  style="width: 200px; text-align: center"><td>
<?php echo $lang['REQ_A_TRACK']; ?></td></table></p>
     <table class="table"  style="width: 60%">
        <tr>
            <td>

<?php
$targetpage    = $_SERVER['SCRIPT_NAME'];  

//Link to this page

$srch = "";
$srchpath = ""; 
$srcquery = "";
$page = 1;
$reqid = "";
$notin = "AND id_subcat NOT IN(".$reqsubcat.")";

echo '<br>';
if(isset($_GET['searchterm'])){
 if($_GET['searchterm'] != "") {
    if (!empty($reqsubcat)){
  $srch = 

mysqli_real_escape_string($conn, $_GET

['searchterm']);
  $srchpath = "&searchterm=

$srch";
  $srcquery = "AND (`artist` LIKE '%$srch%') $notin ";
//Search artist and title not in selected subcats
        }else{
            $srch = mysqli_real_escape_string($conn, $_GET['searchterm']);
  $srchpath = "&searchterm=
$srch";
  $srcquery = "AND (`artist` LIKE '%$srch%')"; //Search artist and title
        }
 }
    echo "<center><div id=\"notfind\">".

$lang['ERROR_FIND']."<br><br>
    <A HREF=\"javascript:javascript:history.go(-1)\"><button>".
$lang['GO_BACK']."</button></A></div></center>";

}

//Get the page if it's requested
if(isset($_GET['page'])){
 $page = mysqli_real_escape_string($conn, $_GET['page']);
}

if($page){
 $start = ($page - 1) * $page_limit;
}else{
 $start = 0;
}

if(isset($_POST['reqsubmit'])){

 /*
 ERROR CODES:
 0 = No error
 1 = no user name
 2 = no requested track
 3 = track already in queue
 4 = request limit reached
 5 = Waiting time not over
 */

 $reqname = mysqli_escape_string($conn, $_POST['requsername']);
 $reqmsg = mysqli_escape_string($conn, $_POST['reqmessage']);
 $reqsongID = mysqli_escape_string($conn, $_POST['songID']);
 $reqIP = getRealIpAddr();
 $error = 0;
 $reccount = 0;
 if(!$reqname){$error = 1;}
 if(!$reqsongID){$error = 2;}
 if($error == 0){
  //track is already requested?
  $recheck = "SELECT COUNT(*) AS num FROM `requests` WHERE `songID`='$reqsongID' AND `played`=0;";
  $total_req = mysqli_fetch_array(mysqli_query($conn, $recheck));
  if($total_req['num'] > 0){   $error = 3;
  }
  @mysqli_free_result($conn, $total_req);
  if($error == 0){
   //user has reached the request limit?
   $recheck = "SELECT COUNT(*) AS num FROM `requests` WHERE `userIP`='$reqIP' AND DATE(`requested`) = DATE(NOW());";
   $total_req = mysqli_fetch_array(mysqli_query($conn, $recheck));
   if($total_req['num'] >= $reqLimit){
    $error = 4;
    $reccount = $total_req['num'];
   }
 # check if waiting time is over for new request.
$sql= "SELECT * FROM `requests` WHERE `userIP`='$reqIP' ORDER BY `requested`  DESC LIMIT 1 ";
  $res = $conn->query($sql);

 if ($res->num_rows > 0) {
while($row = mysqli_fetch_assoc($res)) {
$timex= $row['requested'];
}
$calc = strtotime($timex);
$calc2 = date('Y-m-d H:i:s');
$calc3 = strtotime($calc2);
$calc4 = ($calc3-$calc) / 60;
$min = intval($calc4);

if ($min < $req_wait){
    $error = 5;
}
}
# check waiting time end.
            @mysqli_free_result($conn, $total_req);
     }
 }

 switch ($error) {
  case 0:
        echo "<center>";
            $queryx = "INSERT INTO `requests` SET `songID`='$reqsongID', `username`='$reqname', `userIP`='$reqIP', `message`='$reqmsg', `requested`=now();";
   $resultx = mysqli_query($conn, $queryx);
   if($resultx > 0) {       $nu1= $total_req['num'];                $nu2 = $nu1+1;
    echo "<div class=\"noticediv\">" . $lang['MSG_REQSUCCESS'] . " <b>".$nu2."</b> ".$lang['MSG_REQSUCCESS_TOT']." <b>".

$reqLimit."</b>";
If ($nu2 == $req_wait){
    echo "<center><div class=\"errordiv\">" . $lang['ERROR_LIMITREACHED2'] . "</div></center><br><br>";
}else
{ echo "<br />".$lang['WAIT']." ".$req_wait." ".$lang['WAIT2']."</div>";}
   } else {
    echo "<center><div class=\"errordiv\"><center>" . $lang['ERROR_UNKNOWN'] . "</center></div></center><br /><br>";
   }

   @mysqli_free_result($conn, $resultx);
            echo '</center>';
   break;
  case 1:
        echo '<center>';
   echo 
"<br><center><div class=\"errordiv\"><center>" . $lang['ERROR_USERNAME'] . "</center></div></center><br />";
   echo 
"<center><A HREF=\"javascript:javascript:history.go(-1)\">".$lang['GO_BACK']."</A></center><br>";
   break;
            echo '</center>';
  case 2:
   echo 
"<br><center><div class=\"errordiv\"><center>" . $lang['ERROR_TRACKID'] . " </center></div></center><br />";
            echo 
"<center><A HREF=\"javascript:javascript:history.go(-1)\">".$lang['GO_BACK']."</A></center><br><br>";
   break;
  case 3:
   echo 
"<br><center><div class=\"errordiv\"><center>" . $lang['ERROR_TRACKREQ'] . "</center></div></center><br /><br>";
   break;
  case 4:
   echo 
"<br><center><div class=\"errordiv\"><center>" . $lang['ERROR_LIMITREACHED'] . " (" . $reccount . "/" . $reqLimit . ")" . "</center></div></center><br /><br>";
   break;
  case 5:
        $wait_time= $req_wait-$min;
   echo 
"<br><center><div class=\"errordiv3\">" . 

$lang['ERROR_TRACKREQ_TIME'] . " ".
$wait_time." ".$lang['ERROR_TRACKREQ_TIME2']."</div></center><br>";
   break;
        }


 $reqid = "";
}

//Get the page if it's requested
if(isset($_GET['requestid'])){

 if($_GET['requestid'] != "") {

   $reqid = mysqli_escape_string($conn, $_GET['requestid']);


            echo '<center><table class="requestcontainer"><tr><td>';

            echo "<center>".$lang['REQ_SELECTED']."</center></td></tr>";//Get track artist Name and title.
$querysong = "SELECT * FROM `songs` WHERE `ID` = $reqid ";
$queryderp = mysqli_query($conn, $querysong);
while($row = mysqli_fetch_assoc($queryderp)) {
$title= $row['title'];
$artist= $row['artist'];
}

   echo "    <tr>\n";
   echo "       <td colspan=\"2\"><div align=\"center\"><p>".$artist." - ".$title."</p></div></td>";
   echo "    </tr>";
   echo "   <table>";
   echo " <form id=\"formrequest\" name=\"formrequest\" method=\"post\" action=\"$targetpage?page=$page$srchpath\">";
   echo "   <table align=\"center\" width=\"500\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">";
   echo "    <tr>";
   echo "     <td colspan=\"2\"><div align=\"center\"><p>" . $lang['REQ_DESCRIPTION'] . "</p></div></td>";
   echo "    </tr>";
   echo "    <tr>";
   echo "     <td>" . $lang['REQ_NAME'] . "</td>";
   echo "     <td><input type=\"text\" name=\"requsername\" /></td>";
   echo "    </tr>";
   echo "    <tr>";
   echo "     <td valign=\"top\">" . $lang['REQ_MESSAGE'] . "</td>";
   echo "     <td><textarea name=\"reqmessage\" cols=\"43\" rows=\"5\"></textarea></td>";
   echo "    </tr>";
   echo "    <tr>";
   echo "     <td colspan=\"2\"><div align=\"center\"><input type=\"Submit\" name=\"reqsubmit\" value=\"" . $lang['REQ_BUTTON'] . "\" /></div></td>";
   echo "    </tr>";
   echo "   </table>";
   echo "   <INPUT TYPE=\"hidden\" name=\"songID\" value=\"$reqid\">";
   echo "  </form>";
   echo " </div>";
   echo "</td></table>";
   echo "<center><br><br><A HREF=\"javascript:javascript:history.go(-1)\"><button>".$lang['GO_BACK']."</button></A></center>";
   echo '</td></table></center>';

 }
}

//================//

if($reqid == ""){
 //Get the number of items
    if (!empty($reqsubcat)){        $query = "SELECT COUNT(*) as num FROM `songs` WHERE `enabled`='1' $srcquery AND `song_type`=0 $notin ";
 $total_pages = mysqli_fetch_array(mysqli_query($conn, $query));
 $total_pages = $total_pages['num'];
    }else{
 $query = "SELECT COUNT(*) as num FROM `songs` WHERE `enabled`='1' $srcquery AND `song_type`=0";
 $total_pages = mysqli_fetch_array(mysqli_query($conn, $query));
 $total_pages = $total_pages['num'];
    }
 //Get page data   (To inplant left join queuelist see)
  if (!empty($reqsubcat)){ $query1 = "SELECT `ID`, `artist`, `title`, `year`, `duration`, `date_played`, `artist_played`,`id_subcat` FROM `songs` WHERE `enabled`='1' $srcquery AND`song_type`=0 $notin ORDER BY `artist` ASC LIMIT $start, $page_limit"; $result = mysqli_query($conn, $query1);
  }else{    $query1 = "SELECT `ID`, `artist`, `title`, `year`, `duration`, `date_played`, `artist_played`,`id_subcat` FROM `songs` WHERE `enabled`='1' $srcquery AND`song_type`=0 ORDER BY `artist` ASC LIMIT $start, $page_limit"; $result = mysqli_query($conn, $query1);  }
 // Initial page num setup
 if ($page == 0){$page = 1;}
 $prev = $page - 1;
 $next = $page + 1;
 $lastpage = ceil($total_pages/$page_limit);
 $LastPagem1 = $lastpage - 1;
 $paginate = '';
 if($lastpage > 1) {  $paginate .= "<div class='paginate'>";
  // Previous
  if ($page > 1){   $paginate.= "<a href='$targetpage?page=$prev$srchpath'>" . $lang['NAV_PREV'] . "</a> ";  }
  // Pages
  if ($lastpage < 7 + ($adjacents * 2)) {
   for ($counter = 1; $counter <= $lastpage; $counter++) {
    if ($counter == $page){
     $paginate.= "<span class='current'> $counter </span>";
    }else{
     $paginate.= "<a href='$targetpage?page=$counter$srchpath'> $counter </a>";
    }
   }
  } elseif($lastpage > 5 + ($adjacents * 2)) {

  // Beginning only hide later pages
   if($page < 1 + ($adjacents * 2)) {    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
     if ($counter == $page){
      $paginate.= "<span class='current'> $counter </span>";
     }else{      $paginate.= "<a href='$targetpage?page=$counter$srchpath'> $counter </a>";
     }
    }
    $paginate.= "...";
    $paginate.= "<a href='$targetpage?page=$LastPagem1$srchpath'> $LastPagem1 </a>";
    $paginate.= "<a href='$targetpage?page=$lastpage$srchpath'> $lastpage </a>";
   } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
    $paginate.= "<a href='$targetpage?page=1$srchpath'> 1 </a>";
    $paginate.= "<a href='$targetpage?page=2$srchpath'> 2 </a>";
    $paginate.= "...";
    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++){
     if ($counter == $page){
      $paginate.= "<span class='current'> $counter </span>";
     }else{      $paginate.= "<a href='$targetpage?page=$counter$srchpath'> $counter </a>";
     }
    }
    $paginate.= "...";
    $paginate.= "<a href='$targetpage?page=$LastPagem1$srchpath'> $LastPagem1 </a>";
    $paginate.= "<a href='$targetpage?page=$lastpage$srchpath'> $lastpage </a>";
   } else {    $paginate.= "<a href='$targetpage?page=1$srchpath'> 1 </a>";
    $paginate.= "<a href='$targetpage?page=2$srchpath'> 2 </a>";
    $paginate.= "...";
    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
     if ($counter == $page){      $paginate.= "<span class='current'> $counter </span>";
     }else{      $paginate.= "<a href='$targetpage?page=$counter$srchpath'> $counter </a>";
     }
    }
   }
  }
  // Next
  if ($page < $counter - 1){   $paginate.= "<a href='$targetpage?page=$next$srchpath'> " . $lang['NAV_NEXT'] . "</a>";
  }
  $paginate.= "</div>"; }
 //Search box
 echo '<div align="center">';
 echo "<form name=\"input\" action=\"$targetpage\" method=\"get\">";
 echo $lang['SEARCH_TXT'] . " <input type=\"text\" value=\"$srch\" name=\"searchterm\"> <input type=\"submit\" value=\"" . $lang['SEARCH_BUTTON'] . "\"><br><br>";
   // echo '<br />';
 echo '</form>';
 echo '</div>';
 if($total_pages > 0){           echo '<div id="paginate" align="center">' . $paginate . '</div>';
  //Results table
  echo "<center>";
  echo '<br><table class= "tablespec" border="1" cellspacing="0" cellpadding="5" width="90%">';
  echo " <tr>" . "";
  echo "   <th  width='3%'>".$lang['COL_NUM']."</th>";
  echo "   <th>" . $lang['COL_ARTIST'] . "</th><th>".$lang['COL_TITLE']."</th>";
  echo "   <th width='7%'>" . $lang['COL_YEAR'] . "</th>";
  echo "   <th width='7%'>" . $lang['COL_DURATION'] . "</th>";
  echo "   <th width='7%'>" . $lang['COL_REQ'] . "</th>";
  echo " </tr>" . "";

  $cnt = 1+($page_limit*$page)-$page_limit;
//Results counter
  //Add results to the table
     while($row = mysqli_fetch_assoc($result)) {
   echo " <tr>" . "";
   echo "  <td>$cnt.</td>";
   echo "  <td>" . 

$row['artist'] . "</td><td>".$row['title']."</td>";
           // echo "  <td>" . $row['year'] . "</td>";
            echo "  <td>" . convertTime($row['duration']) . "</td>";
            if(track_can_play($row['date_played'], $row['artist_played']) == true) {
                echo "  <td class=\"entry_no\"><a href=\"$targetpage?page=$page&requestid=" . $row['ID'] . "\" title=\"" . $lang['ALT_REQ'] . "\"><button>".$lang['REQ_SELECT']."</button></a></td>";
   }else{    echo "  <td class=\"entry_no\"><center><span style=\"background-color: #FF9966\"><span style=\"font-size: 8pt\">".$lang['NOREQ']."</span></span></center></td>";
}
   echo " </tr>";
   $cnt++;
  }
  @mysqli_free_result($conn, $result);
  echo "</center>";
?>
</table>
<br />

<?php
  //Add the bottom pagination
  echo '<div id="paginate" align="center">' . $paginate . '</div>'; }else{
  echo "<center><div class=\"errordiv\">" . $lang['MSG_NORESULTS'] . "</div></center>"; }
}
?>
           </td>
        </tr>
    </table>
</center>

<?php
}
?>
</body>
</html>
