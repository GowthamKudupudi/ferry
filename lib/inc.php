<?php

/* Author: Gowtham */

//2012-03-21 11:50

function sqlinjection_free($istring) {
    if (get_magic_quotes_gpc())
	$istring = stripcslashes($istring);
    $istring = mysql_real_escape_string($istring);
    return $istring;
}

function mysql_entities_fix_string($string) {
    return htmlentities(sqlinjection_free($string));
}

function authenticated() {
    if (@$_SESSION['authenticated'])
	echo "true";
    else
	echo "false";
}

function authenticated_neg() {
    if ($_SESSION['authenticated'])
	echo "false";
    else
	echo "true";
}

function valid_mysql_query_data($istring) {
    $istring = "'" . sqlinjection_free($istring) . "'";
    if ($istring == "''") {
	$istring = "NULL";
    }
    return $istring;
}

function stringInsert($str, $istr, $pos) {
    $preStr = substr($str, 0, $pos);
    $posStr = substr($str, $pos, strlen($str) - $pos);
    return $preStr . $istr . $posStr;
}

function stringReplace($str, $rstr, $sPos, $ePos) {
    $preStr = substr($str, 0, $sPos);
    $posStr = substr($str, $ePos + 1);
    return $preStr . $rstr . $posStr;
}

function domesticSlave($master, $slave) {
    $i = 0;
    $j = 0;
    $jcount = 0;
    while ($i < strlen($slave) - 2) {
	if (preg_match('/[A-Z]/', $slave[$i]) and $slave[$i] != 'Z') {
	    $ik = FALSE;
	    while ($j < strlen($master) - 2) {
		if (preg_match('/[A-Z]/', $master[$j]) and preg_match('/[A-Z]/', $master[$j])) {
		    if ($master[$j] == $slave[$i]) {
			$i++;
			$j++;
			$k = 0;
			while (!preg_match('/[A-Z]/', $slave[$i]) and $i < strlen($slave) - 1) {
			    while (!preg_match('/[A-Z]/', $master[$j]) and $j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $slave[$i] == 'z' or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    $k++;
				    if ($master[$j] <= $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$k++;
					$i--;
				    }
				} else {
				    $j+=2;
				    $k+=2;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j-=$k;
			    $k = 0;
			}
		    } elseif ($master[$j] == 'Z' and preg_match('/[A-Z]/', $slave[$i]) and $jcount == 0) {
			$l = $j;
			$k = $i;
			$i = 1;
			$j++;
			while ($j < strlen($master) - 1 and !preg_match('/[A-Z]/', $master[$j])) {
			    while ($i < strlen($slave) - 1) {
				if ($slave[$i] == $master[$j] or $master[$j] == 'z' or $slave[$i] == 'z') {
				    $i++;
				    $j++;
				    if ($slave[$i] >= $master[$j]) {
					return TRUE;
				    } else {
					$i++;
					$j--;
				    }
				} else {
				    $i++;
				}
			    }
			    $j+=2;
			    $i = 0;
			}
			$j = $l + 2;
			$i = $k;
		    } elseif ($slave[$i] == 'Z' and preg_match('/[A-Z]/', $slave[$i])) {
			$l = $i;
			$k = $j;
			$i++;
			$j = 1;
			while ($i < strlen($slave) - 1 and !preg_match('/[A-Z]/', $slave[$i])) {
			    while ($j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $slave[$i] == 'z' or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    if ($master[$j] <= $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$i--;
				    }
				} else {
				    $j++;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j = 0;
			}
		    } else {
			$j++;
		    }
		} else {
		    $j++;
		}
	    }$jcount++;
	    if (!$ik) {
		$i+=2;
		$ik = FALSE;
	    }
	} elseif ($slave[$i] == 'Z') {
	    $l = $i;
	    $k = $j;
	    $i++;
	    $j = 1;
	    while ($i < strlen($slave) - 1 and !preg_match('/[A-Z]/', $slave[$i])) {
		while ($j < strlen($master) - 1) {
		    if ($master[$j] == $slave[$i] or $slave[$i] == 'z' or $master[$j] == 'z') {
			$i++;
			$j++;
			if ($master[$j] <= $slave[$i]) {
			    return TRUE;
			} else {
			    $j++;
			    $i--;
			}
		    } else {
			$j++;
		    }
		}
		$i+=2;
		$j = 0;
	    }
	} else {
	    $i++;
	    $j = 0;
	}
    }
    return FALSE;
}

function authorizeTransit($master, $slave) {
    $i = 0;
    $j = 0;
    $jcount = 0;
    while ($i < strlen($slave) - 2) {
	if (preg_match('/[A-Z]/', $slave[$i])) {
	    $ik = FALSE;
	    while ($j < strlen($master) - 2) {
		if (preg_match('/[A-Z]/', $master[$j]) and preg_match('/[A-Z]/', $master[$j])) {
		    if ($master[$j] == $slave[$i]) {
			$i++;
			$j++;
			$k = 0;
			while (!preg_match('/[A-Z]/', $slave[$i]) and $i < strlen($slave) - 1) {
			    while (!preg_match('/[A-Z]/', $master[$j]) and $j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    $k++;
				    if ($master[$j] <= $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$k++;
					$i--;
				    }
				} else {
				    $j+=2;
				    $k+=2;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j-=$k;
			    $k = 0;
			}
		    } elseif ($master[$j] == 'Z' and preg_match('/[A-Z]/', $slave[$i]) and $jcount == 0) {
			$l = $j;
			$k = $i;
			$i = 1;
			$j++;
			while ($j < strlen($master) - 1 and !preg_match('/[A-Z]/', $master[$j])) {
			    while ($i < strlen($slave) - 1) {
				if ($slave[$i] == $master[$j] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    if ($slave[$i] >= $master[$j]) {
					return TRUE;
				    } else {
					$i++;
					$j--;
				    }
				} else {
				    $i++;
				}
			    }
			    $j+=2;
			    $i = 0;
			}
			$j = $l + 2;
			$i = $k;
		    } else {
			$j++;
		    }
		} else {
		    $j++;
		}
	    }$jcount++;
	    if (!$ik) {
		$i+=2;
		$ik = FALSE;
	    }
	} else {
	    $i++;
	    $j = 0;
	}
    }
    return FALSE;
}

function windowedAccess($master, $slave) {
    $i = 0;
    $j = 0;
    $jcount = 0;
    while ($i < strlen($slave) - 2) {
	if (preg_match('/[A-Z]/', $slave[$i])) {
	    $ik = FALSE;
	    while ($j < strlen($master) - 2) {
		if (preg_match('/[A-Z]/', $master[$j]) and preg_match('/[A-Z]/', $master[$j])) {
		    if ($master[$j] == $slave[$i]) {
			$i++;
			$j++;
			$k = 0;
			while (!preg_match('/[A-Z]/', $slave[$i]) and $i < strlen($slave) - 1) {
			    while (!preg_match('/[A-Z]/', $master[$j]) and $j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    $k++;
				    if ($master[$j] == $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$k++;
					$i--;
				    }
				} else {
				    $j+=2;
				    $k+=2;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j-=$k;
			    $k = 0;
			}
		    } elseif ($master[$j] == 'Z' and preg_match('/[A-Z]/', $slave[$i]) and $jcount == 0) {
			$l = $j;
			$k = $i;
			$i = 1;
			$j++;
			while ($j < strlen($master) - 1 and !preg_match('/[A-Z]/', $master[$j])) {
			    while ($i < strlen($slave) - 1) {
				if ($slave[$i] == $master[$j] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    if ($master[$j] == $slave[$i]) {
					return TRUE;
				    } else {
					$i++;
					$j--;
				    }
				} else {
				    $i++;
				}
			    }
			    $j+=2;
			    $i = 0;
			}
			$j = $l + 2;
			$i = $k;
		    } else {
			$j++;
		    }
		} else {
		    $j++;
		}
	    }$jcount++;
	    if (!$ik) {
		$i+=2;
		$ik = FALSE;
	    }
	} else {
	    $i++;
	    $j = 0;
	}
    }
    return FALSE;
}

function superMaster($master, $slave) {
    $i = 0;
    $j = 0;
    $jcount = 0;
    while ($i < strlen($slave) - 2) {
	if (preg_match('/[A-Z]/', $slave[$i])) {
	    $ik = FALSE;
	    while ($j < strlen($master) - 2) {
		if (preg_match('/[A-Z]/', $master[$j]) and preg_match('/[A-Z]/', $master[$j])) {
		    if ($master[$j] == $slave[$i]) {
			$i++;
			$j++;
			$k = 0;
			while (!preg_match('/[A-Z]/', $slave[$i]) and $i < strlen($slave) - 1) {
			    while (!preg_match('/[A-Z]/', $master[$j]) and $j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    $k++;
				    if ($master[$j] < $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$k++;
					$i--;
				    }
				} else {
				    $j+=2;
				    $k+=2;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j-=$k;
			    $k = 0;
			}
		    } elseif ($master[$j] == 'Z' and preg_match('/[A-Z]/', $slave[$i]) and $jcount == 0) {
			$l = $j;
			$k = $i;
			$i = 1;
			$j++;
			while ($j < strlen($master) - 1 and !preg_match('/[A-Z]/', $master[$j])) {
			    while ($i < strlen($slave) - 1) {
				if ($slave[$i] == $master[$j] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    if ($master[$j] < $slave[$i]) {
					return TRUE;
				    } else {
					$i++;
					$j--;
				    }
				} else {
				    $i++;
				}
			    }
			    $j+=2;
			    $i = 0;
			}
			$j = $l + 2;
			$i = $k;
		    } else {
			$j++;
		    }
		} else {
		    $j++;
		}
	    }$jcount++;
	    if (!$ik) {
		$i+=2;
		$ik = FALSE;
	    }
	} else {
	    $i++;
	    $j = 0;
	}
    }
    return FALSE;
}

function anyDeptSlave($master, $slave) {
    $i = 0;
    $j = 0;
    $jcount = 0;
    while ($i < strlen($slave) - 2) {
	if (preg_match('/[A-Z]/', $slave[$i]) and $slave[$i] != 'Z') {
	    $ik = FALSE;
	    while ($j < strlen($master) - 2) {
		if (preg_match('/[A-Z]/', $master[$j]) and preg_match('/[A-Z]/', $master[$j])) {
		    if ($master[$j] == $slave[$i]) {
			$i++;
			$j++;
			$k = 0;
			while (!preg_match('/[A-Z]/', $slave[$i]) and $i < strlen($slave) - 1) {
			    while (!preg_match('/[A-Z]/', $master[$j]) and $j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    $k++;
				    if ($master[$j] <= $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$k++;
					$i--;
				    }
				} else {
				    $j+=2;
				    $k+=2;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j-=$k;
			    $k = 0;
			}
		    } elseif ($master[$j] == 'Z' and preg_match('/[A-Z]/', $slave[$i]) and $jcount == 0) {
			$l = $j;
			$k = $i;
			$i = 1;
			$j++;
			while ($j < strlen($master) - 1 and !preg_match('/[A-Z]/', $master[$j])) {
			    while ($i < strlen($slave) - 1) {
				if ($slave[$i] == $master[$j] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    if ($slave[$i] >= $master[$j]) {
					return TRUE;
				    } else {
					$i++;
					$j--;
				    }
				} else {
				    $i++;
				}
			    }
			    $j+=2;
			    $i = 0;
			}
			$j = $l + 2;
			$i = $k;
		    } elseif ($slave[$i] == 'Z' and preg_match('/[A-Z]/', $slave[$i])) {
			$l = $i;
			$k = $j;
			$i++;
			$j = 1;
			while ($i < strlen($slave) - 1 and !preg_match('/[A-Z]/', $slave[$i])) {
			    while ($j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    if ($master[$j] <= $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$i--;
				    }
				} else {
				    $j++;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j = 0;
			}
		    } else {
			$j++;
		    }
		} else {
		    $j++;
		}
	    }$jcount++;
	    if (!$ik) {
		$i+=2;
		$ik = FALSE;
	    }
	} elseif ($slave[$i] == 'Z') {
	    $l = $i;
	    $k = $j;
	    $i++;
	    $j = 1;
	    while ($i < strlen($slave) - 1 and !preg_match('/[A-Z]/', $slave[$i])) {
		while ($j < strlen($master) - 1) {
		    if ($master[$j] == $slave[$i] or $master[$j] == 'z') {
			$i++;
			$j++;
			if ($master[$j] <= $slave[$i]) {
			    return TRUE;
			} else {
			    $j++;
			    $i--;
			}
		    } else {
			$j++;
		    }
		}
		$i+=2;
		$j = 0;
	    }
	} else {
	    $i++;
	    $j = 0;
	}
    }
    return FALSE;
}

function anySubDeptSlave($master, $slave) {
    $i = 0;
    $j = 0;
    $jcount = 0;
    while ($i < strlen($slave) - 2) {
	if (preg_match('/[A-Z]/', $slave[$i])) {
	    $ik = FALSE;
	    while ($j < strlen($master) - 2) {
		if (preg_match('/[A-Z]/', $master[$j]) and preg_match('/[A-Z]/', $master[$j])) {
		    if ($master[$j] == $slave[$i]) {
			$i++;
			$j++;
			$k = 0;
			while (!preg_match('/[A-Z]/', $slave[$i]) and $i < strlen($slave) - 1) {
			    while (!preg_match('/[A-Z]/', $master[$j]) and $j < strlen($master) - 1) {
				if ($master[$j] == $slave[$i] or $slave[$i] == 'z' or $master[$j] == 'z') {
				    $i++;
				    $j++;
				    $k++;
				    if ($master[$j] <= $slave[$i]) {
					return TRUE;
				    } else {
					$j++;
					$k++;
					$i--;
				    }
				} else {
				    $j+=2;
				    $k+=2;
				}
			    }
			    $i+=2;
			    $ik = TRUE;
			    $j-=$k;
			    $k = 0;
			}
		    } elseif ($master[$j] == 'Z' and preg_match('/[A-Z]/', $slave[$i]) and $jcount == 0) {
			$l = $j;
			$k = $i;
			$i = 1;
			$j++;
			while ($j < strlen($master) - 1 and !preg_match('/[A-Z]/', $master[$j])) {
			    while ($i < strlen($slave) - 1) {
				if ($slave[$i] == $master[$j] or $master[$j] == 'z' or $slave[$i] == 'z') {
				    $i++;
				    $j++;
				    if ($slave[$i] >= $master[$j]) {
					return TRUE;
				    } else {
					$i++;
					$j--;
				    }
				} else {
				    $i++;
				}
			    }
			    $j+=2;
			    $i = 0;
			}
			$j = $l + 2;
			$i = $k;
		    } else {
			$j++;
		    }
		} else {
		    $j++;
		}
	    }$jcount++;
	    if (!$ik) {
		$i+=2;
		$ik = FALSE;
	    }
	} else {
	    $i++;
	    $j = 0;
	}
    }
    return FALSE;
}

function authField_dep($comment, $rORw) {
    $i = 0;
    $j = -1;
    $mems['r']['index'] = -1;
    $mems['w']['index'] = -1;
    $userMatched = false;
    while ($comment[$i] != NULL) {
	if ($comment[$i] == '{') {
	    $i++;
	    $j++;
	    $k = 0;
	    while ($comment[$i] != '}') {
		if ($comment[$i] == 'r') {
		    $pType = 'r';
		} elseif ($comment[$i] == 'w') {
		    $pType = 'w';
		}
		$i++;
		$mems[$pType]['index']++;
		while ($comment[$i] != ',') {
		    $mems[$pType]['aL'][$mems[$pType]['index']].=$comment[$i];
		    $i++;
		}
		$i++;
		while ($comment[$i] != ',') {
		    $mems[$pType]['gid'][$mems[$pType]['index']].=$comment[$i];
		    $i++;
		}
		$i++;
		if ($comment[$i] == '{') {
		    $i++;
		    $k = 0;
		    while ($comment[$i] != '}') {
			while ($comment[$i] != ',' and $comment[$i] != '}') {
			    $mems[$pType]['rows'][$mems[$pType]['index']][$k].=$comment[$i];
			    $i++;
			}
			if (preg_match('/-/', $mems[$pType]['rows'][$mems[$pType]['index']][$k])) {
			    $jk = 0;
			    $ll = '';
			    $ul = '';
			    while ($mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk] != '-') {
				$ll+=$mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk];
				$jk++;
			    }
			    $jk++;
			    while ($mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk] != null) {
				$ul+=$mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk];
				$jk++;
			    }
			    while ($ll != $ul) {
				$mems[$pType]['rows'][$mems[$pType]['index']][$k] = $ll;
				$ll++;
				$k++;
			    }
			    $mems[$pType]['rows'][$mems[$pType]['index']][$k] = $ll;
			}
			$i++;
			$k++;
		    }
		    $grMems = groupExe($mems[$pType]['gid'][$mems[$pType]['index']], $groups);
		    $groups[] = $mems[$pType]['gid'][$mems[$pType]['index']];
		    $mems[$pType]['users'][$mems[$pType]['index']] = $grMems['users'];
		    $mems[$pType]['objects'][$mems[$pType]['index']] = $grMems['objects'];
		    $users = $grMems['users'];
		    if (!$userMatched) {
			for ($l = 0; $l < count($users); $l++) {
			    if ($_SESSION['uid'] == $users[$l]) {
				$userMatched = true;
				if ($rORw == $pType) {
				    if (authorizeTransit($_SESSION['adminLevel'], $mems[$pType]['aL'][$mems[$pType]['index']])) {
					for ($m = 0; $m < count($mems[$pType]['rows'][$mems[$pType]['index']]); $m++) {
					    for ($ml = 0; $ml < count($authRows); $ml++) {
						if ($authRows[$ml] == $mems[$pType]['rows'][$mems[$pType]['index']][$m]) {
						    $rae = TRUE;
						}
					    }
					    if (!$rae) {
						$authRows[] = $mems[$pType]['rows'][$mems[$pType]['index']][$m];
					    }
					}
				    }
				    $mems[$pType]['authRows'] = $authRows;
				}
			    }
			}
		    }
		}
	    }
	} else {
	    $i++;
	}
    }

    return $mems;
}

function authField($comment) {
    $i = 0;
    $j = -1;
    $mems['r']['index'] = -1;
    $mems['w']['index'] = -1;
    $mems['r']['authRows'] = array();
    $mems['w']['authRows'] = array();
    $userMatched = false;
    while ($comment[$i] != NULL) {
	if ($comment[$i] == '{') {
	    $i++;
	    $j++;
	    $k = 0;
	    while ($comment[$i] != '}') {
		if ($comment[$i] == 'r') {
		    $pType = 'r';
		} elseif ($comment[$i] == 'w') {
		    $pType = 'w';
		}
		$i++;
		$mems[$pType]['index']++;
		while ($comment[$i] != ',') {
		    $mems[$pType]['gid'][$mems[$pType]['index']].=$comment[$i];
		    $i++;
		}
		$i++;
		if ($comment[$i] == '{') {
		    $i++;
		    $k = 0;
		    while ($comment[$i] != '}') {
			while ($comment[$i] != ',' and $comment[$i] != '}') {
			    $mems[$pType]['rows'][$mems[$pType]['index']][$k].=$comment[$i];
			    $i++;
			}
			if (preg_match('/-/', $mems[$pType]['rows'][$mems[$pType]['index']][$k])) {
			    $jk = 0;
			    $ll = '';
			    $ul = '';
			    while ($mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk] != '-') {
				$ll.=$mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk];
				$jk++;
			    }
			    $jk++;
			    while ($mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk] != null) {
				$ul.=$mems[$pType]['rows'][$mems[$pType]['index']][$k][$jk];
				$jk++;
			    }
			    while ($ll != $ul) {
				$mems[$pType]['rows'][$mems[$pType]['index']][$k] = $ll;
				$ll++;
				$k++;
			    }
			    $mems[$pType]['rows'][$mems[$pType]['index']][$k] = $ll;
			}
			$i++;
			$k++;
		    }
		    if ($pType == 'w') {
			if ($mems[$pType]['rows'][$mems[$pType]['index']][0] == '*') {
			    $mems['owner'] = $mems[$pType]['gid'][$mems[$pType]['index']];
			}
		    }
		    if (authorityResolver($mems[$pType]['gid'][$mems[$pType]['index']])) {
			for ($m = 0; $m < count($mems[$pType]['rows'][$mems[$pType]['index']]); $m++) {
			    if ($mems[$pType]['rows'][$mems[$pType]['index']][$m] == '*') {
				$mems[$pType]['authRows'] = array('*');
				break;
			    } elseif ($mems[$pType]['authRows'][0] != '*')
				$mems[$pType]['authRows'][] = (int) $mems[$pType]['rows'][$mems[$pType]['index']][$m];
			}
		    }
		}
	    }
	} else {
	    $i++;
	}
    }
    if ($mems['r']['authRows'][0] != '*')
	$mems['r']['authRows'] = array_unique($mems['r']['authRows']);
    if ($mems['w']['authRows'][0] != '*')
	$mems['w']['authRows'] = array_unique($mems['w']['authRows']);
    return $mems;
}

function groupExe($group, $groups, $op) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/adminScripts/db_login.php";
    if ($op['CNG']) {
	$op['CNG'] = explode(',', $op['CNG']);
	$query = "INSERT INTO groups(`label`,`type`,`members`,`authUnits`)values('" . $op['CNG'][0] . "','" . $op['CNG'][1] . "','" . $op['CNG'][2] . "','" . $op['CNG'][3] . "')";
	$result = mysql_query($query, $dbc);
	$error.= mysql_error($dbc);
	$ngid = mysql_insert_id($dbc);
	$grMems['error'] = $error;
	$grMems['ngid'] = $ngid;
    }
    if ($group[0] == 'g') {
	$gid = substr($group, 1);
	$query = "SELECT * FROM groups WHERE `index`='" . $gid . "'";
	$result = mysql_query($query, $dbc);
	$error.= mysql_error($dbc);
	if (!$error and mysql_result($result, 0, 'index')) {
	    $authUnits = explode(':', mysql_result($result, 0, 'authUnits'));
	    for ($i = 0; $i < count($authUnits); $i++) {
		if ($authUnits[$i][0] == 'r')
		    $rAUs = substr($authUnits[$i], 1);
		elseif ($authUnits[$i][0] == 'w')
		    $wAUs = substr($authUnits[$i], 1);
	    }
	    $rA = authorityResolver($rAUs);
	    $wA = authorityResolver($wAUs);
	    $rA = ($rA or $wA);
	    if ($rA) {
		$members = explode(',', mysql_result($result, 0, 'members'));
		$initMems = $members;
	    } else {
		$members = array();
	    }
	    $memberCount = count($members);
	    $users = array();
	    $objects = array();
	    if ($groups == null)
		$groups = array();
	    $groups[] = $group;
	    $tasks = array();
	    $aObjs = array();
	    $inGpMems = array();
	    $delGrps = array();
	    $delMems = array();
	    $powLessObj = array();
	    if ($memberCount == 1 and $members[0] == '') {
		$members = array();
		$memberCount = 0;
	    }
	    $users = array();
	    $objects = array();
	    $reconGrp = FALSE;
	    if ($wA) {
		foreach ($op as $opType => $entities) {
		    $entities = explode(',', $entities);
		    if ($opType == 'udel' or $opType == 'del') {
			$entCount = count($entities);
			for ($i = 0; $i < $entCount; $i++) {
			    for ($j = 0; $j < $memberCount; $j++) {
				if ($members[$j] == $entities[$i]) {
				    unset($members[$j]);
				    $delMems[] = $entities[$i];
				    $reconGrp = TRUE;
				    break;
				}
			    }
			}
		    } elseif ($opType == 'add') {
			$entCount = count($entities);
			for ($i = 0; $i < $entCount; $i++) {
			    for ($j = 0; $j < $memberCount; $j++) {
				if ($members[$j] == $entities[$i]) {
				    $ae = TRUE;
				    break;
				}
			    }
			    if (!$ae) {
				$members[] = $entities[$i];
				if ($entities[$i][0] == 'u') {
				    $users[] = substr($entities[$i], 1);
				} elseif ($entities[$i][0] == 'o') {
				    $objects[] = substr($entities[$i], 1);
				} elseif ($entities[$i][0] == 'g') {
				    $groups[] = substr($entities[$i], 1);
				} elseif ($entities[$i][0] == 't') {
				    $tasks[] = substr($entities[$i], 1);
				} elseif ($entities[$i][0] == 's') {
				    $aObjs[] = substr($entities[$i], 1);
				}
				$memberCount++;
				$reconGrp = TRUE;
			    }
			}
		    } elseif ($opType == 'del') {
			
		    }
		}
	    }
	    if ($rA) {
		for ($k = 0; $k < $memberCount; $k++) {
		    if (!preg_match('/\./', $members[$k])) {
			if ($members[$k][0] == 'u') {
			    $users[] = $members[$k];
			} elseif ($members[$k][0] == 'o') {
			    $ob = explode('-', substr($members[$k], 1));
			    $oj = $ob[0];
			    $i = 0;
			    do {
				$objects[] = 'o' . $oj;
				$query = "SELECT `uid` FROM `objectTable` WHERE `index`='" . $oj . "'";
				$result = mysql_query($query, $dbc);
				$error = mysql_error($dbc);
				if (!$error) {
				    $row = mysql_fetch_row($result);
				    if ($row[0] != NULL)
					$users[] = 'u' . $row[0];
				}
				$oj++;
			    }while ($oj <= $ob[1]);
			} elseif ($members[$k][0] == 'g') {
			    $g = substr($members[$k], 1);
			    for ($i = 0; $i < count($groups); $i++) {
				if ($groups[$i] == $g)
				    $gEx = true;
			    }
			    if (!$gEx) {
				if ($op['delEmGrp'])
				    $cop['delEmGrp'] = TRUE;
				if ($op['udel']) {
				    $cop['udel'] = $op['udel'];
				    $f = groupExe($g, $groups, $cop);
				} else {
				    $f = groupExe($g, $groups, $cop);
				}
				$users = array_merge($users, $f['users']);
				$objects = array_merge($objects, $f['objects']);
				$groups = array_merge($groups, $f['groups']);
				$tasks = array_merge($tasks, $f['tasks']);
				$aObjs = array_merge($aObjs, $f['aObjs']);
				$delMems = array_merge($delMems, $f['delMems']);
				$powLessObj = array_merge($powLessObj, $f['powLessObj']);
				$inGpMems[$members[$k]] = $f['members'];
				if ($f['members'] == NULL) {
				    $emGrps[] = $members[$k];
				}
				$delGrps = array_merge($delGrps, $f['delGrps']);
				$inGpMems['inGpMems'] = $f['inGpMems'];
				$error.=$f['error'];
			    }
			} elseif ($members[$k][0] == 't') {
			    $t = substr($members[$k], 1);
			    $tasks[] = $members[$k];
			    $query = "SELECT `worker` FROM tasks WHERE `index`='" . $t . "'";
			    $result = mysql_query($query, $dbc);
			    $error.= mysql_error($dbc);
			    if (!$error and mysql_result($result, 0, 'worker')) {
				$oid = mysql_result($result, 0, 'worker');
				$query = "SELECT `uid` FROM `objectTable` WHERE `index`='" . $oid . "'";
				$result = mysql_query($query, $dbc);
				$error.=mysql_error($dbc);
				$uid = mysql_result($result, 0, 'uid');
				if ($uid)
				    $users[] = 'u' . mysql_result($result, 0, 'uid');
			    }
			} elseif ($members[$k][0] == 'U') {
			    $xUsers[] = $members[$k];
			} elseif ($members[$k][0] == 'O') {
			    $xObjects[] = $members[$k];
			} elseif ($members[$k][0] == 'G') {
			    $xGroups[] = $members[$k];
			}
		    } else {
			$aMems = explode('.', $members[$k]);
			if ($aMems[0][0] == 'o') {
			    $i++;
			    $object = substr($aMems[0], 1);
			    if ($aMems[1][0] == 't') {
				$task = substr($aMems[1], 1);
				$query = "SELECT worker FROM tasks WHERE `index`='" . $task . "'";
				$result = mysql_query($query, $dbc);
				$error1 = mysql_error($dbc);
				if (!$error1) {
				    if ($object == mysql_result($result, 0, 'worker')) {
					$query = "SELECT `uid`,`adminLevel` FROM `objectTable` WHERE `index`='" . $object . "'";
					$result = mysql_query($query, $dbc);
					$error1 = mysql_error($dbc);
					if (!$error1) {
					    $user = mysql_result($result, 0, 'uid');
					    $al = mysql_result($result, 0, 'adminLevel');
					    $query = "SELECT `adminLevel` FROM `users` WHERE `index`='" . $user . "'";
					    $result = mysql_query($query, $dbc);
					    if (authorizeTransit(mysql_result($result, 0, 'adminLevel'), $al))
						$users[] = 'u' . $row[0];
					}
					if (!$user)
					    $powLessObj[] = $members[$k];
					else
					    $aObjs[] = $members[$k];
				    }
				}
			    }elseif (TRUE) {
				
			    }
			}
		    }
		}
	    }
	    if ($wA) {
		if ($members == NULL)
		    $emGrps[] = $group;
		if ($op['delEmGrps']) {
		    for ($i = 0; $i < count($emGrps); $i++) {
			for ($j = 0; $j < $memberCount; $j++) {
			    if ($emGrps[$i] == $members[$j]) {
				unset($members[$j]);
				$reconGrp = true;
				break;
			    }
			}
		    }
		    for ($i = 0; $i < count($emGrps); $i++) {
			$query = "DELETE FROM `groups` WHERE `index`='" . substr($emGrps[$i], 1) . "'";
			$result = mysql_query($query, $dbc);
			$error2 = mysql_error($dbc);
			$delGrps[] = $emGrps[$i];
		    }
		}
	    }
	    $users = sortNormArray($users, $xUsers);
	    $objects = sortNormArray($objects, $xObjects);
	    $groups = sortNormArray($groups, $xGroups);
	    $tasks = sortNormArray($tasks, $xTasks);

	    if ($reconGrp) {
		sort($members);
		$grpStr = implode(',', $members);
		$query = "UPDATE `groups` SET `members`='" . $grpStr . "' WHERE `index`='" . $gid . "'";
		$result = mysql_query($query, $dbc);
		$error1 = mysql_error($dbc);
		if ($error1) {
		    $error.=$error1;
		}
	    }
	    if ($initMems != $members)
		$grMems['grMod'] = TRUE;
	    $grMems['users'] = $users;
	    $grMems['objects'] = $objects;
	    $grMems['groups'] = $groups;
	    $grMems['tasks'] = $tasks;
	    $grMems['aObjs'] = $aObjs;
	    $grMems['error'] = $error;
	    $grMems['members'] = $members;
	    $grMems['inGpMems'] = $inGpMems;
	    $grMems['delGrps'] = $delGrps;
	    $grMems['delMems'] = $delMems;
	    $grMems['powLessObj'] = $powLessObj;
	    if ($reconGrp) {
		if (!$error1) {
		    $queryts = "UPDATE `groups` SET `members`=now() WHERE `index`='" . $group . "'";
		    $resultts = mysql_query($queryts, $GLOBALS['timestampLink']);
		    $errorts = mysql_error($GLOBALS['timestampLink']);
		    $queryu = "UPDATE `groups` SET `members`='" . $_SESSION['uid'] . "' WHERE `index`='" . $group . "'";
		    $resultu = mysql_query($queryu, $GLOBALS['uidLink']);
		    $erroru = mysql_error($GLOBALS['uidLink']);
		}
	    }
	} else {
	    $grMems['delGrps'] = array($group);
	}
    } else {
	$grMems['members'] = array($group);
    }
    include '../adminScripts/db_logout.php';
    return $grMems;
}

function taskExe($task, $op) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/adminScripts/db_login.php";
    if ($op['CNT']) {
	$cnt['work'] = ($op['CNT']['work'] ? "'" . $op['CNT']['work'] . "'" : 'NULL');
	$cnt['type'] = ($op['CNT']['type'] ? "'" . $op['CNT']['type'] . "'" : 'NULL');
	$cnt['target'] = ($op['CNT']['target'] ? "'" . $op['CNT']['target'] . "'" : 'NULL');
	$cnt['worker'] = ($op['CNT']['worker'] ? "'" . $op['CNT']['worker'] . "'" : 'NULL');
	$cnt['startTime'] = ($op['CNT']['startTime'] ? "'" . $op['CNT']['startTime'] . "'" : 'NULL');
	$cnt['endTime'] = ($op['CNT']['endTime'] ? "'" . $op['CNT']['endTime'] . "'" : 'NULL');
	$cnt['work'] = ($op['CNT']['work'] ? "'" . $op['CNT']['work'] . "'" : 'NULL');
	$cnt['sst'] = ($op['CNT']['sst'] ? "'" . $op['CNT']['sst'] . "'" : 'NULL');
	$cnt['set'] = ($op['CNT']['set'] ? "'" . $op['CNT']['set'] . "'" : 'NULL');
	$cnt['state'] = ($op['CNT']['state'] ? "'" . $op['CNT']['state'] . "'" : 'NULL');
	$cnt['result'] = ($op['CNT']['result'] ? "'" . $op['CNT']['result'] . "'" : 'NULL');
	$vs = implode(',', $cnt);
	$query = "INSERT INTO `tasks`(`work`,`type`,`target`,`worker`,`startTime`,`endTime`,`scheduledST`,`scheduledET`,`state`,`result`) VALUES(" . $vs . ")";
	$result = mysql_query($query, $dbc);
	$error = mysql_error($dbc);
	if (!$error and $result) {
	    $te['ntid'] = mysql_insert_id($dbc);
	}
    }
    include '../adminScripts/db_logout.php';
    return $te;
}

function objectExe($obj, $op) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/adminScripts/db_login.php";
    if ($op['CNO'] and authorizeTransit($_SESSION['adminLevel'], $op['CNO']['adminLevel'])) {
	$CNO['id'] = ($op['CNO']['id'] ? "'" . $op['CNO']['id'] . "'" : 'NULL');
	$CNO['uid'] = ($op['CNO']['uid'] ? "'" . $op['CNO']['uid'] . "'" : 'NULL');
	$CNO['adminLevel'] = ($op['CNO']['adminLevel'] ? "'" . $op['CNO']['adminLevel'] . "'" : 'NULL');
	$CNO['type1'] = ($op['CNO']['type1'] ? "'" . $op['CNO']['type1'] . "'" : 'NULL');
	$CNO['type2'] = ($op['CNO']['type2'] ? "'" . $op['CNO']['type2'] . "'" : 'NULL');
	$CNO['function'] = ($op['CNO']['function'] ? "'" . $op['CNO']['function'] . "'" : 'NULL');
	$CNO['description'] = ($op['CNO']['description'] ? "'" . $op['CNO']['description'] . "'" : 'NULL');
	$CNO['passKey'] = "'" . generatePassword(16, 8) . "'";
	$vs = implode(',', $CNO);
	$query = "INSERT INTO `objectTable`(`id`,`uid`,`adminLevel`,`type1`,`type2`,`function`,`description`,`passKey`) VALUES(" . $vs . ")";
	$result = mysql_query($query, $dbc);
	$error = mysql_error($dbc);
	if (!$error and $result) {
	    $id = $op['CNO']['id'];
	    $fh = fopen($_SERVER['DOCUMENT_ROOT'] . '/objProperty/' . $id . '.xml', 'a+');
	    $objXML = new DOMDocument();
	    $root = $objXML->createElement('object');
	    $idT = $objXML->createElement('id', $id);
	    $uidT = $objXML->createElement('uid', $op['CNO']['uid']);
	    $alT = $objXML->createElement('al', $op['CNO']['adminLevel']);
	    $t1T = $objXML->createElement('type1', $op['CNO']['type1']);
	    $t2T = $objXML->createElement('type2', $op['CNO']['type2']);
	    $funT = $objXML->createElement('function', $op['CNO']['function']);
	    $desT = $objXML->createElement('description', $op['CNO']['description']);
	    $root->appendChild($idT);
	    $root->appendChild($uidT);
	    $root->appendChild($alT);
	    $root->appendChild($t1T);
	    $root->appendChild($t2T);
	    $root->appendChild($funT);
	    $root->appendChild($desT);
	    $objXML->appendChild($root);
	    $fw = fwrite($fh, $objXML->saveXML());
	    $fc = fclose($fh);
	    $to['noid'] = mysql_insert_id($dbc);
	    if ($CNO['function']) {
		$query = "UPDATE `tasks` SET `worker`='o" . $to['noid'] . "' WHERE `index`=" . $CNO['function'];
		$result = mysql_query($query, $dbc);
		$error .= mysql_error($dbc);
	    }
	}
    }
    include '../adminScripts/db_logout.php';
    $to['error'] = $error;
    return $to;
}

function authorityResolver($authStr) {
    $authEnts = explode(',', $authStr);
    for ($i = 0; $i < count($authEnts); $i++) {
	$authorized = TRUE;
	$aMems = explode('.', $authEnts[$i]);
	for ($j = 0; $j < count($aMems); $j++) {
	    if ($aMems[$j][0] == 'u') {
		if ($_SESSION['uid'] == substr($aMems[$j], 1)) {
		    $authorized = ($authorized and TRUE);
		} else {
		    $authorized = FALSE;
		}
	    } elseif ($aMems[$j][0] == 'o') {
		$authorized = ($authorized and authObject(substr($aMems[$j], 1)));
	    } elseif ($aMems[$j][0] == 't') {
		$authorized = ($authorized and authTask(substr($aMems[$j], 1)));
	    } elseif ($aMems[$j][0] == 'g') {
		$authorized = ($authorized and authGroup(substr($aMems[$j], 1)));
	    } elseif ($aMems[$j][0] == 'a') {
		$authorized = ($authorized and authorizeTransit($_SESSION['adminLevel'], substr($aMems[$j], 1)));
	    } else {
		$authorized = FALSE;
	    }
	}
	if ($authorized) {
	    return $authorized;
	}
    }
    return FALSE;
}

function authTask($tid) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/db_login.php";
    $query = "SELECT * FROM tasks WHERE `index`='" . $tid . "'";
    $result = mysql_db_query('collegedb2admin', $query, $dbc);
    $oid = mysql_result($result, 0, 'worker');
    $authorized = authorityResolver($oid);
    include "$root/lib/db_logout.php";
    return $authorized;
}

function authObject($oid) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/db_login.php";
    $query = "SELECT * FROM objectTable WHERE `index`='" . $oid . "'";
    $result = mysql_db_query('collegedb2admin', $query, $dbc);
    if (mysql_result($result, 0, 'type1') == 'NORMAL') {
	if ($_SESSION['uid'] == mysql_result($result, 0, 'uid') and authorizeTransit($_SESSION['adminLevel'], mysql_result($result, 0, 'adminLevel'))) {
	    $authorized = TRUE;
	} else {
	    $authorized = FALSE;
	}
    } elseif (mysql_result($result, 0, 'type1') == 'GROUP') {
	$authorized = authGroup(mysql_result($result, 0, 'uid'));
    }
    include "$root/lib/db_logout.php";
    return $authorized;
}

function authGroup($gid) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/db_login.php";
    $query = "SELECT `members` FROM groups WHERE `index`='" . $gid . "'";
    $result = mysql_db_query('collegedb2admin', $query, $dbc);
    $authorized = authorityResolver(mysql_result($result, 0, 'members'));
    include "$root/lib/db_logout.php";
    return $authorized;
}

function tableAllowed($dbTable) {
    if (authorizeTransit($_SESSION['adminLevel'], 'Zz0')) {
	return true;
    }
    if (domesticSlave($_SESSION['adminLevel'], 'Zz9')) {
	if (strpos($dbTable, '_' . $_SESSION['username'])) {
	    return TRUE;
	}
	require '../adminLevelDecoder.php';
	if (strpos($dbTable, '_' . $adminLevelDecoder[$_SESSION['function'][$_POST['role']]['aL'][0]]['label'] . '_' . $adminLevelDecoder[$_SESSION['function'][$_POST['role']]['aL'][2]]['label'])) {
	    return TRUE;
	}
    }
    if (anyDeptSlave($_SESSION['function'][$_POST['role']]['aL'], 'Zz0')) {
	require '../adminLevelDecoder.php';
	if (strpos($dbTable, strtolower('_' . $adminLevelDecoder[$_SESSION['function'][$_POST['role']]['aL'][0]]['label'] . '_' . $adminLevelDecoder[$_SESSION['function'][$_POST['role']]['aL'][2]]['label']))) {
	    return TRUE;
	}
    }
    return FALSE;
}

function sortNormArray($array, $xArr) {
    sort($array);
    for ($i = 0; $i < count($array); $i++) {
	if ($array[$i] == $array[$i + 1]) {
	    unset($array[$i]);
	}
	for ($j = 0; $j < count($xArr); $j++) {
	    if ($array[$i] == $xArr[$j]) {
		unset($array[$i]);
		break;
	    }
	}
    }
    return $array;
}

function generatePassword($length, $strength) {
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
	$consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
	$vowels .= "AEUY";
    }
    if ($strength & 4) {
	$consonants .= '23456789';
    }
    if ($strength & 8) {
	$consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
	if ($alt == 1) {
	    $password .= $consonants[(rand() % strlen($consonants))];
	    $alt = 0;
	} else {
	    $password .= $vowels[(rand() % strlen($vowels))];
	    $alt = 1;
	}
    }
    return $password;
}

function innerHTML($el) {
    $doc = new DOMDocument();
    $doc->appendChild($doc->importNode($el, TRUE));
    $html = trim($doc->saveHTML());
    $tag = $el->nodeName;
    return preg_replace('@^<' . $tag . '[^>]*>|</' . $tag . '>$@', '', $html);
}

function prunePermissions($ops, &$liveDBTable) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/lib/superScripts/db_login.php";
    foreach ($ops as $opType => $params) {
	switch ($opType) {
	    case 'delRows':
		$dbTable = $params['dbTable'];
		$dr = $params['rows'];
		if ($params['columns']) {
		    if ($params['ms']) {
			foreach ($params['ms'] as $i => $ms) {
			    if ($ms['r']['index'] > -1 or $ms['w']['index'] > -1) {
				foreach ($ms['r']['gid'] as $key => $gid) {
				    $k = 0;
				    $rows = $ms['r']['rows'][$key];
				    for ($j = 0; $j < count($rows); $j++) {
					if ($rows[$j] == $dr[$k]) {
					    unset($rows[$j]);
					    $k++;
					}
				    }
				    $co[$i][] = "{r" . $gid . ",{" . dtsRows($rows) . "}}";
				}
				foreach ($ms['w']['gid'] as $key => $gid) {
				    $k = 0;
				    $rows = $ms['w']['rows'][$key];
				    for ($j = 0; $j < count($rows); $j++) {
					if ($rows[$j] == $dr[$k]) {
					    unset($rows[$j]);
					    $k++;
					}
				    }
				    $co[$i][] = "{w" . $gid . ",{" . dtsRows($rows) . "}}";
				}
				$co[$i] = implode(",", $co[$i]);
				if ($co[$i] != $params['columns']['comments'][$i]) {
				    $colName = $params['columns']['field'][$i];
				    $type = $params['columns']['type'][$i];
				    $null = $params['columns']['null'][$i];
				    $default = $params['columns']['default'][$i];
				    $key = $params['columns']['key'][$i];
				    $extra = $params['columns']['extra'][$i];
				    $qn = ($null == 'YES') ? ' NULL ' : ' NOT NULL';
				    $qd = ($default != NULL) ? ' DEFAULT ' . $default : '';
				    $qk = ($key != '') ? ' KEY ' . $key : '';
				    $qe = ($extra != '') ? ' ' . $extra : '';
				    $qc = ($co[$i] != '') ? " COMMENT '" . $co[$i] . "'" : '';
				    $query = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` " . $type . $qn . $qd . $qe . $qc;
				    $result = mysql_query($query, $dbc);
				    $error = mysql_error($dbc);
				    if (!$error) {
					$liveDBTable['cells']['tHR'][$colName]['Comment'] = $co[$i];
					$queryts = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` TIMESTAMP NULL COMMENT  '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
					$resultts = mysql_query($queryts, $timestampLink);
					$errorts = mysql_error($timestampLink);
					$queryu = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` INT(13) NULL COMMENT  '" . $_SESSION['oid'] . "'";
					$resultu = mysql_query($queryu, $uidLink);
					$erroru = mysql_error($uidLink);
				    }
				}
			    }
			}
		    }
		}
		break;
	}
    }
    include "$root/lib/superScripts/db_logout.php";
    return $co;
}

function dtsRows($drows) {
    sort($drows);
    $rows = $drows[0];
    for ($i = 1; $i < count($drows); $i++) {
	if ($drows[$i] == ($drows[$i - 1] + 1)) {
	    $seqStrt = true;
	    $es = $drows[$i];
	} else {
	    if ($seqStrt) {
		$rows.='-' . $es;
		$seqStrt = false;
	    }
	    $rows.=',' . $drows[$i];
	}
    }
    if ($seqStrt) {
	$rows.='-' . $es;
	$seqStrt = false;
    }
    return $rows;
}

function getLiveTable($tn) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/conf.php";
    $dbtKey = $_SESSION['shm'][$tn]['dbtKey'] = ftok("$DIR_dbTableData/$tn", 'c');
    if ($dbtKey > -1) {
	$dbtSemId = $_SESSION['shm'][$tn]['dbtSemId'] = sem_get($dbtKey);
	$dbtShmId = $_SESSION['shm'][$tn]['dbtShmId'] = shm_attach($dbtKey, 1000000);
	$sa = sem_acquire($dbtSemId);
	$liveDBTable = shm_get_var($dbtShmId, $dbtKey);
    } else {
	unset($_SESSION['shm'][$tn]);
    }
    return $liveDBTable;
}

function closeLiveTable($tn, &$liveDBTable) {
    $tn = $liveDBTable['name'];
    if ($_SESSION['shm'][$tn]) {
	$spv = shm_put_var($_SESSION['shm'][$tn]['dbtShmId'], $_SESSION['shm'][$tn]['dbtKey'], $liveDBTable);
	$sr = sem_release($_SESSION['shm'][$tn]['dbtSemId']);
    }
}

function removeLiveTable($tn) {
    $sr = shm_remove($_SESSION['shm'][$tn]['dbtShmId']);
    unset($_SESSION['shm'][$tn]);
}

function getTableFromFile($tn) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/conf.php";
    $fn = "$DIR_dbTableData/$tn";
    $fp = fopen($fn, 'a+');
    $tp = fgets($fp);
    fclose($fp);
    $top = json_decode($tp, TRUE);
    return $top;
}

function putTableInFile(&$tp, $dbTable) {
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    $fn = "${GLOBALS['DIR_dbTableData']}/$dbTable";
    $fp = fopen($fn, "w");
    $tp = json_encode($tp, JSON_HEX_QUOT | JSON_HEX_APOS);
    $fw = fwrite($fp, $tp);
    $fc = fclose($fp);
    return $fw and $fc;
}

function true_array_unique($a) {
    $a = array_unique($a);
    foreach ($a as $key => $value) {
	$b[] = $value;
    }
    return $b;
}

?>