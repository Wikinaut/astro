Python program to calculate Sun/Moon rise/set times and azimuths from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset).

```ma.py``` Mondaufgang (ma)


### To do

- [ ] add calculation of Lunar exclipses to ma.py


### Example

```
./ma.py 
Gib den Namen des Ortes oder die Geokoordinaten (lat, lon) ein: berlin

Ort: Berlin, Deutschland
Ort (reverse geo; English): 48, Leipziger Straße, Friedrichswerder, Mitte, Berlin, 10117, Germany

geo:52.510885,13.3989367
https://www.openstreetmap.org/?mlat=52.510885&mlon=13.3989367#map=14/52.510885/13.3989367

Daten für den aktuellen Tag:
MA So 20.10.2024 19:03:30 CEST Az 042°
SU So 20.10.2024 18:00:15 CEST Az 253°

Die nächsten Vollmonddaten:
Fr 15.11.2024 21:28:28 CET
So 15.12.2024 09:01:38 CET
Mo 13.01.2025 22:26:51 CET

Zeiten für Mondaufgang und Sonnenuntergang um Vollmond:
Do 14.11.2024 CET MA 15:30:42 CET Az 054° SU 16:13:05 CET Az 240°
Fr 15.11.2024 21:28:28 CET Vollmond
Fr 15.11.2024 CET MA 15:57:22 CET Az 045° SU 16:11:42 CET Az 239°
Sa 16.11.2024 CET MA 16:37:21 CET Az 040° SU 16:10:21 CET Az 239°

Sa 14.12.2024 CET MA 14:26:53 CET Az 042° SU 15:52:36 CET Az 231°
So 15.12.2024 09:01:38 CET Vollmond
So 15.12.2024 CET MA 15:15:41 CET Az 039° SU 15:52:43 CET Az 231°
Mo 16.12.2024 CET MA 16:22:55 CET Az 040° SU 15:52:54 CET Az 231°

So 12.01.2025 CET MA 15:16:43 CET Az 042° SU 16:19:50 CET Az 235°
Mo 13.01.2025 22:26:51 CET Vollmond
Mo 13.01.2025 CET MA 16:39:52 CET Az 049° SU 16:21:24 CET Az 235°
Di 14.01.2025 CET MA 18:03:27 CET Az 058° SU 16:23:00 CET Az 235°
```

### Outdated 

Outdated is the older PHP program su.php:

PHP program to calculate Sun/Moon rise/set times and azimuths, and also Lunar eclipses from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset).

Defunct: local time determination via an external web service api.teleport.org, as this web service is no longer working.
(was explained in https://stackoverflow.com/questions/16086962/how-to-get-a-time-zone-from-a-location-using-latitude-and-longitude-coordinates/32437518#32437518 and https://stackoverflow.com/a/32437518/731798)
