<body>
<?php
/* Author: Gowtham */
require 'inc.php';
$master=$_GET['master'];
$slave=$_GET['slave'];
$authorizeTransit=  superMaster($master, $slave);
echo "authorized: ".$authorizeTransit;
?>
    <form method="GET" action="./authorizeTransitTester.php">
        master: <input id="master" name="master" type="text"/><br/>
        slave: <input id="slave" name="slave" type="text"/><br/>
        <input type="submit" value="submit"/>
    </form>
</body>