Alter table messages_outbox ADD COLUMN webuser_id integer;

ALTER TABLE messages_outbox ADD CONSTRAINT fk_webUserId FOREIGN KEY (webuser_id) REFERENCES webuser(id) MATCH FULL;

