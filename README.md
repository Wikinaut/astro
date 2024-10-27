Python program to calculate Sun/Moon rise/set times and azimuths from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset). Lunar eclipses are indicated.

```ma.py``` Mondaufgang (ma)

### Example

```
/ma.py 
Gib den Namen des Ortes oder die Geokoordinaten (lat, lon) ein: berlin

Ort: Berlin, Deutschland
Ort (reverse geo; English): 48, Leipziger Straße, Friedrichswerder, Mitte, Berlin, 10117, Germany

geo:52.510885,13.3989367
https://www.openstreetmap.org/?mlat=52.510885&mlon=13.3989367#map=14/52.510885/13.3989367
https://www.timeanddate.com/moon/@52.510885,13.3989367

Local time für Mitternacht des aktuellen Tages: So 27.10.2024 00:00:00 CEST
UTC-Wert für Mitternacht des aktuellen Tages: Sa 26.10.2024 22:00:00 UTC

Daten für den Mondaufgang des heutigen Tages:
MA So 27.10.2024 02:03:44 CEST Az 069°
SU So 27.10.2024 16:45:50 CET Az 249°

Die Vollmonddaten für die nächsten 2 Monate:
Fr 15.11.2024 22:28:28 CET
So 15.12.2024 10:01:38 CET

Zeiten für Mondaufgang und Sonnenuntergang um Vollmond:
Fr 15.11.2024 MA 15:30:42 CET Az 054° SU 16:13:05 CET Az 240°
Fr 15.11.2024 Vollmond 22:28:28 CET
Sa 16.11.2024 MA 15:57:22 CET Az 045° SU 16:11:42 CET Az 239°
So 17.11.2024 MA 16:37:21 CET Az 040° SU 16:10:21 CET Az 239°

Sa 14.12.2024 MA 14:26:53 CET Az 042° SU 15:52:36 CET Az 231°
So 15.12.2024 Vollmond 10:01:38 CET
So 15.12.2024 MA 15:15:41 CET Az 039° SU 15:52:43 CET Az 231°
Mo 16.12.2024 MA 16:22:55 CET Az 040° SU 15:52:54 CET Az 231°
```

### Outdated 

Outdated is the older PHP program su.php:

PHP program to calculate Sun/Moon rise/set times and azimuths, and also Lunar eclipses from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset).

Defunct: local time determination via an external web service api.teleport.org, as this web service is no longer working.
(was explained in https://stackoverflow.com/questions/16086962/how-to-get-a-time-zone-from-a-location-using-latitude-and-longitude-coordinates/32437518#32437518 and https://stackoverflow.com/a/32437518/731798)
