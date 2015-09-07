<? include("header.php"); ?>

<?
  $query = "SELECT title, min(ts) as mindt, max(ts) as maxdt, count(distinct editor_id) as editors, sum(1) as edits " .
           "FROM contributions " .
           "WHERE pageid=" . $_GET['pageid'] . " " .
           "GROUP BY title";
  $result = mysqli_query($conn, $query);
  while ($r = mysqli_fetch_array($result)) {
    $row = $r;
  }
  $result = mysqli_query($conn, "SELECT t1.name, sum(1) as c FROM editor t1 JOIN contributions t2 on t1.editor_id = t2.editor_id WHERE t2.pageid=25867");
  $editors = array();
  while ($r = mysqli_fetch_array($result)) {
    $e = array("name" => $r['name'], "c" => $r['c']);
    array_push($editors, $e);
  }
  $result = mysqli_query($conn, "select sum(views) as total_views from page_views where pageid=" . $_GET['pageid'] . " and dt > (select min(ts) as ts from contributions where pageid=" . $_GET['pageid'] . ")");
  while ($r = mysqli_fetch_array($result)) {
    $pvs = $r['total_views'];
  }
?>

<h1><? echo($row['title']); ?></h1>
<a href="https://en.wikipedia.org/wiki/<? echo($row['title']); ?>">wiki page</a>

<table>
  <tr>
    <td>Total views since first GSoW edit:</td>
    <td><b><? echo($pvs); ?></b></td>
  </tr>
  <tr>
    <td>First GSoW edit:</td>
    <td><? echo($row['mindt']); ?></td>
  </tr>
  <tr>
    <td>Last GSoW edit:</td>
    <td><? echo($row['maxdt']); ?></td>
  </tr>
  <tr>
    <td>Touched by <? echo($row['editors']); ?> GSoW editors:</td>
    <td>
    <?
      foreach ($editors as $editor) {
        echo($editor['name'] . " (" . $editor['c'] . ")");
      }
    ?>
    </td>
  </tr>
</table>

<style>
.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
  font: 10px sans-serif;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}
</style>

<script>
var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y-%m-%d").parse;

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.views); });

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.tsv("page-data.php?pageid=<? echo($_GET['pageid']); ?>", function(error, data) {
  if (error) throw error;

  data.forEach(function(d) {
    d.date = parseDate(d.date);
    d.views = +d.views;
  });

  x.domain(d3.extent(data, function(d) { return d.date; }));
  y.domain(d3.extent(data, function(d) { return d.views; }));

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Page Views");

  svg.append("path")
      .datum(data)
      .attr("class", "line")
      .attr("d", line);
});

</script>

<?php include("footer.php"); ?>
