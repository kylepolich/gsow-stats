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
  $conn = mysqli_connect($host, $user, $password, "gsow", $port);
	$conn->set_charset("utf8");
  $msg = "";
  if (isset($_POST['edit_id'])) {
    $q = "DELETE FROM edits where edit_id = " . $_POST['edit_id'] . " and lang='" . $_POST['lang'] . "'";
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
    $tagq = "JOIN tags t99 on t1.pageid = t99.pageid and t99.tag='" . $tag . "' ";
  }
  $q = "
    SELECT t1.edit_id, t1.page, t1.lang, t1.start
    , t2.*
    , coalesce(t3.c, 0) as tags 
    FROM edits t1 
    LEFT JOIN (
      SELECT pageid, project, min(dt) as min_dt, max(dt) as max_dt, sum(views) as views
      , SUM(CASE WHEN dt > DATE_SUB(NOW(), INTERVAl 30 day) THEN views ELSE 0 END) as last_30 
      , SUM(CASE WHEN dt > DATE_SUB(NOW(), INTERVAl 1 day) THEN views ELSE 0 END) as last_7 
      , SUM(CASE WHEN dt > DATE_SUB(NOW(), INTERVAl 1 day) THEN views ELSE 0 END) as last_1 
      FROM page_views
      WHERE dt > DATE_SUB(NOW(), INTERVAl 30 day)
      GROUP BY pageid, project
    ) t2
    on  t1.pageid = t2.pageid 
    and t1.lang   = t2.project
    left join (select pageid, count(*) as c from tags group by pageid ) t3
    on t1.pageid = t3.pageid 
    " . $tagq . " 
    GROUP BY t1.edit_id, t1.page, t1.lang, t1.start, t1.pageid ORDER BY t1.page
";
  $result = mysqli_query($conn, $q);
  $rows = array();
  $tot = 0;
  $tot_30 = 0;
  $tot_7 = 0;
  $tot_1 = 0;
  $otag = $tag;
  while ($row = mysqli_fetch_array($result)) {
    $tot = $tot + $row['views'];
    $tot_30 = $tot_30 + $row['last_30'];
    $tot_7 = $tot_7 + $row['last_7'];
    $tot_1 = $tot_1 + $row['last_1'];
    array_push($rows, $row);
  }

  $q = "SELECT dt, sum(views) as views FROM page_views WHERE dt > DATE_SUB(NOW(), INTERVAl 365 day) GROUP BY dt ORDER BY dt";
  $dts = array();
  $views = array();
  array_push($dts, "Date");
  array_push($views, "Views");
  $result = mysqli_query($conn, $q);
  while ($row = mysqli_fetch_array($result)) {
    array_push($dts, $row['dt']);
    array_push($views, $row['views']);
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
      <td>All time total:</td>
      <td align='right'><?php echo(number_format($tot)); ?></td>
    </tr>
    <tr>
      <td>Total last 30 days:</td>
      <td align='right'><?php echo(number_format($tot_30)); ?></td>
    </tr>
    <tr>
      <td>Total last 1 days:</td>
      <td align='right'><?php echo(number_format($tot_1)); ?></td>
    </tr>
  </table>

  <div id="views_timeseries">


  <center>
  <form action='index.php' method='get' style='display: inline'>
    Keyword: <input name='tag' value='<?php echo($tag); ?>' />
    <input type='submit' value='Search' />
  </form>
  <br/><br/>
  <div style='width: 100%;'>
  <?php
    $q = "select t1.tag, ifnull(t2.tag_group, '') as tag_group, count(distinct t1.pageid) as c
          from tags t1
          left join tag_group t2
           on t1.tag = t2.tag
          group by t1.tag, t2.tag_group
          order by t2.tag_group, t1.tag;";
    $result = mysqli_query($conn, $q);
    echo("<table><tr><td><div class='tcell'>");
    $last_tg = "___zzz___";
    while ($row = mysqli_fetch_array($result)) {
      $tag = $row['tag'];
      $tg = $row['tag_group'];
      if (strcmp($tg, $last_tg) != 0) {
        echo("</div></td><td valign='top'><div class='tcell'><b>" . $tg . "</b><br/>");
      }
      $last_tg = $tg;
      $c = $row['c'];
      echo("<a href='index.php?tag=" . $tag . "'><nobr>" . $tag . " (" . $c . ")</nobr></a><br />");
    }
    echo("</div></td></tr></table>");
  ?>
  </div>
  </center>

  <hr/>

  <table id="myTable" class="tablesorter">
    <thead>
      <tr>
        <th>Lang</th>
        <th>Page</th>
        <th data-metric-name='cn'>Total Views</th>
        <th data-metric-name='cn'>Last 30 days</th>
        <th data-metric-name='cn'>Last 7 days</th>
        <th>First edit</th>
        <th>Last updated</th>
        <th> </th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach ($rows as $row) {
    echo("<tr>");
    echo("<td>" . $row['lang'] . "</td>");
    if ($row["pageid"] != null) {
      echo("<td><a href='/gsow/page.php?pageid=" . $row["pageid"] . "'>" . $row["page"] . " (" . $row["tags"] . ")" . "</a></td>");
    } else {
      echo("<td>" . $row["page"] . "(" . $row["tags"] . ")" . " (ERROR: not found)</td>");
    }
    ?>
    <td><?php echo(number_format($row["views"])); ?></td>
    <td><?php echo(number_format($row["last_30"])); ?></td>
    <td><?php echo(number_format($row["last_7"])); ?></td>
    <td><?php echo(substr($row["start"], 0, 10)); ?></td>
    <td><?php echo($row["max_dt"]); ?></td>
    <td>
      <form action='index.php' method=post style='display: inline'>
        <input type='hidden' name='tag' value='<?php echo($otag); ?>' />
        <input type='hidden' name='lang' value='<?php echo($row['lang']); ?>' />
        <input type='hidden' name='edit_id' value='<?php echo($row['edit_id']); ?>' />
        <input type='submit' value='delete' />
      </form>
      <form action='admin.php' style='display: inline'>
        <input type='hidden' name='tag' value='<?php echo($otag); ?>' />
        <input type='hidden' name='lang' value='<?php echo($row['lang']); ?>' />
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

  bb.generate({
      bindto: "#views_timeseries",
      data: {
          x: 'Date',
          columns: [ <?php echo(json_encode($dts)); ?>, <?php echo(json_encode($views)); ?> ]
      },
      axis: { x: { type: 'timeseries', tick: { rotate: 90, format: '%Y-%m-%d' } } }
  });
});


</script>
<?php
  include("footer.php");
?>
