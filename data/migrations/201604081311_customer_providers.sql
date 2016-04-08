CREATE TABLE public.customer_providers
(
  customer_id      integer REFERENCES public.customers (id),
  provider_id  character varying(50) NOT NULL,
  provider     character varying(255) NOT NULL,

PRIMARY KEY (customer_id, provider_id),
UNIQUE (provider_id, provider)
);