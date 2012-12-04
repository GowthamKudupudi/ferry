<?php
function validate_username($field) {
    if ($field == "NULL")
        return "No Username was entered<br />";
    //else if (strlen($field) < 8)
    //    return "Usernames must be at least 8 characters<br />";
    else if (preg_match("/[^a-zA-Z0-9_-.]/", $field))
        return "Only letters, numbers, - and _ in usernames<br />";
    return "";
}

function validate_password($field) {
    if ($field == "NULL")
        return "No Password was entered<br />";
    else if (!strlen($field) == 32)
        return "Passwords must be MD5<br />"; 
    return "";
}

function validate_age($field) {
    if ($field == "")
        return "No Age was entered<br />";
    else if ($field < 18 || $field > 110)
        return "Age must be between 18 and 110<br />";
    return "";
}

function validate_email_id($field) {
    if ($field == "NULL")
        return "";
    else if (!preg_match("/[a-zA-Z0-9_.]+@[a-zA-Z0-9.]+.com/", $field))
        return "The Email address is invalid<br />";
    return "";
}

function validate_full_name($field) {
    if ($field == "NULL")
        return "No Full Name was entered<br />";
    return "";
}

function validate_gaurdian_id($field) {
    if ($field == "NULL")
        return "No Gaurdian ID was entered<br />";
    else if (!preg_match("/[\d]{1,32}/", $field))
        return "Gaurdian ID should b of 1 to 32 numerals<br/>";
    return "";
}

function validate_dob($field) {
    if ($field == "NULL")
        return "No DOB entered</br>";
    else if (!preg_match("/[\d]{4}-[\d]{1,2}-[\d]{1,2}/", $field))
        return "incorrect date format<br/>";
    return "";
}

function validate_p_address($field) {
    if (strlen($field) > 200)
        return "address length exceeded 200 chars";
    return "";
}

function validate_tel($field) {
    if ($field=="NULL") {
        return "";
    }
    else if (!preg_match("/[0-9]{10,14}/", $field))
        return "phone no. should b of 10 to 14 numerals";
    return "";
}

function validate_form($username, $password, $full_name, $nickName, $gaurdian_id, $dob, $p_address, $tel1, $tel2, $email_id) {
    return validate_username($username) . validate_password($password) . validate_full_name($full_name) . validate_full_name($nickName). validate_gaurdian_id($gaurdian_id) . validate_dob($dob) . validate_tel($tel1) . validate_tel($tel2) . validate_email_id($email_id);
}
?>