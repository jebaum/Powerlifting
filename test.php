<?php

$cost = 15;
$salt = "22characterstringoshit";

$_salt = sprintf('$2a$%02d$%s', $cost, $salt);


$hash = crypt('dingdong', $_salt);


// notice $hash contains the cost and salt already, so you can just
// put it back into `crypt` to very passwords and not worry about
// salts

// we would store this in the db
echo "$hash\n";


// then you'd verify it like this
$hash_verify = crypt('dingdong', $hash);

if( $hash_verify == $hash ) {
  echo "titties!\n";
} else {
  echo ":|\n";
}

echo "$hash_verify\n";

