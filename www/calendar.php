<? include("header.php"); ?>

<div id='jqxCalendar'></div>

<script>
$("#jqxCalendar").jqxCalendar({
    width: '200px',
    height: '200px',
    theme: 'energyblue'
});
$('#jqxCalendar ').jqxCalendar('setDate', new Date(2014, 0, 1));
</script>


<? include("footer.php"); ?>

