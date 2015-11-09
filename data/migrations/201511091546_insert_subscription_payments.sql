insert into subscription_payments
select nextval('subscription_payments_id_seq'), c.id, c.fleet_id, t.id, t.amount, now()
from customers c
join contracts co on co.customer_id = c.id
join transactions t on t.contract_id = co.id and (t.amount = 1000 or t.amount = 100) and t.outcome = 'OK'
where c.first_payment_completed = true
order by c.id