#!/usr/bin/env python3

"""

Programm zur Berechnung der Mondaufgangszeiten an Tagen der kommenden Vollmonde

20241014 initial

Die Basis dieses Programm entstand durch zwei Anfragen bei ChatgPT.

Schreibe ein Pythonprogramm

- mit Eingabe von Geokoordinaten oder Namen des Ortes via Nominatim und
- Ausgabe der Mondaufgangszeit, des Azimuts des Mondes zur Mondaufgangszeit,
- der Sonnenuntergangszeit und des Azimuts der Sonne zur Untergangszeit und
- Ausgabe mit der Zeit der aktuellen Timezone des Ortes, und
- zur Berechnung der nächsten Vollmonds.
- Zusätzlich berechne Mondaufgangszeit, Mondaufgangsazimut, Sonnenuntergangzeit, Sonnenuntergangsazimut am Vollmondtag, am Tag davor und danach.

- Schreibe ein Pythonprogramm, das zu einem Ort die lokale Zeitzone und den Offset zu UTC berechnet,
- den richtigen user_agent setzt
- und die Zeitzone Abbreviation (Beispiel: CEST, EEST usw.) ausgibt.

Hinweise:

Eingabe: Das Programm akzeptiert entweder den Namen eines Ortes (z.B. "Berlin") oder Geokoordinaten im Format "lat, lon" (z.B. "52.5200, 13.4050").
Vollmond: Das Programm sucht Vollmonddaten in einem Zeitraum von x Tagen nach dem aktuellen Datum.
Ausgabe: Das Programm gibt die Zeiten für Mondaufgang, Sonnenuntergang, die Azimute für den aktuellen Tag als auch für den Tag vor, am und nach jedem Vollmond aus.
Zeitzone: Die Zeitzone wird basierend auf den eingegebenen Koordinaten ermittelt.

Stelle sicher, dass du die folgenden Pakete installiert hast:
pip install pytz ephem timezonefinder geopy

"""

MONTHS=2

import pytz
import ephem
import locale
import re
from geopy.geocoders import Nominatim
from datetime import datetime,timedelta
from timezonefinder import TimezoneFinder

# colors
BLACK = '\033[30m'
RED = '\033[31m'
GREEN = '\033[32m'
YELLOW = '\033[33m' # orange on some systems
BLUE = '\033[34m'
MAGENTA = '\033[35m'
CYAN = '\033[36m'
LIGHT_GRAY = '\033[37m'
DARK_GRAY = '\033[90m'
BRIGHT_RED = '\033[91m'
BRIGHT_GREEN = '\033[92m'
BRIGHT_YELLOW = '\033[93m'
BRIGHT_BLUE = '\033[94m'
BRIGHT_MAGENTA = '\033[95m'
BRIGHT_CYAN = '\033[96m'
WHITE = '\033[97m'
RESET = '\033[0m' # called to return to standard terminal text color
# print(BLACK + "black" + RESET)

def get_astronomical_info(observer):
    # Mondaufgang und Sonnenuntergang berechnen
    moonrise = observer.next_rising(ephem.Moon())
    sunset = observer.next_setting(ephem.Sun())

    # Azimut des Mondes zur Mondaufgangszeit
    observer.date = moonrise
    moon = ephem.Moon(observer)
    moon_azimuth = moon.az * (180.0 / ephem.pi)  # Umwandlung von Radiant in Grad

    # Azimut der Sonne zur Untergangszeit
    observer.date = sunset
    sun = ephem.Sun(observer)
    sun_azimuth = sun.az * (180.0 / ephem.pi)  # Umwandlung von Radiant in Grad

    return moonrise, moon_azimuth, sunset, sun_azimuth


def get_full_moon_dates(start_date, end_date):
    full_moons = []

    date = start_date

    while date <= end_date:
      nextfull = ephem.next_full_moon(date).datetime()
      full_moons.append(nextfull)
      date = nextfull

    return full_moons


def is_lunar_eclipse(fullmoon):
      global observer

      # check for lunar eclipse that fullmoon date
      # https://gist.github.com/priyadi/e5322666248ee22a81d8e56e84fe1bcb
      observer.date = fullmoon

      # compute the position of the sun and the moon with respect to the observer
      moon = ephem.Moon()
      sun = ephem.Sun()

      moon.compute(observer)
      sun.compute(observer)

      # calculate the separation between the moon and the sun, convert
      # it from radians to degrees, subtract it by 180°.
      # this is basically the separation of the moon from the Earth's
      # center of umbra.
      sep = abs((float(ephem.separation(moon, sun)) / 0.01745329252) - 180)

      # eclipse occurs if the separation is less than 1.5°.
      # this should detect all total and partial eclipses, but is
      # hit-and-miss for penumbral eclipses.
      # the number is hardcoded for simplicity. for accuracy it should
      # however be computed from the distance to the Sun and the Moon.

      # print(f"{fullmoon.strftime('%Y/%m/%d %H:%M:%S')} Mondfinsternis Distance: {sep}")

      if sep < 1.5:
          ret = True
      else:
          ret = False

      return ret


def get_times_for_full_moon_dates(observer, lat, lon, full_moon_dates):
    results = []
    for full_moon in full_moon_dates:
        observer.date = full_moon

        # Berechne Mondaufgang und Sonnenuntergang am Tag des Vollmonds
        moonrise, moon_azimuth, sunset, sun_azimuth = get_astronomical_info(observer)

        # Berechne Zeiten für die Tage vor, am und nach dem Vollmond
        for delta in [-1, 0, 1]:  # Vorheriger, Vollmondtag und nächster Tag
            observer.date = full_moon + timedelta(days=delta)
            moonrise_day, moon_azimuth_day, sunset_day, sun_azimuth_day = get_astronomical_info(observer)

            results.append({
                'date': full_moon + timedelta(days=delta),
                'moonrise': moonrise_day.datetime(),
                'moon_azimuth': moon_azimuth_day,
                'sunset': sunset_day.datetime(),
                'sun_azimuth': sun_azimuth_day,
                'text': False
            })

        results.append({
                'date': full_moon + timedelta(days=2),
                'text': " "
            })

    return results


def print_result( result ):
   global local_tz
   text = result['text']

   if not text:

       moonrise_local = localtime(result['moonrise'])
       sunset_local = localtime(result['sunset'])

       print(f"{moonrise_local.strftime('%a %d.%m.%Y MA %H:%M:%S %Z')} "
             f"Az {round(result['moon_azimuth'], 0):0>3.0f}° "
             f"{sunset_local.strftime('SU %H:%M:%S %Z')} "
             f"Az {round(result['sun_azimuth'], 0):0>3.0f}°")

   elif text == "Vollmond":

       if is_lunar_eclipse(result['date']):
           print(f"{BRIGHT_RED}{localtime(result['date']).strftime('%a %d.%m.%Y *** Mondfinsternis *** %H:%M:%S %Z')}{RESET}")
       else:
           print(f"{localtime(result['date']).strftime('%a %d.%m.%Y Vollmond %H:%M:%S %Z')}")

   else:

      print()

   return


def localtime(utc_time):
    global lat, lon, local_tz

    # Wandle die UTC-Zeit in die lokale Zeit um
    utc_dt = utc_time.replace(tzinfo=pytz.utc)
    local_time = utc_dt.astimezone(local_tz)

    # Informationen zur Zeitzone
    # timezone_name = local_tz.zone
    # timezone_abbreviation = local_time.strftime('%Z')
    # utc_offset = local_time.utcoffset().total_seconds() / 3600

    return local_time


def main():
    global lat, lon, local_tz, observer

    locale.setlocale(locale.LC_ALL, '')
    # Geolocator mit User-Agent (irgendein Name unseres Programms)
    geolocator = Nominatim(user_agent="moon_sun_info_app")

    location_name = input("Gib den Namen des Ortes oder die Geokoordinaten (lat, lon) ein: ")

    try:

        # Wenn der Benutzer Koordinaten eingab
        if re.match(r'^[0-9., -]+$', location_name):
            lat, lon = map(float, location_name.split(','))
            location = geolocator.reverse((lat, lon), exactly_one=True, language='en')
        else:

            if location_name == "":
                location_name = "Berlin, Drachenberg"

            location = geolocator.geocode(location_name)
            if location:
                location_reverse = geolocator.reverse((location.latitude, location.longitude), exactly_one=True, language='en')

        if location:
            lat, lon, address = location.latitude, location.longitude, location.address
        else:
            raise ValueError("Ort nicht gefunden.")

        try:
            address_full = location_reverse.address
        except:
            address_full = address
            address = False

        observer = ephem.Observer()
        observer.lat = str(lat)
        observer.lon = str(lon)
        observer.elevation = 0
        observer.date = datetime.utcnow()

        moonrise, moon_azimuth, sunset, sun_azimuth = get_astronomical_info(observer)

        # Finde die Zeitzone des Ortes basierend auf den Koordinaten
        tf = TimezoneFinder()
        timezone_str = tf.timezone_at(lat=lat, lng=lon)

        if timezone_str is None:
           raise ValueError("Keine gültige Zeitzone für die angegebenen Koordinaten gefunden.")

        # Erstelle ein Zeitzonen-Objekt
        local_tz = pytz.timezone(timezone_str)

        moonrise_local = localtime(moonrise.datetime())
        sunset_local = localtime(sunset.datetime())

        print()

        if address:
            print(f"Ort: {address}")
        if address_full:
            print(f"Ort (reverse geo; English): {address_full}")

        print()

        print(f"geo:{lat},{lon}")
        print(f"https://www.openstreetmap.org/?mlat={lat}&mlon={lon}#map=14/{lat}/{lon}")
        print()
        print("Daten für den aktuellen Tag:")
        print(f"MA {moonrise_local.strftime('%a %d.%m.%Y %H:%M:%S %Z')} "
              f"Az {round(moon_azimuth, 0):0>3.0f}°")
        print(f"SU {sunset_local.strftime('%a %d.%m.%Y %H:%M:%S %Z')} "
              f"Az {round(sun_azimuth, 0):0>3.0f}°")

        # Berechne nächste Vollmonde
        today = datetime.utcnow()
        start_date = today
        end_date = today + timedelta(days=30*(MONTHS-1))

        dates = []

        full_moons = get_full_moon_dates(start_date, end_date)
        print(f"\nDie Vollmonddaten für die nächsten {MONTHS} Monate:")
        for full_moon in full_moons:
            print(localtime(full_moon).strftime('%a %d.%m.%Y %H:%M:%S %Z'))
            dates.append({
                'date': full_moon,
                'moonrise': False,
                'moon_azimuth': False,
                'sunset': False,
                'sun_azimuth': False,
                'text': 'Vollmond'
            })

        # Berechne Zeiten für Vollmonddaten
        results = get_times_for_full_moon_dates(observer, lat, lon, full_moons)
        print("\nZeiten für Mondaufgang und Sonnenuntergang um Vollmond:")

        for result in results:
            dates.append(result)

        sorted_dates = sorted(dates, key=lambda k: k['date'])

        for date in sorted_dates:
            print_result(date)

    except Exception as e:
        print(f"Fehler: {e}")


if __name__ == "__main__":
    main()

