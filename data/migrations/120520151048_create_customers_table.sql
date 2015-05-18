CREATE TYPE gender AS ENUM ('male', 'female');

CREATE TABLE customers (
    id serial PRIMARY KEY,
    email text,
    password text,
    name text,
    surname text,
    gender gender,
    birth_date date,
    birth_town text,
    birth_province text,
    birth_country varchar(2),
    vat text,
    tax_code text,
    language varchar(2),
    country varchar(2),
    province text,
    town text,
    address text,
    address_info text,
    zip_code text,
    phone text,
    mobile text,
    fax text,
    driver_license text,
    driver_license_categories text[],
    driver_license_expire date,
    pin varchar(4),
    notes text,
    card_code text,
    inserted_ts timestamp with time zone DEFAULT now(),
    update_id bigint,
    update_ts bigint
)