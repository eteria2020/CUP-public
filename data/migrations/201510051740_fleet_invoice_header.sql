ALTER TABLE fleets ADD invoice_header TEXT;

UPDATE fleets SET invoice_header =
'<span class="name">CS Milano SRL</span><br>
Sede legale<br>
Via dei Pelaghi, 162 – 57121 Livorno (LI)<br>
P.IVA: 01808470494<br>
Sede operativa<br>
Via Casati Felice 1/A – 20124 Milano (MI)<br>
Tel: 0586.1975772<br>
Email: servizioclienti@sharengo.eu'
WHERE id = 1; /*ensure that 1 is the id corresponding to Milano*/
UPDATE fleets SET invoice_header =
'<span class="name">CS Milano SRL</span><br>
Sede legale<br>
Via dei Pelaghi, 162 – 57124 Livorno (LI)<br>
P .IVA: 01823770498 - REA: LI161360<br>
Email: servizioclienti@sharengo.eu'
WHERE id = 2; /*ensure that 1 is the id corresponding to Firenze*/


ALTER TABLE fleets ALTER COLUMN invoice_header SET NOT NULL;