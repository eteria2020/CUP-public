#!/bin/sh
#clean old commands and old archive reservations
#
time='3 months'
#
years='3 years'
#
now="$(date --date="3 year ago" "+%Y-%m-%d 00:00:00")"
#
echo $now
#
PSQL='psql postgresql://localhost:5432/sharengo?sslmode=require -U sharengo'
#
#PSQL='psql -p 5433'

$PSQL <<THE_END
DELETE FROM commands
WHERE queued < (now() - interval'$time');
DELETE FROM reservations_archive
WHERE ts < (now() - interval'$time');
DELETE FROM customer_locations
WHERE timestamp < (now() - interval '10 month');
DELETE FROM messages_outbox
WHERE submitted < (now() - interval'$time');


DELETE FROM public.customer_deactivations where inserted_ts < '$now';

DELETE FROM public.trip_bonuses where timestamp_end < '$now';


BEGIN;
DELETE FROM public.subscription_payments
WHERE public.subscription_payments.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.cartasi_csv_anomalies_notes
WHERE public.cartasi_csv_anomalies_notes.cartasi_csv_anomaly_id IN
      ( SELECT cc.id 
	    FROM public.cartasi_csv_anomalies cc
	    	left join public.transactions t on t.id = cc.transaction_id
	   		left join public.contracts c on c.id = t.contract_id
	    	where c.inserted_ts < '$now'
      );
DELETE FROM public.cartasi_csv_anomalies
WHERE public.cartasi_csv_anomalies.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.bonus_package_payments
WHERE public.bonus_package_payments.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.trip_payment_tries_canceled
WHERE public.trip_payment_tries_canceled.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.extra_payment_tries_canceled
WHERE public.extra_payment_tries_canceled.extra_payment_canceled_id IN
      ( SELECT e.id 
	    	FROM public.extra_payments e
	    	left join public.transactions t on t.id = e.transaction_id
	   		left join public.contracts c on c.id = t.contract_id
	    	where c.inserted_ts < '$now'
      );
DELETE FROM public.extra_payment_tries
WHERE public.extra_payment_tries.extra_payment_id IN
      ( SELECT e.id 
	    	FROM public.extra_payments e
	    	left join public.transactions t on t.id = e.transaction_id
	   		left join public.contracts c on c.id = t.contract_id
	    	where c.inserted_ts < '$now'
      );
DELETE FROM public.safo_penalty
WHERE public.safo_penalty.extra_payment_id IN
      ( SELECT e.id 
	    	FROM public.extra_payments e
	    	left join public.transactions t on t.id = e.transaction_id
	   		left join public.contracts c on c.id = t.contract_id
	    	where c.inserted_ts < '$now'
      );
DELETE FROM public.extra_payments_canceled
WHERE public.extra_payments_canceled.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );	  
DELETE FROM public.extra_payments
WHERE public.extra_payments.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.customers_bonus
WHERE public.customers_bonus.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.trip_payment_tries
WHERE public.trip_payment_tries.transaction_id IN
      ( select t.id from public.transactions t
			left join public.contracts c on c.id = t.contract_id
			where c.inserted_ts < '$now'
      );
DELETE FROM public.transactions
WHERE public.transactions.contract_id IN
      ( SELECT c.id 
	    	FROM public.contracts c
	    	WHERE c.inserted_ts < '$now'
      );
DELETE FROM public.contracts WHERE public.contracts.inserted_ts < '$now';
COMMIT;
THE_END