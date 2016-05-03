CREATE TYPE car_status AS ENUM (''); /*TODO: define the possible statuses*/
CREATE TYPE cleanliness AS ENUM ('clean', 'average', 'dirty');

/* to execute as a superuser */
-- Enable PostGIS (includes raster)
CREATE EXTENSION postgis;
-- Enable Topology
CREATE EXTENSION postgis_topology;
-- fuzzy matching needed for Tiger
CREATE EXTENSION fuzzystrmatch;
-- Enable US Tiger Geocoder
CREATE EXTENSION postgis_tiger_geocoder;

/*back as a normal user*/
CREATE TABLE cars (
    plate text NOT NULL PRIMARY KEY,
    manufactures text NOT NULL,
    model text NOT NULL,
    status car_status NOT NULL,
    number int NOT NULL,
    active boolean DEFAULT true,
    int_cleanliness cleanliness NOT NULL,
    ext_cleanliness cleanliness NOT NULL,
    notes text,
    longitude numeric NOT NULL,
    latitude numeric NOT NULL,
    damages text, /*TODO: upgrade db to 9.4 to use jsonb*/
    battery int NOT NULL,
    frame text,
    location geometry NOT NULL,
    firmware_version text NOT NULL,
    software_version text NOT NULL,
    mac text NOT NULL,
    imei text NOT NULL,
    last_contact timestamp with time zone,
    last_location_time timestamp with time zone,
    busy boolean DEFAULT false,
    hidden boolean DEFAULT false,
    rpm int NOT NULL,
    speed int NOT NULL,
    obc_in_use int NOT NULL,
    obc_wl_size int NOT NULL,
    km int NOT NULL,
    running boolean DEFAULT false,
    parking boolean DEFAULT false,
    plug boolean DEFAULT false NOT NULL
);
