<?php
  include("header.php");
  $conn = mysqli_connect($host, $user, $password, "gsow");
  $page = "";
  $dt = "YYYY-MM-DD";
  $act = "Add";
  $lang = "";
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $dt = substr($_GET['dt'], 0, 10);
    $lang = $_GET['lang'];
    $act = "Update";
  }
  if (isset($_POST['page'])) {
    $page = $_POST['page'];
    $lang = $_POST['lang'];
    if (!isset($_POST['dt'])) {
      echo("<h2>Sorry, you need to enter a start date</h2>");
    }
    else {
      if ($_POST['dt']=="YYYY-MM-DD") {
        echo("<h2>Please enter a valid date</h2>");
      }
      else {
        $dt = $_POST['dt'];
        $lang = $_POST['lang'];
        $page = str_replace("'", "\'", $page);
        $query = "UPDATE edits SET start='$dt' WHERE page='$page' and lang='$lang'";
        $result = mysqli_query($conn, $query);
        $rows = mysqli_affected_rows($conn);
        if ($rows == 1) {
          echo("<h2>Page updated</h2>");
        }
        else {
          $query = "INSERT INTO edits (page, lang, start) VALUES ('$page', '$lang', '$dt');";
          $result = mysqli_query($conn, $query);
          $rows = mysqli_affected_rows($conn);
          if ($rows != 1) {
            echo("<h2>Sorry, there was an error adding that page</h2>");
          }
          else {
            echo("<p>Page added successfully. Please allow a few minutes for the data to be downloaded.</p>");
          }
        }
      }
    }
    $lang = "";
  }
?>
<form action='admin.php' method=POST>
<h1><?php echo($act); ?> page</h1>
<table>
  <tr>
    <td>Page name:</td>
    <td><input name='page' value="<?php echo($page); ?>" /></td>
  </tr>
  <tr>
    <td>Language:</td>
    <td>
      <?php
        if ($lang != "") {
          echo($lang);
          echo("<input type='hidden' name='lang' value='$lang' />");
        } else {
      ?>
      <select name="lang">
        <option value="en">en - English</option>
        <option value="fr">fr - French</option>
        <option value="es">es - Spanish</option>
        <option value="de">de - German</option>
        <option value="it">it - Italian</option>
        <option value="pt">pt - Portugues</option>
        <option value="pl">pl - Polish</option>
        <option value="nl">nl - Dutch</option>
        <option value="hu">hu - Hungarian</option>
        <option value="ru">ru - Russian</option>
        <option value="fi">fi - Finnish</option>
        <option value="bg">bg - Bulgarian</option>
        <option value="sv">sv - Swedish</option>
        <option value="ar">ar - Arabic</option>
        <option value="cs">cs - Czech</option>
        <option value="ro">ro - Romanian</option>
        <option value="zh">zh - Chinese</option>
      </select>
      <?php } ?>
    </td>
  </tr>
  <tr>
    <td>First major edit date:</td>
    <td><input name='dt' value="<?php echo($dt); ?>" /></td>
  </tr>
  <tr>
    <td colspan=2 align=right><input type='submit' value='<?php echo($act); ?>' /></td>
  </tr>
</table>
</form>

<?php include("footer.php"); ?>
