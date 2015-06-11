CREATE TABLE IF NOT EXISTS "user" (
    user_id SERIAL NOT NULL,
    username varchar(32),
    email varchar(32) NOT NULL,
    display_name varchar(32),
    password varchar(255) NOT NULL,
    state smallint
)