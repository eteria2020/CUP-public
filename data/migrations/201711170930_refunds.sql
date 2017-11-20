CREATE TABLE refunds (
   id SERIAL PRIMARY KEY,
   codTrans VARCHAR(255) DEFAULT NULL,
   request_type VARCHAR(255) DEFAULT NULL,
   outcome text DEFAULT NULL,
   type_op VARCHAR(4) DEFAULT NULL,
   amount VARCHAR(255) DEFAULT NULL,
   currency VARCHAR(3) NOT NULL,
   codAut VARCHAR(255) DEFAULT NULL,
   amount_op VARCHAR(255) DEFAULT NULL,
   inserted_ts timestamp(0) without time zone DEFAULT NULL
);