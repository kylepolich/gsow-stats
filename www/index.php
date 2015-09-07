<?php
  include("header.php");
  $q = "SELECT t1.edit_id, t1.page, t1.start, t1.pageid, sum(t3.views) as views, min(t3.dt) as min_dt, max(t3.dt) as max_dt " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 30 day) AND NOW() THEN t3.views ELSE 0 END) as last_30 " .
       " , SUM(CASE WHEN t3.dt BETWEEN DATE_SUB(NOW(), INTERVAl 7 day) AND NOW() THEN t3.views ELSE 0 END) as last_7 " .
       "FROM edits t1 " .
       "JOIN page_views t3 " .
       " on t1.pageid=t3.pageid " .
       "WHERE t1.start <= t3.dt " .
       "AND t3.project='en' " .
       "GROUP BY t1.edit_id, t1.page, t1.start, t1.pageid";
  $result = mysqli_query($conn, $q);
  //echo($q);
  echo("<table border=1><thead><tr><th>Page</th><th>First edit</th><th>Page Views from</th><th>To</th><th>Total Views</th><th>Last 30 days</th><th>Last 7 days</th></tr></thead><tbody>");
  while ($row = mysqli_fetch_array($result)) {
    echo("<tr>");
    echo("<td><a href='/gsow/page.php?pageid=" . $row["pageid"] . "'>" . $row["page"] . "</td>");
    echo("<td>" . $row["start"] . "</td>");
    echo("<td>" . $row["min_dt"] . "</td>");
    echo("<td>" . $row["max_dt"] . "</td>");
    echo("<td>" . number_format($row["views"]) . "</td>");
    echo("<td>" . number_format($row["last_30"]) . "</td>");
    echo("<td>" . number_format($row["last_7"]) . "</td>");
    echo("</tr>");
  }
  echo("</tbody></table>");
  include("footer.php");
?>
