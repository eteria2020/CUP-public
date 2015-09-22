UPDATE pg_enum SET enumlabel = 'to_be_payed'
WHERE enumlabel = 'not_payed' AND enumtypid = (
  SELECT oid FROM pg_type WHERE typname = 'trip_payment_status'
);
