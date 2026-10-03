# city vs cities — KAYAN taxonomy fix

`cities` stays the only city taxonomy. Canonical rewrite is `/city/{slug}/`.
Legacy `city` is no longer registered. Its database rows are kept.

One-time migration option: `kayan_city_to_cities_migrated`.
Rewrite flush option: `kayan_city_cities_rewrites_flushed`.
ID map: `kayan_city_to_cities_map`.
