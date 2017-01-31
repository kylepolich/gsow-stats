<?php
  include("../config.php");
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
?>
<!doctype html>

<html lang="en">
<head>
 <meta charset="utf-8">
 <title>GSoW Page View Tracker</title>

 <link rel="stylesheet" href="style.css">

 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.css" integrity="sha256-gl80aFE+bSTFw7UJf+ne/RkwC55cjidIp0Oe3AX5pfo=" crossorigin="anonymous" />
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.4/css/theme.bootstrap_3.min.css" integrity="sha256-cerl+DYHeG2ZhV/9iueb8E+s7rubli1gsnKuMbKDvho=" crossorigin="anonymous" />

 <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.4.3/d3.min.js" integrity="sha256-NppAh3cBS+VkCILD7lX/evuRWFgE+xEwtsjy9qUKHjk=" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.js" integrity="sha256-mlJR4DaLFvEBAgHcavz/vASrM6RrzcQq2lXDBwMixZU=" crossorigin="anonymous"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.4/js/jquery.tablesorter.min.js" integrity="sha256-etMCBAdNUB2TBSMUe3GISzr+drx6+BjwAt9T3qjO2xk=" crossorigin="anonymous"></script>

</head>
<body>

<h1>GSoW Page View Tracker</h1>

<a href="index.php">Home</a>
|
<a href="admin.php">Admin</a>
<!--
|
<a href="summary.php">Summary</a>
|
<a href="editors.php">Editors</a>
|
<a href="pages.php">Pages</a>
-->


<hr/>
