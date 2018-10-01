update maintenance_motivations set enabled = FALSE WHERE id in (3,18); 

update maintenance_motivations set description = 'Auto da carro per officina' WHERE id in (2); 

update maintenance_motivations set description = 'Auto già in officina' WHERE id in (5); 

update maintenance_motivations set description = 'Auto già in Carrozzeria' WHERE id in (10); 