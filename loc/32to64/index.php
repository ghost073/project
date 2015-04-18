<?php
define("STEAM_ID_UPPER_32_BITS", "00000001000100000000000000000001");
// gets the lower 32-bits of a 64-bit steam id
function GET_32_BIT ($ID_64) {
	$upper = gmp_mul( bindec(STEAM_ID_UPPER_32_BITS) , "4294967296" );
	return gmp_strval(gmp_sub($ID_64,$upper));
}

// creates a 64-bit steam id from the lower 32-bits
function MAKE_64_BIT ( $ID_32, $hi = false ) {
	if ($hi === false) {
		$hi = bindec(STEAM_ID_UPPER_32_BITS);
	}

	// workaround signed/unsigned braindamage on x32
	$hi = sprintf ( "%u", $hi );
	$ID_32 = sprintf ( "%u", $ID_32 );

	return gmp_strval ( gmp_add ( gmp_mul ( $hi, "4294967296" ), $ID_32 ) );      
} 

$my_64_id = "76561197986553915";
$my_32_id = "26288187";

$calc_32 = GET_32_BIT($my_64_id); // gives "26288187"
$calc_64 = MAKE_64_BIT($my_32_id); // gives "76561197986553915"  
var_dump($calc_32);
var_dump('32bit:' . $calc_32 . '=====' . '64bit:' . $calc_64);

$new_64_id = '76561198004671288';
$new_calc_32 = GET_32_BIT($new_64_id);
var_dump($new_calc_32);
?>