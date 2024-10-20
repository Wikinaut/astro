Python program to calculate Sun/Moon rise/set times and azimuths from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset). Lunar eclipses are indicated.

```ma.py``` Mondaufgang (ma)

### Example

```
./ma.py 
Gib den Namen des Ortes oder die Geokoordinaten (lat, lon) ein: Berlin, Drachenberg

Ort: Drachenberg, Teufelsseechaussee, Grunewald, Charlottenburg-Wilmersdorf, Berlin, 14193, Deutschland
Ort (reverse geo; English): Drachenberg, Teufelsseechaussee, Grunewald, Charlottenburg-Wilmersdorf, Berlin, 14193, Germany

geo:52.5023685,13.2482422
https://www.openstreetmap.org/?mlat=52.5023685&mlon=13.2482422#map=14/52.5023685/13.2482422

Daten für den aktuellen Tag:
MA So 20.10.2024 19:04:11 CEST Az 042°
SU Mo 21.10.2024 17:58:45 CEST Az 253°

Die Vollmonddaten für die nächsten 2 Monate:
Fr 15.11.2024 22:28:28 CET
So 15.12.2024 10:01:38 CET

Zeiten für Mondaufgang und Sonnenuntergang um Vollmond:
Fr 15.11.2024 MA 15:31:21 CET Az 054° SU 16:13:44 CET Az 240°
Fr 15.11.2024 Vollmond 22:28:28 CET
Sa 16.11.2024 MA 15:58:03 CET Az 045° SU 16:12:20 CET Az 239°
So 17.11.2024 MA 16:38:03 CET Az 040° SU 16:10:59 CET Az 239°

Sa 14.12.2024 MA 14:27:34 CET Az 042° SU 15:53:15 CET Az 231°
So 15.12.2024 Vollmond 10:01:38 CET
So 15.12.2024 MA 15:16:22 CET Az 039° SU 15:53:22 CET Az 231°
Mo 16.12.2024 MA 16:23:37 CET Az 040° SU 15:53:33 CET Az 231°
```

### Outdated 

Outdated is the older PHP program su.php:

PHP program to calculate Sun/Moon rise/set times and azimuths, and also Lunar eclipses from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset).

Defunct: local time determination via an external web service api.teleport.org, as this web service is no longer working.
(was explained in https://stackoverflow.com/questions/16086962/how-to-get-a-time-zone-from-a-location-using-latitude-and-longitude-coordinates/32437518#32437518 and https://stackoverflow.com/a/32437518/731798)
