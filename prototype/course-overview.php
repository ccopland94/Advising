<html>
<?php 
	include("sensitive.php");
?>
<head>
  <link rel="stylesheet" type="text/css" href="./CSS/foundation.css">
  <link rel="stylesheet" type="text/css" href="./CSS/foundation.min.css">
  <link rel="stylesheet" type="text/css" href="./CSS/global.css">
</head>
  <body>
    <div id="container" class="row">
      <div id="header"><span id="title">Honors Advising Portal</span>
      </div>
    </div>
	<div class="row" id="dropdown-select">
		<h3>Course Overview</h3>
		<div>
		  <table>
			<tr>
			  <th>Prefix</th>
			  <th>Course No.</th>
			  <th>Honors</th>
			  <th>CRN</th>
			</tr>
			<tr>
			  <td>
				<select>
				  <option>COSC</option>
				</select>
			  </td>
			  <td>
				<select>
				  <option>111</option>
				</select>
			  </td>
			  <td>
				<select>
				  <option>Both</option>
				</select>
			  </td>
			  <td>
				<select>
				  <option>-Select-</option>
				</select>
			  </td>
			</tr>
		  </table>
      </div>
      <div class="page row" id="course-overview">
        <span><b>Selected Courses</b></span>
        <table style="margin-top: 20px;">
          <tr>
            <th>Prefix</th>
            <th>Course No.</th>
            <th>CRN</th>
            <th>Honors</th>
            <th>Advised</th>
            <th>Registered</th>
            <th>Capacity</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>COSC</td>
            <td>111</td>
            <td>18792</td>
            <td>Yes</td>
            <td>4</td>
            <td>0</td>
            <td>20</td>
            <td><button onclick="window.location.href='section-details.php'">Details</button></td>
          </tr>
          <tr>
            <td>COSC</td>
            <td>111</td>
            <td>17293</td>
            <td>No</td>
            <td>2</td>
            <td>0</td>
            <td>35</td>
            <td><button>Details</button></td>
          </tr>
          <tr>
            <td>COSC</td>
            <td>111</td>
            <td>10293</td>
            <td>No</td>
            <td>3</td>
            <td>0</td>
            <td>35</td>
            <td><button>Details</button></td>
          </tr>
          <tr>
            <td>COSC</td>
            <td>111</td>
            <td>10293</td>
            <td>No</td>
            <td>1</td>
            <td>0</td>
            <td>35</td>
            <td><button>Details</button></td>
          </tr>
        </table>
      </div>
      <div style="margin-top: 10px;">
        <button onclick="window.location.href='home.php'">Home</button>
      </div>
    </div>

    <script>
    function fetch_courses() {
      $.ajax({
        method: "POST",
        url: "course-overview-funcs.php",
        data: {action: "get_course_prefixes"},
        success: function(output) {
          console.log(output);
        }
      });
    }
    </script>

  </body>
</html>