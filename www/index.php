<?php
/*
Other languages is second. 

Figuring out how to make the keywords more attractive on the page

Frozen Header
*/
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
  include("../config.php");
  $conn = mysqli_connect($host, $user, $password, "gsow");
  $msg = "";
  if (isset($_POST['edit_id'])) {
    $q = "DELETE FROM edits where edit_id = " . $_POST['edit_id'];
    $result = mysqli_query($conn, $q);
    //header( 'Location: index.php?msg=Delete+successful&tag=' + $_POST['tag'] ) ;
    //return;
    $msg="<h2>Delete successed</h2>";
  }
  include("header.php");
  $tag = "";
  $tagq = "";
  if (isset($_GET['tag'])) {
    $tag = trim($_GET['tag']);
  }
  if (isset($_POST['tag'])) {
    $tag = trim($_POST['tag']);
  }
  if ($tag != "") {
    $tagq = "JOIN tags t4 on t1.pageid = t4.pageid and t4.tag='" . $tag . "' ";
  }
  $q = "SELECT t1.edit_id, t1.page, t1.start, t1.pageid, sum(t3.views) as views, min(t3.dt) as min_dt, max(t3.dt) as max_dt " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 30 day) AND NOW() THEN t3.views ELSE 0 END) as last_30 " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 7 day) AND NOW() THEN t3.views ELSE 0 END) as last_7 " .
       " , coalesce(t4.c, 0) as tags " .
       "FROM edits t1 " .
       "LEFT JOIN page_views t3 " .
       " on t1.pageid=t3.pageid " .
       " and t1.start <= t3.dt " .
       " and t3.project='en' " .
       "left join (select pageid, count(*) as c from tags group by pageid ) t4 " .
       " on t1.pageid = t4.pageid " .
       $tagq .
       "GROUP BY t1.edit_id, t1.page, t1.start, t1.pageid ORDER BY t1.page";
  $result = mysqli_query($conn, $q);
  $rows = array();
  $tot = 0;
  $tot_30 = 0;
  $tot_7 = 0;
  $otag = $tag;
  while ($row = mysqli_fetch_array($result)) {
    $tot = $tot + $row['views'];
    $tot_30 = $tot_30 + $row['last_30'];
    $tot_7 = $tot_7 + $row['last_7'];
    array_push($rows, $row);
  }
  if (isset($_GET['msg']) && $_GET['msg'] != '') {
    $msg = $_GET['msg'];
  }
  if ($msg != "") {
    echo("<center>" . $msg . "</center>");
  }
?>
  <table>
    <tr>
      <td>Total:</td>
      <td align='right'><?php echo(number_format($tot)); ?></td>
    </tr>
    <tr>
      <td>Total last 30 days:</td>
      <td align='right'><?php echo(number_format($tot_30)); ?></td>
    </tr>
    <tr>
      <td>Total last 7 days:</td>
      <td align='right'><?php echo(number_format($tot_7)); ?></td>
    </tr>
  </table>

  <center>
  <form action='index.php' method='get' style='display: inline'>
    Keyword: <input name='tag' value='<?php echo($tag); ?>' />
    <input type='submit' value='Search' />
  </form>
  <br/><br/>

  <div style='width: 500px;'>
  <?php
    $q = "select tag, count(distinct pageid) as c from tags group by tag order by count(distinct pageid) desc;";
    $result = mysqli_query($conn, $q);
    $i=0;
    while ($row = mysqli_fetch_array($result)) {
      $tag = $row['tag'];
      $c = $row['c'];
      if ($i > 0) {
        echo(", ");
      }
      echo("<a href='index.php?tag=" . $tag . "'><nobr>" . $tag . " (" . $c . ")</nobr></a>");
      $i = $i + 1;
    }
  ?>
  </div>
  </center>

  <hr/>

  <table id="myTable" class="tablesorter">
    <thead>
      <tr>
        <th>Page</th>
        <th>Lang</th>
        <th>First edit</th>
        <th>Last updated</th>
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
    if ($row["pageid"] != null) {
      echo("<td><a href='/gsow/page.php?pageid=" . $row["pageid"] . "'>" . $row["page"] . " (" . $row["tags"] . ")" . "</a></td>");
    } else {
      echo("<td>" . $row["page"] . "(" . $row["tags"] . ")" . " (ERROR: not found)</td>");
    }
    echo("<td>en</td>");
    ?>
    <td><?php echo($row["start"]); ?></td>
    <td><?php echo($row["max_dt"]); ?></td>
    <td><?php echo(number_format($row["views"])); ?></td>
    <td><?php echo(number_format($row["last_30"])); ?></td>
    <td><?php echo(number_format($row["last_7"])); ?></td>
    <td>
      <form action='index.php' method=post style='display: inline'>
        <input type='hidden' name='tag' value='<?php echo($otag); ?>' />
        <input type='hidden' name='edit_id' value='<?php echo($row['edit_id']); ?>' />
        <input type='submit' value='delete' />
      </form>
      <form action='admin.php' style='display: inline'>
        <input type='hidden' name='tag' value='<?php echo($otag); ?>' />
        <input type='hidden' name='pageid' value='<?php echo($row["pageid"]); ?>' />
        <input type='hidden' name='page' value='<?php echo($row["page"]); ?>' />
        <input type='hidden' name='dt' value='<?php echo($row["start"]); ?>' />
        <input type='submit' value='edit' />
      </form>
    </td>
    <?php
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

	$("#myTable").tablesorter({headers : {4: {sorter: 'cn'}, 5: {sorter: 'cn'}, 3: {sorter: 'cn'} } });
});

</script>
<?php
  include("footer.php");
?>
