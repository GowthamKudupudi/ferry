<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $s = $_POST['string'];
        var_dump(utf8_encode($s));
        echo "<br>";
        var_dump(utf8_decode($s));
        echo "<br>";
        $s = json_decode($s, true);
        $s = json_encode($s);
        var_dump(utf8_encode($s));
        echo "<br>";
        var_dump(utf8_decode($s));
        echo "<br>";
        $s = json_decode($s, true);
        var_dump($s);
        ?>
        <form method="POST" action="/lib/jsondecoder.php">
            <input id="string" name="string" type="text" size="70"/>
            <input type="submit"/>
        </form>
    </body>
</html>
