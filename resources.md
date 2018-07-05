The PHP program calculates the Sun and Moon rise and set times along with the azimuths according to the publication:

	Low-precision formulae for planetary positions
	Fundamental arguments (Van Flandern & Pulkkinen, 1979)
	https://doi.org/10.1086/190623

Resources:

* https://doi.org/10.1086/190623 = http://adsabs.harvard.edu/abs/1979ApJS...41..391V Low-precision formulae for planetary positions
* http://www.stargazing.net/kepler/sunrise.html
* http://conga.oan.es/~alonso/doku.php?id=blog:sun_moon_position
* https://github.com/gregseth/suncalc-php/blob/master/suncalc.php
* http://aa.quae.nl/en/reken/zonpositie.html
* https://web.archive.org/web/20161202180207/http://williams.best.vwh.net/sunrise_sunset_algorithm.htm
* http://www.stjarnhimlen.se/comp/ppcomp.html
* ftp://ssd.jpl.nasa.gov/pub/eph/planets/
* https://lexikon.astronomie.info/java/sunmoon/
* http://www.stargazing.net/kepler/newlink.html High Precision Ephemerides
* http://www.stargazing.net/kepler/sunrise.html Approximate Astronomical Positions Sun rise and set, and twilight
* https://heavens-above.com/
* http://aa.usno.navy.mil/data/index.php
* http://aa.usno.navy.mil/cgi-bin/aa_rstablew.pl?ID=AA&year=2018&task=1&place=Berlin-Drachenberg&lon_sign=1&lon_deg=13.25&lat_sign=1&lat_deg=52.5
* https://computus.de/mondphase/mondphase.htm Javascript
* https://de.wikibooks.org/wiki/Astronomische_Berechnungen_f%C3%BCr_Amateure/_Zeit/_Zeitrechnungen
* http://www.nabkal.de/akzel.html Onlinerechner
* https://homepage.univie.ac.at/Georg.Zotti/hp/urania/meeuserr.html Errata Jean Meeus: Astronomische Algorithmen, 2. Auflage, 1994
* https://github.com/solarissmoke/php-moon-phase/blob/master/Solaris/MoonPhase.php
* https://github.com/ypid/suncalc-php PHP. Calculate sun position, sunlight phases, moon position and lunar phase. It is based on the JavaScript implementation created by Vladimir Agafonkin (@mourner) as a part of the SunCalc.net project.
* http://conga.oan.es/~alonso/doku.php?id=blog:sun_moon_position Accurate and fast Sun/Moon ephemerides suitable for Android (and iOS) projects
* https://github.com/mourner/suncalc/issues/106 Moonrise time 4 min deviation · Issue #106 · mourner/suncalc

More resources will be added later.

A different PHP version can be found here https://gitlab.com/snippets/1729624 .

Example 1:
```
php su.php

Nördl. Breite          ±dd.dddd°  [52.5°]:52.5
Östliche Länge        ±ddd.dddd° [13.25°]:13.25
Zeitzone ±h [UTC+1 (Zeitzone nach Länge)]:2
Zeitzone nach Länge:   UTC+1
Zeitzone nach Eingabe: UTC+2
Datum       yyyy.mmdd [2018.0701 (heute)]:2018.0701

01.07.2018 Breite: 52.5 Länge: 13.25 Zeitzone: UTC+2
SA 04:48 Az 048° SU 21:33 Az 312° 
Obere Kulmination Mittag 13:10 Az 179.96°
MU 07:51 Az 240° MA 23:28 Az 117°
```

Example 2:
```
php su.php

Nördl. Breite          ±dd.dddd°  [52.5°]:52.5
Östliche Länge        ±ddd.dddd° [13.25°]:13.25
Zeitzone ±h [UTC+1 (Zeitzone nach Länge)]:2
Zeitzone nach Länge:   UTC+1
Zeitzone nach Eingabe: UTC+2
Datum       yyyy.mmdd [2018.0701 (heute)]:2018.0615
15.06.2018 Breite: 52.5 Länge: 13.25 Zeitzone: UTC+2
SA 04:44 Az 048° SU 21:32 Az 312° 
Obere Kulmination Mittag 13:07 Az 180.03°
MA 06:34 Az 055° MU 23:04 Az 304°
```
