<?php
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
  include("header.php");
  if (isset($_POST['edit_id'])) {
    $q = "DELETE FROM edits where edit_id = " . $_POST['edit_id'];
    $result = mysqli_query($conn, $q);
  }
  $tag = "";
  $tagq = "";
  if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];
  }
  if (isset($_POST['tag'])) {
    $tag = $_POST['tag'];
  }
  if ($tag != "") {
    $tagq = "JOIN tags t4 on t1.pageid = t4.pageid and t4.tag='" . $tag . "' ";
  }
  $q = "SELECT t1.edit_id, t1.page, t1.start, t1.pageid, sum(t3.views) as views, min(t3.dt) as min_dt, max(t3.dt) as max_dt " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 30 day) AND NOW() THEN t3.views ELSE 0 END) as last_30 " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 7 day) AND NOW() THEN t3.views ELSE 0 END) as last_7 " .
       "FROM edits t1 " .
       "LEFT JOIN page_views t3 " .
       " on t1.pageid=t3.pageid " .
       " and t1.start <= t3.dt " .
       " and t3.project='en' " .
       $tagq .
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
      <td align='right'><? echo(number_format($tot)); ?></td>
    </tr>
    <tr>
      <td>Total last 30 days:</td>
      <td align='right'><? echo(number_format($tot_30)); ?></td>
    </tr>
    <tr>
      <td>Total last 7 days:</td>
      <td align='right'><? echo(number_format($tot_7)); ?></td>
    </tr>
  </table>

  <center>
  <form action='index.php' method='get' style='display: inline'>
    Keyword: <input name='tag' value='<? echo($tag); ?>' />
    <input type='submit' value='Search' />
  </form>
  </center>
  <br/>

  <table id="myTable" class="tablesorter">
    <thead>
      <tr>
        <th>Page</th>
        <th>First edit</th>
        <th>Page Views from</th>
        <th>To</th>
        <th data-metric-name='cn'>Total Views</th>
        <th data-metric-name='cn'>Last 30 days</th>
        <th data-metric-name='cn'>Last 7 days</th>
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
    echo("<td><form action='index.php' method=post style='display: inline'><input type='hidden' name='tag' value='" . $tag . "' /><input type='hidden' name='edit_id' value='" . $row['edit_id'] . "' /><input type='submit' value='delete' /></form>");
    echo("<form action='edit.php' style='display: inline'><input type='hidden' name='tag' value='" . $tag . "' /><input type='hidden' name='pageid' value='" . $row["pageid"] . "' /><input type='submit' value='edit' /></td>");
    echo("</tr>");
  }
?>
    </tbody>
  </table>
<script type="text/javascript">
$(document).ready(function() {
		$.tablesorter.addParser({
			// set a unique id
			id: 'cn',
			is: function(s) {
				return false;
			},
			format: function(s, table, cell, cellIndex) {
				return (cell.innerHTML.replace(',', ''));
			},
			// set type, either numeric or text
			type: 'numeric'
		});

	$("#myTable").tablesorter({headers : {4: {sorter: 'cn'}, 5: {sorter: 'cn'}, 6: {sorter: 'cn'} } });
});

</script>
<?php
  include("footer.php");
?>
