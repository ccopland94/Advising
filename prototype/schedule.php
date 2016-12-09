<?php session_start();

include("sensitive.php");

// Check connection
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = array();
for($i=0; $i<count($_POST['prefix']); $i++)
{
  $q_temp = "SELECT *
          FROM COURSE
          WHERE ";
  $query_array = array();
  if($_POST['prefix'][$i] != "")
  {
    $query_array[] = "coursePrefix='".$_POST['prefix'][$i]."'";
  }
  if($_POST['courseNo'][$i] != "")
  {
    $query_array[] = "courseNO='".$_POST['courseNo'][$i]."'";
  }
  if($_POST['honors'][$i] != "")
  {
    $query_array[] = "isHonors=".$_POST['honors'][$i];
  }
  if($_POST['crn'][$i] != "")
  {
    $query_array[] = "CRN='".$_POST['crn'][$i]."'";
  }
  if($_POST['days'][$i] != "")
  {
    $query_array[] = "days='".$_POST['days'][$i]."'";
  }

  $conditions = implode(" AND ", $query_array);
  $q_temp = $q_temp.$conditions;
  $query[] = $q_temp;
}

$classes = array();
$courseCount = 0;
foreach($query as $key=>$sql)
{
  $result = mysqli_query($conn, $sql);
  $courses = array();
  while($row = mysqli_fetch_assoc($result))
  {
    $courses[] = $row;
  }
  $classes[] = $courses;
}

$schedules2 = array();

function decideFit($courseArr, $stackArr) {
  foreach($stackArr as $key=>$stackCourse) {
    $courseDays = str_split($courseArr["days"]);
    $stackDays = str_split($stackCourse["days"]);
    if(!empty(array_intersect($stackDays, $courseDays)) && $stackCourse['days'] != "")
    {
      if(strtotime($courseArr["timeStart"]) >= strtotime($stackCourse["timeStart"]) && strtotime($courseArr["timeStart"]) <= strtotime($stackCourse["timeEnd"]))
      {
        return false;
      }
      if(strtotime($courseArr["timeEnd"]) >= strtotime($stackCourse["timeStart"]) && strtotime($courseArr["timeEnd"]) <= strtotime($stackCourse["timeEnd"]))
      {
        return false;
      }
    }
  }
  return true;
}

function makeSchedule($length, $tempArray = array())
{
  //base case
  if($length == count($GLOBALS['classes'])-1)
  {
    for($j = 0; $j < count($GLOBALS['classes'][$length]); $j++)
    {
      if($GLOBALS['courseCount'] < 500)
      {
        if(decideFit($GLOBALS['classes'][$length][$j], $tempArray))
        {
            $tempArray[] = $GLOBALS['classes'][$length][$j];
            $GLOBALS['schedules2'][] = $tempArray;
            array_pop($tempArray);
            $GLOBALS['courseCount']++;
        }
      } else {
        break;
      }
    }
  //recursive case
  } else {
    for($i = 0; $i < count($GLOBALS['classes'][$length]); $i++)
    {
      if($GLOBALS['courseCount'] < 500)
      {
        if(decideFit($GLOBALS['classes'][$length][$i], $tempArray))
        {
            $tempArray[] = $GLOBALS['classes'][$length][$i];
            makeSchedule($length+1, $tempArray);
            array_pop($tempArray);
        }
      } else {
        break;
      }
    }
  }
}

//call the recursive function to schedule
makeSchedule(0);
?>

<html>
<head>
  <link rel="stylesheet" type="text/css" href="./CSS/global.css">
  <script src="./JS/jquery-3.1.1.min.js"></script>
</head>
  <body>
    <div id="container">
      <div id="header"><span id="title">Honors Advising Portal</span>
      </div>
    </div>
    <div class="page">
      <span><b>Student Schedule for <?=$_SESSION['student']['fname']?></b></span>
      <table id="classList">
        <tr>
          <th>Prefix</th>
          <th>Course No.</th>
          <th>Honors</th>
          <th>CRN</th>
          <th>Days</th>
          <th>Start</th>
          <th>End</th>
          <th>Credits</th>
        </tr>
        <?php
          for($i=0; $i<count($schedules2[0]); $i++)
          { ?>
            <tr>
              <td><?=$schedules2[0][$i]['coursePrefix']?></td>
              <td><?=$schedules2[0][$i]['courseNO']?></td>
              <td><?=$schedules2[0][$i]['isHonors']?></td>
              <td><?=$schedules2[0][$i]['CRN']?></td>
              <td><?=$schedules2[0][$i]['days']?></td>
              <td><?=$schedules2[0][$i]['timeStart']?></td>
              <td><?=$schedules2[0][$i]['timeEnd']?></td>
              <td><?=$schedules2[0][$i]['credits']?></td>
            </tr>
          <?php
            } ?>
        <tr>
          <td colspan="7" style="text-align: right; border: none;">Total Credits:</td>
          <td style="text-align: left; border: none;"></td>
        </tr>
      </table>
    </div>
    <div>
      <div style="margin-top: 10px;">
        <button onclick="submit(course);">Choose Schedule and Submit</button>
      </div>
      <div style="width: 50%; margin: auto; padding-top: 20px;">
        <button style="float: left;" onclick="byFive(-1);">Backward Five</button>
        <button style="float: left;" onclick="byOne(-1)">Previous</button>
        <span style="float: center;" id="total">Showing Schedule 1 of <?=count($schedules2)?></span>
        <button style="float: right;" onclick="byFive(1);">Forward Five</button>
        <button style="float: right;" onclick="byOne(1)">Next</button>
      </div>
    </div>
  </body>
  <script>
    var course = 0;
    var classArr = <?=json_encode($schedules2)?>;
    $(document).ready(function() {
      if(classArr.length > 0)
        selectCourses(0);
    });

    function submit(courseIndex)
    {
      var scheduleChosen = classArr[courseIndex];
      //call AJAX to submit. if it works, take to confirmation. else, stay here and alert error.
      var jsonString = JSON.stringify(scheduleChosen);
      console.log(jsonString);
      $.ajax({
        method: "POST",
        url: "schedule_funcs.php",
        data: {action: "schedule", array_str: jsonString},
        success: function(output) {
          console.log(output);
        }
      });
      //window.location.href='complete.php'
    }

    function byOne(sign)
    {
      if((sign>0 && course + 1 < classArr.length) || (sign < 0 && course - 1 >= 0))
      {
        course = course + sign;
        selectCourses(course);
      }
    }
    function byFive(sign)
    {
      if((sign>0 && course + 5 < classArr.length) || (sign < 0 && course - 5 >= 0))
      {
        course = course + 5*sign;
        selectCourses(course);
      }
    }
    function selectCourses(index)
    {
      if(index < classArr.length && index >= 0)
      {
        var replaceStr = "<tr>"+
          "<th>Prefix</th>"+
          "<th>Course No.</th>"+
          "<th>Honors</th>"+
          "<th>CRN</th>"+
          "<th>Days</th>"+
          "<th>Start</th>"+
          "<th>End</th>"+
          "<th>Credits</th>"+
        "</tr>";
        var creditSum = 0;
        for(i=0; i<classArr[index].length; i++)
        {
          var honorsStr = "";
          if(classArr[index][i]['isHonors'] == 1)
          {
            honorsStr = "Yes";
          } else {
            honorsStr = "No";
          }
            replaceStr += "<tr>"+
            "<td>"+classArr[index][i]['coursePrefix']+"</td>"+
            "<td>"+classArr[index][i]['courseNO']+"</td>"+
            "<td>"+honorsStr+"</td>"+
            "<td>"+classArr[index][i]['CRN']+"</td>"+
            "<td>"+classArr[index][i]['days']+"</td>"+
            "<td>"+classArr[index][i]['timeStart']+"</td>"+
            "<td>"+classArr[index][i]['timeEnd']+"</td>"+
            "<td>"+classArr[index][i]['credits']+"</td>"+
          "</tr>";

          creditSum += Number(classArr[index][i]['credits']);
        }

        replaceStr += "<tr>"+
          "<td colspan='7' style='text-align: right; border: none;'>Total Credits:</td>"+
          "<td style='text-align: left; border: none;'>"+creditSum+"</td>"+
        "</tr>";

        $("#classList").html(replaceStr);
        $("#total").html("Showing Schedule "+(course+1)+" of <?=count($schedules2)?>")
      }
    }
  </script>
</html>