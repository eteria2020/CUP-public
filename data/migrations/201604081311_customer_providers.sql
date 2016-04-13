CREATE TABLE provider_authenticated_customers (
    id UUID NOT NULL,
    provider VARCHAR(255) NOT NULL,
    identifier VARCHAR(255) DEFAULT NULL,
    website_url VARCHAR(255) DEFAULT NULL,
    profile_url VARCHAR(255) DEFAULT NULL,
    photo_url VARCHAR(255) DEFAULT NULL,
    display_name VARCHAR(255) DEFAULT NULL,
    description VARCHAR(255) DEFAULT NULL,
    first_name VARCHAR(255) DEFAULT NULL,
    last_name VARCHAR(255) DEFAULT NULL,
    gender VARCHAR(255) DEFAULT NULL,
    language VARCHAR(255) DEFAULT NULL,
    age INT DEFAULT NULL,
    birth_day INT DEFAULT NULL,
    birth_month INT DEFAULT NULL,
    birth_year INT DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    email_verified VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(255) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    country VARCHAR(255) DEFAULT NULL,
    region VARCHAR(255) DEFAULT NULL,
    city VARCHAR(255) DEFAULT NULL,
    zip VARCHAR(255) DEFAULT NULL,
    customer_id INT DEFAULT NULL REFERENCES customers(id),
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE,
    PRIMARY KEY(id)
);
COMMENT ON COLUMN provider_authenticated_customers.id IS '(DC2Type:uuid)';

ALTER TABLE provider_authenticated_customers OWNER TO sharengo;