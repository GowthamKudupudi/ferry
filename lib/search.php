<?php
/* Author: Gowtham */
session_start();
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require 'inc.php';
//include 'db_login.php';
require "$root/lib/adminScripts/db_login.php";
$searchString = sqlinjection_free($_POST['searchString']);
$searchType = json_decode($_POST['searchType'], true);
if ($_SESSION['authenticated']) {
    if ($searchType['type'] != 'onlyDBSearch') {
        $query = "SELECT `uid` FROM `objectTable` WHERE `id`='" . $searchString . "'";
        $result = mysql_db_query('collegedb2admin', $query, $dbc);
        $error2 = mysql_error($dbc);
        $UID = mysql_result($result, 0, 'uid');
        if ($UID) {
            $query = "SELECT PID, username FROM `users` WHERE `index`='" . $UID . "'";
            $result = mysql_db_query('collegedb2admin', $query, $dbc);
            $error3 = mysql_error();
            $PID = mysql_result($result, 0, 'PID');
            $username = mysql_result($result, 0, 'username');
            if ($PID) {
                $query = "SELECT * FROM user_profiles WHERE `index`='" . $PID . "'";
                $result = mysql_db_query('collegedb2admin', $query, $dbc);
                $error4 = mysql_error($dbc);
                $full_name = mysql_result($result, 0, "full_name");
                $gaurdian_id = mysql_result($result, 0, "gaurdian_id");
                $sex = mysql_result($result, 0, 'sex');
                $dob = mysql_result($result, 0, 'DOB');
                $p_address = mysql_result($result, 0, 'permenent_address');
                $tel1 = mysql_result($result, 0, 'telephone_no1');
                $tel2 = mysql_result($result, 0, 'telephone_no2');
                $email_id = mysql_result($result, 0, 'email_id');
                $year = $dob[0] . $dob[1] . $dob[2] . $dob[3];
                $month = $dob[5] . $dob[6];
                $day = $dob[8] . $dob[9];
                $photoId = mysql_result($result, 0, 'photo_id');
            }
        }
    }
    $mRows = array();
    $stc = 0;
    if ($searchType['type'] != 'onlyDBSearch') {
        $query = "SHOW TABLES";
        $trs = mysql_db_query("collegedb2", $query, $dbc);
        $tc = mysql_num_rows($trs);
        for ($i = 0; $i < $tc and $stc <= 10; $i++) {
            $fr = mysql_fetch_row($trs);
            $tna[] = $fr[0];
        }
        $_SESSION['search']['tables'] = $tna;
    }
    $sti = 0;
    if ($searchType['type'] == 'onlyDBSearch') {
        if ($searchType['options'] == 'next') {
            $sti = $_SESSION['search']['sti'];
        }
    }
    for ($i = $sti; $i < $tc and $stc <= 10; $i++) {
        $tn = $tna[$i];
        $query = "SHOW TABLE STATUS LIKE  '" . $tn . "'";
        $result = mysql_db_query("collegedb2", $query, $dbc);
        $com = mysql_result($result, '0', 'Comment');
        if ($com) {
            $com = explode(",", $com);
            $sm = false;
            $own = false;
            foreach ($com as $key => $value) {
                $al = explode(":", $value);
                if ($al[0] == 'al') {
                    $al = trim($al[1]);
                    $sm = superMaster($_SESSION['adminLevel'], $al);
                } else if ($al[0] == 'o') {
                    $al = trim($al[1]);
                    $own = authObject($al);
                }
            }
            if ($own or $sm or authorizeTransit($_SESSION['adminLevel'],"Zz0")) {
                $query = "SHOW FULL COLUMNS FROM `" . $tn . "`";
                $tResult = mysql_db_query("collegedb2", $query, $dbc);
                $cc = mysql_num_rows($tResult);
                for ($j = 0; $j < $cc; $j++) {
                    $Field[$j] = mysql_result($tResult, $j, 'Field');
                    $query = "SELECT * FROM `" . $tn . "` WHERE `" . $Field[$j] . "` LIKE '" . $searchString . "'";
                    $sResult = mysql_query($query, $dbc);
                    $sRCount = mysql_num_rows($sResult);
                    if ($sRCount > 0) {
                        $mRows[$tn] = array();
                        $rRow = array();
                        for ($k = 0; $k < $sRCount; $k++) {
                            for ($c = 0; $c < $cc; $c++) {
                                $cn = mysql_result($tResult, $c, "Field");
                                $rRow[$cn] = mysql_result($sResult, $k, $cn);
                            }
                            $mRows[$tn][] = $rRow;
                        }
                        $stc++;
                    }
                }
            }
        }
    }
    if ($i >= $tc) {
        $mRows[] = 'END';
    } else {
        $mRows[] = 'NEXT';
    }
    $_SESSION['search']['sti'] = $i;
    if ($searchType['type'] == 'onlyDBSearch') {
        echo json_encode($mRows);
        die();
    }
}
?>
<head>
    <title>searchForm</title>
</head>
<body>

    <div class="userSpecific gdgBody" id="searchPage">
        
        <?php if ($PID)
            include 'userProfileTemplate.php'; ?>
        <div id="dbSearchContent">
            <h3>DB Search Content</h3>
            <div id="sdata"><?php echo json_encode($mRows) ?></div>
        </div>
        <script id="searchPageScript" type="text/javascript" src="/lib/search.js" onload="
            search.body=this.parentElement;
            search.init();
        "></script>
    </div>
</body>
