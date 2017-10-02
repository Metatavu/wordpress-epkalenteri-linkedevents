# Wordpress LinkedEvents

Wordpress plugin for synchronizing epkalenteri.fi events into Linked Events API

## Install

Download latest release zip-file from https://github.com/Metatavu/wordpress-epkalenteri-linkedevents/releases and unzip the file into Wordpress /wp-content/plugins -folder.

After that activate the plugin via 'Plugins' menu from Wordpress administration view.

## Configuration

Plugin can be configured via Wordpress admin menu Settings > Linked Events EP.

### API Settings

These settings specify how the plugin connects to the API.

  - API URL - `URL into Linked Events API (e.g. https://example.linkedevents.fi/v1)`
  - API Key	- `Key for the API. Request this from your Linked Events administrator`
  - Datasource	- `Datasource for the API (e.g. exampleds). Request this from your Linked Events administrator`
  - Publisher Organization	- `Publisher organization for the API (e.g. exampleds:example). Request this from your Linked Events administrator`
  
###  GEOCoder Settings

These settings are used for GEO Coding addresses into components.

  - Geocoder provider  - `Provider used for GEO Coder provider. Use nominatim [default] or google_maps.`
  - Nominatim Server - `Nominatim server. Used only if Geocoder provider is nominatim. Defaults to OpenStreetMaps server`
  - Google Maps API Key - `Google Maps API Key. Used if Geocoder provider is google_maps. Should always be defined if google_maps proivder is used because otherwise the geocoder will eventually run out of free requests and start failing`
  
### Updater Settings

Updater settings can be used to define how often updater runs and how many items are processed in one batch.

Settings

  - [Place|Event|Image|Keyword|Audience|Event] update interval - `interval for updater. One of hourly, twicedaily, daily`
  - [Place|Event|Image|Keyword|Audience|Event] update batch size - `Batch size for updater (e.g. 10)`
