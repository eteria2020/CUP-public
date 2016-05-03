CREATE SEQUENCE fares_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE fares (
    id INT NOT NULL,
    motion_cost_per_minute INT NOT NULL,
    park_cost_per_minute INT NOT NULL,
    cost_steps JSONB NOT NULL,
    PRIMARY KEY(id)
);

