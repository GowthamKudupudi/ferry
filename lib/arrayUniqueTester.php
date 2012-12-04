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
        $a = array("0", "1", "2", "3");
        unset($a[2]);
        var_dump($a);
        var_dump(array_unique($a));
        ?>
    </body>
</html>
