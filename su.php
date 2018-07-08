<?php

/*

	Mond-und-Sonnen-auf-und-untergang

	basiert auf:
	https://astronomy.stackexchange.com/questions/24304/expression-for-length-of-sunrise-sunset-as-function-of-latitude-and-day-of-year
	Section "Here is an implementation of rising/setting times in Basic"
 
	Ein unbenannter Spaghetticodefreund 201806
	20180619 php Version
	20180701
 
	Sonnenberechnung aus Bahndaten und Koeffizienten
	Low-precision formulae for planetary positions
	Fundamental arguments (Van Flandern & Pulkkinen, 1979)
	https://doi.org/10.1086/190623

	Source: https://computus.de/mondphase/mondphase.htm (Javascript)

	Der Algorithmus zur Mondphasenberechnung stammt aus dem Buch
	"Astronomische Algorithmen" von Jean Meeus
	Verlag Johann Ambrosius Barth
	Leipzig, Berlin, Heidelberg 2. Auflage 1994.
	ISBN 3-335-00400-0

*/

	# echo "Precision: " . ini_get( 'precision' ) . PHP_EOL;
	define( "DEBUG", false );
	define( "pi", pi() );
	define( "pi2", 2.0*pi() );
	define( "grad", pi/180.0 );
	define( "k1", 15.04107 * grad );

	$a = array();
	$d = array();
	$m = array();
	$Zeit = array();
	$Zh = array();
	$Zm = array();
	$Az = array();

	$eventCnt = 0;

function readln( $prompt = '' ) {

	if ( function_exists( "readline" ) ) {

		return readline( "$prompt " );

	} else {

		echo "$prompt ";
		return rtrim( fgets( STDIN ), "\n" );
	
	}

}

// return integer value, closer to 0
function Int($x) {
	if ($x < 0) {
		return ceil($x);
	} else {
		return floor($x);
	}
}

	# Defaultkoordinaten "Berlin, Drachenberg"

	echo "Sonnenberechnung aus Bahndaten und Koeffizienten\n";

	$breite = rtrim( readln( "Nördl. Breite          ±dd.dddd°  [52.5°]:"), "°" );
	if ( empty( $breite ) ) {
		$breite = 52.5;
	}

	$laenge = rtrim( readln( "Östliche Länge        ±ddd.dddd° [13.25°]:"), "°" );
	if ( empty( $laenge ) ) {
		$laenge = 13.25;
	}

	$zeitzoneCalculated = calculatedZeitzone( $laenge );
	
	/* Eingabe in Stunden, ganzzahlig mit Vorzeichen für E(+) und W(-) */

	echo "Zeitzone des Rechners                    : " . formatZeitzone( Date( "Z" ) / 3600 ) . "\n";
	echo "Zeitzone nach Länge                      : " . formatZeitzone( $zeitzoneCalculated ) . "\n";

	$zeitzone = readln( "Zeitzone ±h [" . formatZeitzone( $zeitzoneCalculated ) . " (Zeitzone nach Länge)]:");
	if ( empty( $zeitzone ) ) {
		$zeitzone = $zeitzoneCalculated;
	}

	echo "Zeitzone nach Eingabe: " . formatZeitzone( $zeitzone ) . "\n";

	$today = Date( "Y.md" );
	$date = readln( "Datum       yyyy.mmdd [$today (heute)]:" );
	if ( empty( $date ) ) {
		$date = $today;
	}

	echo <<<HERE

Sonnenberechnung aus Bahndaten und Koeffizienten

Low-precision formulae for planetary positions
Fundamental arguments (Van Flandern & Pulkkinen, 1979)
https://doi.org/10.1086/190623

Der Algorithmus zur Mondphasenberechnung stammt aus dem Buch
"Astronomische Algorithmen" von Jean Meeus. 2. Auflage 1994.
https://computus.de/mondphase/mondphase.htm (Javascript)
	
SA: Sonnenaufgang  SU: Sonnenuntergang
MA: Mondaufgang    MU: Monduntergang
MF: Mondfinsternis Az: Azimut


HERE;

	echo "Breite: $breite Länge: $laenge Zeitzone: " . formatZeitzone( $zeitzone ) . "\n";
	$laenge = $laenge / 360.0;

	list( $j, $f, $t, $t0 ) = Kalender( $date, $zeitzone, $laenge );

	calcSun( $breite, $t );
	echo " ";
	calcMoon( $breite, $t );
	echo "\n";

	$vm = NaechsterVM( $j );

	// echo "VM " . date('d.m.Y H:i:s', jd2unix( $vm ) );

	$pMF = NaechsteMF( $j, 0 ); // partielle MF
	$tMF = NaechsteMF( $j, 1 ); // totale MF

	$d = Date( "d.m.Y", jd2unix( $vm - 1.0 ) );
	list( $j, $f, $t, $t0 ) = Kalender( Date( "Y.md", jd2unix( $vm - 1.0 ) ), $zeitzone, $laenge );

	calcSun( $breite, $t );
	echo " ";
	calcMoon( $breite, $t );
	echo "\n";

	echo str_repeat( "*", 40 ) . "\n";
	# echo "VM " . date('d.m.Y H:i:s', jd2unix( $vm ) );
	echo "VM " . date('H:i:s', jd2unix( $vm ) );

	if ( checkMFtime( $pMF, $vm ) ) {
		# echo " Partielle MF " . date('d.m.Y H:i:s', jd2unix( $pMF ) ) . "\n";
		echo " Partielle MF " . date('H:i:s', jd2unix( $pMF ) ) . "\n";
	} else	if ( checkMFtime( $tMF, $vm ) ) {
		# echo " Totale MF " . date('d.m.Y H:i:s', jd2unix( $tMF ) ) . "\n";
		echo " Totale MF " . date('H:i:s', jd2unix( $tMF ) ) . "\n";
	} else {
		echo "\n";
	}
	echo str_repeat( "*", 40 ) . "\n";
	
	$d = Date( "d.m.Y", jd2unix( $vm ) );
	list( $j, $f, $t, $t0 ) = Kalender( Date( "Y.md", jd2unix( $vm ) ), $zeitzone, $laenge );

	calcSun( $breite, $t );
	echo " ";
	calcMoon( $breite, $t );
	echo "\n";


	$d = Date( "d.m.Y", jd2unix( $vm + 1.0 ) );
	list( $j, $f, $t, $t0 ) = Kalender( Date( "Y.md", jd2unix( $vm + 1.0 ) ), $zeitzone, $laenge );

	calcSun( $breite, $t );
	echo " ";
	calcMoon( $breite, $t );
	echo "\n";


	/* 
	$t = unixtojd() - 1;

	for ( $i = 0; $i < 12; $i++ ) {

		$vm = NaechsterVM( $t );
		echo "VM " . date('d.m.Y H:i:s', jd2unix( $vm ) );

		$pMF = NaechsteMF( $t, 0 ); // partielle MF
		$tMF = NaechsteMF( $t, 1 ); // totale MF

		if ( checkMFtime( $pMF, $vm ) ) {
			echo " Partielle MF " . date('d.m.Y H:i:s', jd2unix( $pMF ) ) . "\n";
		} else	if ( checkMFtime( $tMF, $vm ) ) {
			echo " Totale MF " . date('d.m.Y H:i:s', jd2unix( $tMF ) ) . "\n";
		} else {
			echo PHP_EOL;
		}

		$t = $vm + 1;

		// date_default_timezone_set( "GMT" );
		// echo date('D, d M Y H:i:s T', $nextVM ) . PHP_EOL;

	}

	*/
	
exit;


function calcMoon( $breite, $t ) {

	global $u, $v, $v2, $w;
	global $a0, $a2, $c, $d0, $d2, $s, $t0, $z;

	for ( $i = 1; $i <= 3; $i++ ) {

		list( $alpha, $delta, $rho ) = BahndatenMond( $t );

		$m[$i][1] = $alpha;
		$m[$i][2] = $delta;
		$m[$i][3] = $rho;
		$t = $t + 0.5;

	}

	if ( $m[2][1] <= $m[1][1] ) {
		$m[2][1] = $m[2][1] + pi2;
	}

	if ( $m[3][1] <= $m[2][1] ) {
		$m[3][1] = $m[3][1] + pi2;
	}
	
	$z1 = grad * ( 90.567 - 41.685 / $m[2][3] );
	$s = sin( $breite * grad );
	$c = cos( $breite * grad );
	$z = cos( $z1 );
	$m8 = 0;
	$w8 = 0	;
	$a0 = $m[1][1];
	$d0 = $m[1][2];

	$v0 = 0.0;

	for ( $hour = 0; $hour <= 23; $hour++ ) {

		$p = ( $hour + 1.0 ) / 24.0;

		$f0 = $m[1][1] ;
		$f1 = $m[2][1];
		$f2 = $m[3][1];
		$a2 = Interpolation( $f0, $f1, $f2, $p );

		$f0 = $m[1][2];
		$f1 = $m[2][2];
		$f2 = $m[3][2];
		$d2 = Interpolation( $f0, $f1, $f2, $p );

		calcRiseSet( "Moon", $hour, $t0, $a0, $a2, $d0, $d2, $v0, $v2, $m8, $w8, $s, $c, $z );

		$a0 = $a2;
		$d0 = $d2;
		$v0 = $v2;

	}
	
	calcVisibility( "Moon", $m8, $w8, $v2 );
}


function calcSun( $breite, $t ) {
	global $u, $v, $v2, $w;
	global $a0, $a2, $c, $d0, $d2, $s, $t0, $z;

	list( $alpha, $delta, $rho ) = BahndatenSonne( $t );

	$a[1] = $alpha;
	$d[1] = $delta;

	list( $alpha, $delta, $rho ) = BahndatenSonne( $t + 1.0 );

	$a[2] = $alpha;
	$d[2] = $delta;

	if ( $a[2] < $a[1] ) {
		$a[2] = $a[2] + pi2;
	}

	$z1 = grad * 90.833; // Zenith dist.
	$s = sin( $breite * grad );
	$c = cos( $breite * grad );
	$z = cos( $z1 );

	$m8 = 0;
	$w8 = 0;

	$a0 = $a[1];
	$d0 = $d[1];
	$da = $a[2] - $a[1];
	$dd = $d[2] - $d[1];

	$v0 = 0.0;

	for ( $hour = 0; $hour <= 23; $hour++ ) {

		$p = ( $hour + 1 ) / 24.0;
		$a2 = $a[1] + $p * $da;
		$d2 = $d[1] + $p * $dd;

		calcRiseSet( "Sun", $hour, $t0, $a0, $a2, $d0, $d2, $v0, $v2, $m8, $w8, $s, $c, $z );

		$a0 = $a2;
		$d0 = $d2;
		$v0 = $v2;

	}
	
	calcVisibility( "Sun", $m8, $w8, $v2 );
}


/* SUBROUTINEN */

//	Eingabe Jahr Monat und Tag;

function Kalender( $date, $zeitzone, $laenge ) {
	
	$year = intval( $date );					// Y = intval (jjjj.mmmtt)  			--> jjjj;
	$monat = 100.0000001 * ( $date - $year );	// Mm = 100.000001 *(jjjj.mmtt-jjjj) --> mm.tt;
	$m = intval( $monat ); 						// M = intval(mm.tt) 					--> mm;
	$d = 100.0000001 * ( $monat - $m );			// D = 100.000001 * (mm.tt-mm) 		--> tt.tt;
	$d = intval( $d ); 							// $d = intval(tt.tt) 					--> tt;
	$g = 1;

	if ( $year < 1583 ) {
		$g = 0;
	}

	$d1 = intval( $d );
	$f = getDecimalPart( $d1 ) - 0.5;
	$jd = -intval( 7 * ( intval( ( $m + 9 ) / 12 ) + $year ) / 4);

	if ( $g !== 0 ) { // nach 1583

		$s = sign( $m - 9 );
		$a = abs( $m - 9 );
		$j3 = intval( $year + $s * intval( $a / 7 ) );
		$j3 = -intval( ( intval( $j3 / 100 ) + 1 ) * 3 / 4 );

	}
	
	$jd += intval( 275.0 * $m / 9 ) + $d1 + $g * $j3;
	$jd += 1721027.0 + 2 * $g + 367.0 * $year;

	if ( $f < 0 ) {

		$f += 1.0;
		$jd -= 1.0;

	}

	$customDate = date_create( "$d.$m.$year" );
	# echo date_format( $customDate, "d.m.Y" ) . " JD: " . sprintf( "%.4f", $jd ) . " ";
	echo date_format( $customDate, "d.m.Y" ) . ": ";

	# Julian days since 2000 January 1.5 = JD 2451545.0
	$t = $jd - 2451545.0 + $f;
	$t0 = Zeitzone( $t, $zeitzone, $laenge );
	$t = $t - $zeitzone / 24.0;

	return array( $jd, $f, $t, $t0 );
}


function strSign( $i ) {
	return sign( $i ) ? "+" : "-";
}

function calculatedZeitzone( $laenge ) {
	return Int( round( sign( $laenge ) * ( $laenge + 7.5 ) ) / 15 );
}

function formatZeitzone( $i ) {
	return "UTC" . strSign( $i ) . abs( $i );
}


/*
	Bezogen auf 0h Zeitzone;
*/
function Zeitzone( $t, $zeitzone, $laenge ) {

	$t0 = $t / 36525.0;
	$s = 24110.5 + 8640184.813 * $t0;
	$s += -86636.6 * $zeitzone / 24.0 + 86400.0 * $laenge;
	
	return getDecimalPart( $s / 86400.0 ) * pi2;
}

function julianCenturiesSince1900( $t ) {
	# Julian centuries since 1900.0
	return $t / 36525.0 + 1.0 ;
}

/*	3-Punkt Interpolation */
function Interpolation( $f0, $f1, $f2, $p ) {

	$a = $f1 - $f0;
	$b = $f2 - $f1 - $a;

	return $f0 + $p * ( 2.0 * $a + $b * ( 2.0 * $p - 1.0 ) );

}


function sign( $floatNumber ) {
    return ( $floatNumber > 0.0 ) ? 1 : ( ( $floatNumber < 0.0 ) ? -1 : 0 );
} 


function getDecimalPart( $floatNum ) {
    return $floatNum - intval( $floatNum );
}


function s( $revolutions ) {
	return sin( pi2 * getDecimalPart( $revolutions ) );
}


function c( $revolutions ) {
	return cos( pi2 * getDecimalPart( $revolutions ) );
}


function calcRiseSet( $obj, $hour, $t0, $a0, &$a2, $d0, &$d2, &$v0, &$v2, &$m8, &$w8, $s, $c, $z ) {

	global $eventCnt, $Az, $Zh, $Zm, $Zeit;

	$l0 = $t0 + $hour * k1;
	$l2 = $l0 + k1;

	if ( ( $obj === "Moon" ) && ( $a2 < $a0 ) ) {
		$a2 = $a2 + pi2;
	}

	$h0 = $l0 - $a0;
	$h2 = $l2 - $a2;
	$h1 = ( $h2 + $h0 ) / 2.0 ; // berechnet Stundenwinkel
	$d1 = ( $d2 + $d0 ) / 2.0 ; // berechnet Deklination

	if ( $hour <= 0.0 ) {

		$v0 = $s * sin( $d0 ) + $c * cos( $d0 ) * cos( $h0 ) - $z;

	}
	
	$v2 = $s * sin( $d2 ) + $c * cos( $d2 ) * cos( $h2 ) - $z;
	
	if ( sign( $v0 ) === sign( $v2 ) ) {
		return;
	}

	$v1 = $s * sin( $d1 ) + $c * cos( $d1 ) * cos( $h1 ) - $z;
	$a = 2.0 * $v2 - 4.0 * $v1 + 2.0 * $v0;
	$b = 4.0 * $v1 - 3.0 * $v0 - $v2;
	$d = $b * $b - 4.0 * $a * $v0;

	if ( $d < 0.0 ) {
		return;
	}

	$d = sqrt( $d );
	
	$e = ( -$b + $d ) / ( 2.0 * $a );

	if ( ( $e > 1.0 ) || ( $e < 0.0 ) ) {
		$e = ( -$b - $d ) / ( 2.0 * $a );
	}

	$t3 = $hour + $e + 1.0 / 120.0;
	$h3 = intval( $t3 );
	$m3 = intval( getDecimalPart( $t3 ) * 60.0 );

	$eventCnt++;
	$Zh[$eventCnt] = $h3;
	$Zm[$eventCnt] = $m3 * 5.0/300.0;
	$Zeit[$eventCnt] = $Zh[$eventCnt] + $Zm[$eventCnt];

	$h7 = $h0 + $e * ( $h2 - $h0 );
	$n7 = -cos( $d1 ) * sin( $h7 );
	$d7 = $c * sin( $d1 ) - $s * cos( $d1 ) * cos( $h7 );
	$a7 = atan( $n7 / $d7 ) / grad;

	if ( $d7 < 0.0 ) {
		$a7 -= 180.0;
	}
	
	if ( $a7 < 0.0 ) {
		$a7 += 360.0;
	}

	if ( $a7 > 360.0 ) {
		$a7 -= 360.0;
	}

	$Az[$eventCnt] = $a7;

	$azimut = sprintf( "%03.0f", round( $a7, 2 ) );
	$time = sprintf( "%02d", $h3 ) . ":" . sprintf( "%02d", $m3 );

	$riseOrSet = "";

	if ( ( $v0 < 0.0 ) && ( $v2 > 0.0 ) ) {
	
		$m8 = 1;
		$riseOrSet = "A";

	} else if ( ( $v0 > 0.0 ) && ( $v2 < 0.0 ) ) {

			$w8 = 1;
			$riseOrSet = "U";

	}

	$object = ( $obj === "Moon" ) ? "M" : "S";
	echo "$object${riseOrSet} $time Az ${azimut}° ";

	if ( $eventCnt == 2 ) {
		mittag( $Az, $Zeit );
		$eventCnt = 0;
	}
}

function mittag( $Az, $Zeit ) {	

	$DeltaAz = $Az[2] - $Az[1];
	if ( $Az[1] < $Az[2] ) {
		$DeltaAz = $Az[1] - $Az[2];
	}
	
	$AzMittag = $Az[2] + $DeltaAz / 2;
	
	if ( $AzMittag > 360.0 ) {
		$AzMittag = $AzMittag - 360.0;
	}
	
	$DeltaZ = $Zeit[2] - $Zeit[1];

	if ( $Zeit[2] < $Zeit[1] ) {
		$DeltaZ = $Zeit[1] - $Zeit[2];
	}
	
	$Zmittag = $Zeit[1] + $DeltaZ / 2;

	If ( $Zmittag > 24 ) {
		$Zmittag = $Zmittag - 24;
	}
	
	$std = $Zmittag;
	$min = ( $Zmittag - Int( $std ) ) * 60.0;
	$AzMittag = sprintf( "%03.0f", round( $AzMittag, 2 ) );
	$time = sprintf( "%02d", $std ) . ":" . sprintf( "%02d", $min );

	# echo "\nObere Kulmination ";
	# echo "Mittag $time Az " . $AzMittag . "°";
	echo "Mittag $time ";

}

/*
	Mondberechnung aus Bahndaten und Koeffizienten;
*/

function BahndatenMond( $t ) {

	$x1 = getDecimalPart( 0.606434 + 0.03660110129 * $t ); // Moon
	$x2 = getDecimalPart( 0.374897 + 0.03629164709 * $t );
	$x3 = getDecimalPart( 0.259091 + 0.03674819520 * $t );
	$x4 = getDecimalPart( 0.827362 + 0.03386319198 * $t );
	$x5 = getDecimalPart( 0.347343 - 0.00014709391 * $t );
	$x8 = getDecimalPart( 0.993126 + 0.00273777850 * $t ); // Sun

	$T = julianCenturiesSince1900( $t );

	if ( DEBUG ) {
		echo sprintf( "x1: %8.5f", $x1) . "\n";
		echo sprintf( "x2: %8.5f", $x2) . "\n";
		echo sprintf( "x3: %8.5f", $x3) . "\n";
		echo sprintf( "x4: %8.5f", $x4) . "\n";
		echo sprintf( "x5: %8.5f", $x5 - 1.0 ) . "\n";
		echo sprintf( "x8: %8.5f", $x8) . "\n";
	}

	$v = 0.39558 * s( $x3 + $x5 );
	$v += 0.08200 * s( $x3 );
	$v += 0.03257 * s( $x2 - $x3 - $x5 );
	$v += 0.01092 * s( $x2 + $x3 + $x5 );
	$v += 0.00666 * s( $x2 - $x3 );
	$v -= 0.00644 * s( $x2 + $x3 - 2 * $x4 + $x5 );
	$v -= 0.00331 * s( $x3 - 2 * $x4 + $x5 );
	$v -= 0.00304 * s( $x3 - 2 * $x4 );
	$v -= 0.00240 * s( $x2 - $x3 - 2 * $x4 - $x5 );
	$v += 0.00226 * s( $x2 + $x3 );
	$v -= 0.00108 * s( $x2 + $x3 - 2 * $x4 );
	$v -= 0.00079 * s( $x3 - $x5 );
	$v += 0.00078 * s( $x3 + 2 * $x4 + $x5 );
	$v += 0.00066 * s( $x3 + $x5 - $x8 );
	$v -= 0.00062 * s( $x3 + $x5 + $x8 );
	$v -= 0.00050 * s( $x2 - $x3 - 2 * $x4 );
	$v += 0.00045 * s( 2 * $x2 + $x3 + $x5 );
	$v -= 0.00031 * s( 2 * $x2 + $x3 - 2 * $x4 + $x5 );
	$v -= 0.00027 * s( $x2 + $x3 - 2 * $x4 + $x5 + $x8 );
	$v -= 0.00024 * s( $x3 - 2 * $x4 + $x5 );
	$v -= 0.00021 * $T * s( $x3 + $x5 );
	$v += 0.00018 * s( $x3 - $x4 + $x5 );
	$v += 0.00016 * s( $x3 + 2 * $x4 );
	$v += 0.00016 * s( $x2 - $x3 - $x5 - $x8 );
	$v -= 0.00016 * s( 2 * $x2 - $x3 - $x5 );
	$v -= 0.00015 * s( $x3 - 2 * $x4 + $x8 );
	$v -= 0.00012 * s( $x2 - $x3 - 2 * $x4 + $x8 );
	$v -= 0.00011 * s( $x2 - $x3 - $x5 + $x8 );

	$u = 1.0 - 0.10828 * c( $x2 );
	$u -= 0.01880 * c( $x2 - 2 * $x4 );
	$u -= 0.01479 * c( 2 * $x4 );
	$u += 0.00181 * c( 2 * $x2 - 2 * $x4 );
	$u -= 0.00147 * c( 2 * $x2 );
	$u -= 0.00105 * c( 2 * $x4 - $x8 );
	$u -= 0.00075 * c( $x2 - 2 * $x4 + $x8 );
	$u -= 0.00067 * c( $x2 - $x8 );
	$u += 0.00057 * c( $x4 );
	$u += 0.00055 * c( $x2 + $x8 );
	$u -= 0.00046 * c( $x2 + 2 * $x4 );
	$u += 0.00041 * c( $x2 - 2 * $x3 );
	$u += 0.00024 * c( $x8 );
	$u += 0.00017 * c( 2 * $x4 + $x8 );
	$u += 0.00013 * c( $x2 - 2 * $x4 - $x8 );
	$u -= 0.00010 * c( $x2 - 4 * $x4 );

	$w = 0.10478 * s( $x2 );
	$w -= 0.04105 * s( 2 * $x3 + 2 * $x5 );
	$w -= 0.02130 * s( $x2 - 2 * $x4 );
	$w -= 0.01779 * s( 2 * $x3 + $x5 );
	$w += 0.01774 * s( $x5 );
	$w += 0.00987 * s( 2 * $x4 );
	$w -= 0.00338 * s( $x2 - 2 * $x3 - 2 * $x5 );
	$w -= 0.00309 * s( $x8 );
	$w -= 0.00190 * s( 2 * $x3 );
	$w -= 0.00144 * s( $x2 + $x5 );
	$w -= 0.00144 * s( $x2 - 2 * $x3 - $x5 );
	$w -= 0.00113 * s( $x2 + 2 * $x3 + 2 * $x5 );
	$w -= 0.00094 * s( $x2 - 2 * $x4 + $x8 );
	$w -= 0.00092 * s( 2 * $x2 - 2 * $x4 );
	$w += 0.00071 * s( 2 * $x4 - $x8 );
	$w += 0.00070 * s( 2 * $x2 );
	$w += 0.00067 * s( $x2 + 2 * $x3 - 2 * $x4 + 2 * $x5 );
	$w += 0.00066 * s( 2 * $x3 - 2 * $x4 + $x5 );
	$w -= 0.00066 * s( 2 * $x4 + $x5 );
	$w += 0.00061 * s( $x2 - $x8 );
	$w -= 0.00058 * s( $x4 );
	$w -= 0.00049 * s( $x2 + 2 * $x3 + $x5 );
	$w -= 0.00049 * s( $x2 - $x5 );
	$w -= 0.00042 * s( $x2 + $x8 );
	$w += 0.00034 * s( 2 * $x3 - 2 * $x4 + 2 * $x5 );
	$w -= 0.00026 * s( 2 * $x3 - 2 * $x4 );
	$w += 0.00025 * s( $x2 - 2 * $x3 - 2 * $x4 - 2 * $x5 );
	$w += 0.00024 * s( $x2 - 2 * $x3 );
	$w += 0.00023 * s( $x2 + 2 * $x3 - 2 * $x4 + $x5 );
	$w += 0.00023 * s( $x2 - 2 * $x4 - $x5 );
	$w += 0.00019 * s( $x2 + 2 * $x4 );
	$w += 0.00012 * s( $x2 - 2 * $x4 - $x8 );
	$w += 0.00011 * s( $x2 - 2 * $x4 + $x5 );
	$w += 0.00011 * s( $x2 - 2 * $x3 - 2 * $x4 - $x5 );
	$w -= 0.00010 * s( 2 * $x4 + $x8 );

	# echo "   Mond  " . sprintf( "t:%10.1f  v:%8.5f u:%8.5f w:%8.5f", $t, $v, $u, $w ) . "\n";

	return Winkel( "Moon", $x1 * pi2, $u, $v, $w );
}


function BahndatenSonne( $t ) {

	$x1 = getDecimalPart( 0.606434 + 0.03660110129 * $t ); // Moon
	$x2 = getDecimalPart( 0.374897 + 0.03629164709 * $t );
	$x3 = getDecimalPart( 0.259091 + 0.03674819520 * $t );
	$x4 = getDecimalPart( 0.827362 + 0.03386319198 * $t );
	$x5 = getDecimalPart( 0.347343 - 0.00014709391 * $t );
	$x7 = getDecimalPart( 0.779072 + 0.00273790931 * $t ); // Sun
	$x8 = getDecimalPart( 0.993126 + 0.00273777850 * $t );
	$x13 = getDecimalPart( 0.140023 + 0.00445036173 * $t ); // Venus
	$x16 = getDecimalPart( 0.053856 + 0.00145561327 * $t ); // Mars
	$x19 = getDecimalPart( 0.056531 + 0.00023080893 * $t ); // Jupiter

	$T = julianCenturiesSince1900( $t );

	/* Testwerte für 28.06.1969 0h */

/*
	$x1 = -0.29454;
	$x2 = -0.07736;
	$x3 = -0.28117;
	$x4 = -0.56098;
	$x5 = -0.01337;
	$x7 = -0.73356;
	$x8 = -0.51805;
*/

	if ( DEBUG ) {
		echo sprintf( "x1: %8.5f", $x1) . "\n";
		echo sprintf( "x2: %8.5f", $x2) . "\n";
		echo sprintf( "x3: %8.5f", $x3) . "\n";
		echo sprintf( "x4: %8.5f", $x4) . "\n";
		echo sprintf( "x5: %8.5f", $x5 - 1.0) . "\n";
		echo sprintf( "x7: %8.5f", $x7) . "\n";
		echo sprintf( "x8: %8.5f", $x8) . "\n";
	}

	$v = 0.39785 * s( $x7 );
	$v -= 0.01000 * s( $x7 - $x8 );
	$v += 0.00333 * s( $x7 + $x8 );
	$v -= 0.00021 * $T * s( $x7 );
	$v += 0.00004 * s( $x7 + 2 * $x8 );
	$v -= 0.00004 * c( $x7 );
	$v -= 0.00004 * s( $x5 - $x7 );
	$v += 0.00003 * $T * s( $x7 - $x8 );

	$u = 1.0 - 0.03349 * c( $x8 );
	$u -= 0.00014 * c( 2.0 * $x8 );
	$u += 0.00008 * $T * c( $x8 );
	$u -= 0.00003 * s( $x8 - $x19 );

	$w = -0.00010 - 0.04129 * s( 2.0 * $x7 );
	$w += 0.03211 * s( $x8 );
	$w += 0.00104 * s( 2.0 * $x7 - $x8 );
	$w -= 0.00035 * s( 2.0 * $x7 + $x8);
	$w -= 0.00008 * $T * s( $x8 );
	$w -= 0.00008 * s( $x5 );
	$w += 0.00007 * s(2 * $x8 );
	$w += 0.00005 * $T * s( 2.0 * $x7 );
	$w += 0.00003 * s( $x1 - $x7 );
	$w -= 0.00002 * c( $x8 - $x19 );
	$w += 0.00002 * s( 4 * $x8 - 8 * $x16 + 3 * $x19 );
	$w -= 0.00002 * s( $x8 - $x13 );
	$w -= 0.00002 * c( 2 * $x8 - 2 * $x13 );

	# echo "   Sonne " . sprintf( "t:%10.1f  v:%8.5f u:%8.5f w:%8.5f", $t, $v, $u, $w ) . "\n";

	return Winkel( "Sun", $x7 * pi2, $u, $v, $w );
}


/*
	Berechne Refraktion, Radius, Declination;
*/
 
function Winkel( $obj, $L, $u, $v, $w ) {

	$s = $w / sqrt( $u - $v * $v );
	$alpha = $L + atan( $s / sqrt( 1.0 - $s * $s ) );

	$s = $v / sqrt( $u );

	$delta = atan( $s / sqrt( 1.0 - $s * $s ) );

/*	Why not this? (according to Van Flandern/Pulkkinen)
	$alpha = $L + atan( $w / sqrt( $u - $v * $v ) );
	$delta = atan( $v / sqrt( $u ) );
*/

	if ( $obj === "Moon" ) {
		$scalingFactor = 60.40974;
	}
	if ( $obj === "Sun" ) {
		$scalingFactor = 1.00021;
	}

	$rho = $scalingFactor * sqrt( $u );

	// echo "alpha $alpha delta $delta rho $rho\n";
	return array( $alpha, $delta, $rho );
}


/* Zustandsausgabe für Sonne und Mond */

function calcVisibility( $obj, $m8, $w8, $v2 ) {

	$aufgang = "";
	$untergang = "";
	$sichtbar = "";

	$objDE = ( $obj === "Moon" ) ? "Mond" : "Sonne";

	if ( ( $m8 === 0 ) && ( $w8 === 0 ) ) {

		$sichtbar = ( $v2 > 0.0 ) ? "$objDE ganztägig sichtbar" : ( ( $v2 < 0.0 ) ? "$objDE ganztätig unsichtbar" : "" );

	} else {

		if ( $m8 === 0 ) {

			$aufgang = "Kein ${objDE}aufgang an diesem Tag.\n";

		}

		if ( $w8 === 0 ) {

		$untergang = "Kein ${objDE}untergang an diesem Tag.\n";

		}

	}

	echo $sichtbar;
	echo $aufgang;
	echo $untergang;

}


/*
	Source: https://computus.de/mondphase/mondphase.htm (Javascript)

	Der Algorithmus zur Mondphasenberechnung stammt aus dem Buch
	"Astronomische Algorithmen" von Jean Meeus
	Verlag Johann Ambrosius Barth
	Leipzig, Berlin, Heidelberg 2. Auflage 1994.
	ISBN 3-335-00400-0

*/

/* return Unix Timestamp also for decimal Julian dates
   PHP's built-in jdtounix does only work for non-decimal Julian dates
*/
function jd2unix( $jd ) {
	return ( $jd - 2440587.5) * 86400;
}

function CS( $x ) {
	return cos( $x * grad );
}
function SN( $x ) {
	return sin( $x * grad );
}
  
function Var_o( $k, $t ) {
	return 124.7746 - 1.5637558 * $k + .0020691 * $t * $t + .00000215 * $t * $t * $t;
}
function Var_f( $k, $t ) {
	return 160.7108 + 390.67050274 * $k - .0016341 * $t * $t - .00000227 * $t * $t * $t + .000000011 * $t * $t * $t * $t;
}
function Var_m1( $k, $t) {
	return 201.5643 + 385.81693528 * $k + .1017438 * $t * $t + .00001239 * $t * $t * $t - .000000058 * $t * $t * $t * $t;
  }
function Var_m( $k, $t ) {
	return 2.5534 + 29.10535669 * $k - .0000218 * $t * $t - .00000011 * $t * $t * $t;
}
function Var_e( $t ) {
	return 1 - .002516 * $t - .0000074 * $t * $t;
}
function Var_JDE( $k, $t ) {
	return 2451550.09765 + 29.530588853 * $k + .0001337 * $t * $t - .00000015 * $t * $t * $t + .00000000073 * $t * $t * $t * $t;
}
function Var_k( $tz, $zeit ) {
    $startday = getDate( jdtounix( $zeit ) );
    return ( $startday['year'] + ( ( $startday['mon'] - 1 ) * 30.4 + $startday['mday'] + $tz) / 365 - 2000) * 12.3685;
}
  
function Korrektur( $JDE, $t, $k ) {
    //Zusätzliche Korrekturen
    $JDE += .000325 * SN( 299.77 + .107408 * $k - .009173 * $t * $t ) + .000165 * SN( 251.88 + .016321 * $k ) + .000164 * SN( 251.83 + 26.651886 * $k ) + .000126 * SN( 349.42 + 36.412478 * $k ) + .00011 * SN( 84.66 + 18.206239 * $k );
    $JDE += .000062 * SN( 141.74 + 53.303771 * $k ) + .00006 * SN( 207.14 + 2.453732 * $k ) + .000056 * SN( 154.84 + 7.30686 * $k ) + .000047 * SN( 34.52 + 27.261239 * $k ) + .000042 * SN( 207.19 + .121824 * $k ) + .00004 * SN( 291.34 + 1.844379 * $k );
    $JDE += .000037 * SN( 161.72 + 24.198154 * $k ) + .000035 * SN( 239.56 + 25.513099 * $k ) + .000023 * SN( 331.55 + 3.592518 * $k );
    return $JDE;
}
  
function Vollmond( $k ) {

	$k = floor( $k ) + .5;
    $t = $k / 1236.85;

    $e = Var_e( $t );
    $m = Var_m( $k, $t );
    $m1 = Var_m1( $k, $t );
    $f = Var_f( $k, $t );
    $o = Var_o( $k, $t );

    //Vollmondkorrekturen
    $JDE = Var_JDE( $k, $t );
    $JDE += -.40614 * SN( $m1 ) + .17302 * $e * SN( $m ) + .01614 * SN( 2 * $m1 ) + .01043 * SN( 2 * $f ) + .00734 * $e * SN( $m1 - $m ) - .00515 * $e * SN( $m1 + $m ) + .00209 * $e * $e * SN( 2 * $m ) - .00111 * SN( $m1 - 2 * $f ) - .00057 * SN( $m1 + 2 * $f);
    $JDE += .00056 * $e * SN( 2 * $m1 + $m ) - .00042 * SN( 3 * $m1 ) + .00042 * $e * SN( $m + 2 * $f ) + .00038 * $e * SN( $m - 2 * $f) - .00024 * $e * SN( 2 * $m1 - $m ) - .00017 * SN( $o ) - .00007 * SN( $m1 + 2 * $m) + .00004 * SN( 2 * $m1 - 2 * $f);
    $JDE += .00004 * SN( 3 * $m ) + .00003 * SN( $m1 + $m - 2 * $f ) + .00003 * SN( 2 * $m1 + 2 * $f) - .00003 * SN( $m1 + $m + 2 * $f) + .00003 * SN( $m1 - $m + 2 * $f) - .00002 * SN( $m1 - $m - 2 * $f) - .00002 * SN( 3 * $m1 + $m );
    $JDE += .00002 * SN( 4 * $m1 );
    return Korrektur( $JDE, $t, $k );
}

function Finsternis( $k, $Typ, $Modus ) {

	/*	Typ:   .5:	Mondfinsternis 
				0:	Sonnenfinsternis

		Modus:	0:	Finsternis partiell
				1:	Finsternis total
				2:	Sonnenfinsternis ringförmig
	*/

	$k = floor( $k ) + $Typ;
    $t = $k / 1236.85;
    $f = Var_f( $k, $t );
    $JDE = 0;
    $Ringtest = 0;

    if ( SN( abs( $f ) ) <= .36 ) {

		$o = Var_o( $k, $t );
		$f1 = $f - .02665 * SN( $o );
		$a1 = 299.77 + .107408 * $k - .009173 * $t * $t;
		$e = Var_e( $t );
		$m = Var_m( $k, $t );
		$m1 = Var_m1( $k, $t );
		$p = .207 * $e * SN( $m ) + .0024 * $e * SN( 2 * $m ) - .0392 * SN( $m1 ) + .0116 * SN( 2 * $m1 ) - .0073 * $e * SN( $m1 + $m ) + .0067 * $e * SN( $m1 - $m ) + .0118 * SN( 2 * $f1);
		$q = 5.2207 - .0048 * $e * CS( $m ) + .002 * $e * CS( 2 * $m ) - .3299 * CS( $m1 ) - .006 * $e * CS( $m1 + $m) + .0041 * $e * CS( $m1 - $m );
		$g = ( $p * CS( $f1 ) + $q * SN( $f1 ) ) * ( 1 - .0048 * CS( abs( $f1 ) ) );
		$u = .0059 + .0046 * $e * CS( $m ) - .0182 * CS( $m1 ) + .0004 * CS( 2 * $m1 ) - .0005 * CS( $m + $m1 );
		$JDE = Var_JDE( $k, $t );

		if ( $Typ ) {
			$JDE += - .4065 * SN( $m1 ) + .1727 * $e * SN( $m );
		} else {
			$JDE += - .4075 * SN( $m1 ) + .1721 * $e * SN( $m );
		}

		$JDE += .0161 * SN( 2 * $m1 ) - .0097 * SN( 2 * $f1 ) + .0073 * $e * SN( $m1 - $m ) - .005 * $e * SN( $m1 + $m ) - .0023 * SN( $m1 - 2 * $f1 ) + .0021 * $e * SN( 2 * $m );
		$JDE += .0012 * SN( $m1 + 2 * $f1 ) + .0006 * $e * SN( 2 * $m1 + $m) - .0004 * SN( 3 * $m1 ) - .0003 * $e * SN( $m + 2 * $f1 ) + .0003 * SN( $a1 ) - .0002 * $e * SN( $m - 2 * $f1 ) - .0002 * $e * SN( 2 * $m1 - $m) - .0002 * SN( $o );

		if ( $Typ ) {

			if ( ( 1.0248 - $u - abs($g) ) / .545 <= 0 ) {
				$JDE = 0; // keine Mf
			}

			if ( $Modus == 0 && ( 1.0128 - $u - abs( $g ) ) / .545 > 0 && ( .4678 - $u ) * ( .4678 - $u ) - $g * $g > 0 ) {
			$JDE = 0; // keine partielle Mf
			}

			if ( $Modus == 1 && ( ( 1.0128 - $u - abs( $g ) ) / .545 <= 0 != ( .4678 - $u ) * ( .4678 - $u ) - $g * $g <= 0 ) ) {
				$JDE = 0; // keine totale Mf
			}
	
		} else {

			if ( abs( $g ) > 1.5433 + $u) {
				$JDE = 0; // keine SF
			}

			if ( $Modus == 0 && ( ( $g >= -.9972 && $g <= .9972 ) || ( abs( $g ) >= .9972 && abs( $g ) < .9972 + abs( $u ) ) )) {
				$JDE = 0; // keine partielle Sf
			}

			if ( $Modus > 0 ) {

				if ( ( $g < -.9972 || $g > .9972 ) || ( abs( $g ) < .9972 && abs( $g ) > .9972 + abs( $u ) ) ) {
					$JDE = 0; // keine ringförmige oder totale SF
				}

				if ( $u > .0047 || $u >= .00464 * sqrt( 1 - $g * $g ) ) {
					$Ringtest = 1; // keine totale Sf
				}

				if ( $Ringtest == 1 && $Modus == 1 ) {
					$JDE = 0;
				}

				if ( $Ringtest == 0 && $Modus == 2 ) {
					$JDE = 0;
				}
			
			}

      }

	}

    return $JDE;

}


function checkMFtime( $mfTime, $vmTime ) {
	return abs( $mfTime - $vmTime ) < 0.5;
}

function NaechsterVM( $zeit ) {
    $tz = 0;

    do {

		$k = Var_k( $tz, $zeit );
		$tz += 1;

	} while ( ( $vm = Vollmond( $k ) ) < $zeit );

    return $vm;
}

function NaechsteMF( $zeit, $Typ ) {
	$tz = 0;

	do {

		$k = Var_k( $tz, $zeit );
		$tz += 1;
    
    }

	while ( ( $mf = Finsternis( $k, .5, $Typ ) ) < $zeit );

	return $mf;
}
