<?php
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
  include("header.php");
  if (isset($_POST['edit_id'])) {
    $q = "DELETE FROM edits where edit_id = " . $_POST['edit_id'];
    $result = mysqli_query($conn, $q);
  }
  $q = "SELECT t1.edit_id, t1.page, t1.start, t1.pageid, sum(t3.views) as views, min(t3.dt) as min_dt, max(t3.dt) as max_dt " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 30 day) AND NOW() THEN t3.views ELSE 0 END) as last_30 " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 7 day) AND NOW() THEN t3.views ELSE 0 END) as last_7 " .
       "FROM edits t1 " .
       "LEFT JOIN page_views t3 " .
       " on t1.pageid=t3.pageid " .
       " and t1.start <= t3.dt " .
       " and t3.project='en' " .
       "GROUP BY t1.edit_id, t1.page, t1.start, t1.pageid";
  $result = mysqli_query($conn, $q);
  $rows = array();
  $tot = 0;
  $tot_30 = 0;
  $tot_7 = 0;
  while ($row = mysqli_fetch_array($result)) {
    $tot = $tot + $row['views'];
    $tot_30 = $tot_30 + $row['last_30'];
    $tot_7 = $tot_7 + $row['last_7'];
    array_push($rows, $row);
  }
?>
  <table>
    <tr>
      <td>Total:</td>
      <td><? echo(number_format($tot)); ?></td>
    </tr>
    <tr>
      <td>Total last 30 days:</td>
      <td><? echo(number_format($tot_30)); ?></td>
    </tr>
    <tr>
      <td>Total last 7 days:</td>
      <td><? echo(number_format($tot_7)); ?></td>
    </tr>
  </table>
  
  <table id="myTable" class="tablesorter">
    <thead>
      <tr>
        <th>Page</th>
        <th>First edit</th>
        <th>Page Views from</th>
        <th>To</th>
        <th>Total Views</th>
        <th>Last 30 days</th>
        <th>Last 7 days</th>
        <th> </th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach ($rows as $row) {
    echo("<tr>");
    echo("<td><a href='/gsow/page.php?pageid=" . $row["pageid"] . "'>" . $row["page"] . "</td>");
    echo("<td>" . $row["start"] . "</td>");
    echo("<td>" . $row["min_dt"] . "</td>");
    echo("<td>" . $row["max_dt"] . "</td>");
    echo("<td>" . number_format($row["views"]) . "</td>");
    echo("<td>" . number_format($row["last_30"]) . "</td>");
    echo("<td>" . number_format($row["last_7"]) . "</td>");
    echo("<td><form action='index.php' method=post><input type='hidden' name='edit_id' value='" . $row['edit_id'] . "' /><input type='submit' value='delete' /></form></td>");
    echo("</tr>");
  }
?>
    </tbody>
  </table>
<script type="text/javascript">
$(document).ready(function() {
	$("#myTable").tablesorter();
});
</script>
<?php
  include("footer.php");
?>
