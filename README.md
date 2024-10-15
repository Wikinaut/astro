Python program to calculate Sun/Moon rise/set times and azimuths from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset).

```ma.py``` Mondaufgang (ma)


### To do

- [ ] add calculation of Lunar exclipses to ma.py


### Example

```
./ma.py
Gib den Namen des Ortes oder die Geokoordinaten (lat, lon) ein: berlin, drachenberg

Ort: Drachenberg, Teufelsseechaussee, Grunewald, Charlottenburg-Wilmersdorf, Berlin, 14193, Deutschland
Ort (reverse geo; English): Drachenberg, Teufelsseechaussee, Grunewald, Charlottenburg-Wilmersdorf, Berlin, 14193, Germany

geo:52.5023685,13.2482422
https://www.openstreetmap.org/?mlat=52.5023685&mlon=13.2482422#map=14/52.5023685/13.2482422

Daten für den aktuellen Tag:
MA Di 15.10.2024 15:29:48 CEST Az 094°
SU Di 15.10.2024 16:11:42 CEST Az 257°

Die nächsten Vollmonddaten:
Do 17.10.2024 11:26:21 CEST
Fr 15.11.2024 21:28:28 CET
So 15.12.2024 09:01:38 CET
Mo 13.01.2025 22:26:51 CET
Mi 12.02.2025 13:53:19 CET

Zeiten für Mondaufgang und Sonnenuntergang um Vollmond:
Mi 16.10.2024 CEST MA 17:41:23 CEST Az 082° SU 18:09:30 CEST Az 256°
Do 17.10.2024 CEST MA 17:54:14 CEST Az 070° SU 18:07:19 CEST Az 255°
Fr 18.10.2024 CEST MA 18:10:14 CEST Az 059° SU 18:05:09 CEST Az 255°
Do 14.11.2024 CET MA 15:31:21 CET Az 054° SU 16:13:44 CET Az 240°
Fr 15.11.2024 CET MA 15:58:03 CET Az 045° SU 16:12:20 CET Az 239°
Sa 16.11.2024 CET MA 16:38:03 CET Az 040° SU 16:10:59 CET Az 239°
Sa 14.12.2024 CET MA 14:27:34 CET Az 042° SU 15:53:15 CET Az 231°
So 15.12.2024 CET MA 15:16:22 CET Az 039° SU 15:53:22 CET Az 231°
Mo 16.12.2024 CET MA 16:23:37 CET Az 040° SU 15:53:33 CET Az 231°
So 12.01.2025 CET MA 15:17:25 CET Az 043° SU 16:20:28 CET Az 235°
Mo 13.01.2025 CET MA 16:40:33 CET Az 049° SU 16:22:02 CET Az 235°
Di 14.01.2025 CET MA 18:04:07 CET Az 058° SU 16:23:39 CET Az 235°
Di 11.02.2025 CET MA 15:42:07 CET Az 054° SU 17:12:53 CET Az 248°
Mi 12.02.2025 CET MA 17:03:23 CET Az 064° SU 17:14:47 CET Az 249°
Do 13.02.2025 CET MA 18:21:08 CET Az 074° SU 17:16:41 CET Az 249°
```

### Outdated 

Outdated is the older PHP program su.php:

PHP program to calculate Sun/Moon rise/set times and azimuths, and also Lunar eclipses from latitude/longitude, or worldwide name/address data.
Calculated times are output in UTC and/or in local time (with correct day light saving time offset).

Defunct: local time determination via an external web service api.teleport.org, as this web service is no longer working.
(was explained in https://stackoverflow.com/questions/16086962/how-to-get-a-time-zone-from-a-location-using-latitude-and-longitude-coordinates/32437518#32437518 and https://stackoverflow.com/a/32437518/731798)
