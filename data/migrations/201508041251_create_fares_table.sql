CREATE SEQUENCE fares_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE fares (
    id INT NOT NULL,
    motion_cost_per_minute INT NOT NULL,
    park_cost_per_minute INT NOT NULL,
    cost_steps JSONB NOT NULL,
    PRIMARY KEY(id)
);

INSERT INTO fares (motion_cost_per_minute, park_cost_per_minute, cost_steps)
VALUES (28, 10, '{"1440": 5000, "240": 3000, "60": 1200}');