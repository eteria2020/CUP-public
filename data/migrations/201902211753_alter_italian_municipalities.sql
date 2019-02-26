ALTER TABLE italian_municipalities ADD COLUMN zip_codes TEXT;
COMMENT ON COLUMN italian_municipalities.zip_codes IS 'Array dei CAP (da aggiornare con php public/index.php municipality update)';
