<?php include("header.php"); ?>

Below are some top viewed pages.

<?php
  $query = "SELECT t1.title, sum(t2.views) as total" .
           ", sum(CASE WHEN t2.dt > NOW() - INTERVAL 3 DAY THEN t2.views ELSE 0 END) as last3 " .
           "FROM contributions t1 " .
           "JOIN page_views t2 " .
           " ON t1.pageid = t2.pageid " .
           "WHERE t2.dt > t1.ts " .
           "GROUP BY t1.title " .
           "ORDER BY sum(CASE WHEN t2.dt > NOW() - INTERVAL 3 DAY THEN t2.views ELSE 0 END) desc " .
           "LIMIT 20";
  $result = mysqli_query($conn, $query);
  echo("<table border=1>");
  echo("<tr><td>Page</td><td>Total Views since first edit</td><td>Total Views last 3 days</td></tr>");
  while ($row = mysqli_fetch_array($result)) {
    echo("<tr>");
    echo("<td>" . $row['title'] . "</td>");
    echo("<td>" . $row['total'] . "</td>");
    echo("<td>" . $row['last3'] . "</td>");
    echo("</tr>");
  }
  echo("</table>");
?>

<?php include("footer.php"); ?>
