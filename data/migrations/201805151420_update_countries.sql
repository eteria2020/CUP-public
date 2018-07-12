-- psql -d sharengo -f /tmp/query_da_eseguire.sql
ALTER TABLE "public"."countries" ADD COLUMN "cadastral_code" character varying(255);

UPDATE countries SET cadastral_code = 'Z100' WHERE name = 'Albania';
UPDATE countries SET cadastral_code = 'Z101' WHERE name = 'Andorra';
UPDATE countries SET cadastral_code = 'Z102' WHERE name = 'Austria';
UPDATE countries SET cadastral_code = 'Z103' WHERE name = 'Belgio';
UPDATE countries SET cadastral_code = 'Z104' WHERE name = 'Bulgaria';
UPDATE countries SET cadastral_code = 'Z106' WHERE name = 'Citt&agrave; del vaticano';
UPDATE countries SET cadastral_code = 'Z107' WHERE name = 'Danimarca';
UPDATE countries SET cadastral_code = 'Z108' WHERE name = 'Isole Faroe';
UPDATE countries SET cadastral_code = 'Z109' WHERE name = 'Finlandia';
UPDATE countries SET cadastral_code = 'Z110' WHERE name = 'Francia';
UPDATE countries SET cadastral_code = 'Z112' WHERE name = 'Germania';
UPDATE countries SET cadastral_code = 'Z113' WHERE name = 'Gibilterra';
UPDATE countries SET cadastral_code = 'Z114' WHERE name = 'Regno Unito';
UPDATE countries SET cadastral_code = 'Z115' WHERE name = 'Grecia';
UPDATE countries SET cadastral_code = 'Z116' WHERE name = 'Irlanda';
UPDATE countries SET cadastral_code = 'Z117' WHERE name = 'Islanda';
UPDATE countries SET cadastral_code = 'Z119' WHERE name = 'Liechtenstein';
UPDATE countries SET cadastral_code = 'Z120' WHERE name = 'Lussemburgo';
UPDATE countries SET cadastral_code = 'Z121' WHERE name = 'Malta';
-- ['MAN (ISOLA)', 'Z122']
UPDATE countries SET cadastral_code = 'Z123' WHERE name = 'Monaco';
-- ['ISOLE DEL CANALE', 'Z124']
UPDATE countries SET cadastral_code = 'Z125' WHERE name = 'Norvegia';
UPDATE countries SET cadastral_code = 'Z126' WHERE name = 'Paesi Bassi';
UPDATE countries SET cadastral_code = 'Z127' WHERE name = 'Polonia';
UPDATE countries SET cadastral_code = 'Z128' WHERE name = 'Portogallo';
UPDATE countries SET cadastral_code = 'Z129' WHERE name = 'Romania';
UPDATE countries SET cadastral_code = 'Z130' WHERE name = 'San Marino';
UPDATE countries SET cadastral_code = 'Z131' WHERE name = 'Spagna';
UPDATE countries SET cadastral_code = 'Z132' WHERE name = 'Svezia';
UPDATE countries SET cadastral_code = 'Z133' WHERE name = 'Svizzera';
UPDATE countries SET cadastral_code = 'Z134' WHERE name = 'Ungheria';
UPDATE countries SET cadastral_code = 'Z138' WHERE name = 'Ucraina';
UPDATE countries SET cadastral_code = 'Z139' WHERE name = 'Bielorussia';
UPDATE countries SET cadastral_code = 'Z140' WHERE name = 'Moldavia';
UPDATE countries SET cadastral_code = 'Z144' WHERE name = 'Estonia';
UPDATE countries SET cadastral_code = 'Z145' WHERE name = 'Lettonia';
UPDATE countries SET cadastral_code = 'Z146' WHERE name = 'Lituania';
UPDATE countries SET cadastral_code = 'Z148' WHERE name = 'Macedonia';
UPDATE countries SET cadastral_code = 'Z149' WHERE name = 'Croazia';
UPDATE countries SET cadastral_code = 'Z150' WHERE name = 'Slovenia';
UPDATE countries SET cadastral_code = 'Z153' WHERE name = 'Bosnia Erzegovina';
UPDATE countries SET cadastral_code = 'Z154' WHERE name = 'Russia';
UPDATE countries SET cadastral_code = 'Z155' WHERE name = 'Slovacchia';
UPDATE countries SET cadastral_code = 'Z156' WHERE name = 'Repubblica Ceca';
-- ['KOSOVO', 'Z160']
UPDATE countries SET cadastral_code = 'Z161' WHERE name = 'Palestina';
UPDATE countries SET cadastral_code = 'Z200' WHERE name = 'Afghanistan';
UPDATE countries SET cadastral_code = 'Z203' WHERE name = 'Arabia Saudita';
UPDATE countries SET cadastral_code = 'Z204' WHERE name = 'Bahrein';
UPDATE countries SET cadastral_code = 'Z205' WHERE name = 'Bhutan';
UPDATE countries SET cadastral_code = 'Z206' WHERE name = 'Myanmar (Birmania)';
UPDATE countries SET cadastral_code = 'Z207' WHERE name = 'Brunei';
UPDATE countries SET cadastral_code = 'Z208' WHERE name = 'Cambogia';
UPDATE countries SET cadastral_code = 'Z209' WHERE name = 'Sri Lanka';
UPDATE countries SET cadastral_code = 'Z210' WHERE name = 'Cina';
UPDATE countries SET cadastral_code = 'Z211' WHERE name = 'Cipro';
UPDATE countries SET cadastral_code = 'Z212' WHERE name = 'Isole Cocos (Keeling)';
UPDATE countries SET cadastral_code = 'Z213' WHERE name = 'Corea del Sud';
UPDATE countries SET cadastral_code = 'Z214' WHERE name = 'Corea del Nord';
UPDATE countries SET cadastral_code = 'Z215' WHERE name = 'Emirati Arabi Uniti';
UPDATE countries SET cadastral_code = 'Z216' WHERE name = 'Filippine';
UPDATE countries SET cadastral_code = 'Z217' WHERE name = 'Taiwan';
-- ['GAZA (TERRITORIO DI)', 'Z218']
UPDATE countries SET cadastral_code = 'Z219' WHERE name = 'Giappone';
UPDATE countries SET cadastral_code = 'Z220' WHERE name = 'Giordania';
UPDATE countries SET cadastral_code = 'Z222' WHERE name = 'India';
UPDATE countries SET cadastral_code = 'Z223' WHERE name = 'Indonesia';
UPDATE countries SET cadastral_code = 'Z224' WHERE name = 'Iran';
UPDATE countries SET cadastral_code = 'Z225' WHERE name = 'Iraq';
UPDATE countries SET cadastral_code = 'Z226' WHERE name = 'Israele';
UPDATE countries SET cadastral_code = 'Z227' WHERE name = 'Kuwait';
UPDATE countries SET cadastral_code = 'Z228' WHERE name = 'Laos';
UPDATE countries SET cadastral_code = 'Z229' WHERE name = 'Libano';
UPDATE countries SET cadastral_code = 'Z231' WHERE name = 'Macau';
UPDATE countries SET cadastral_code = 'Z232' WHERE name = 'Maldive';
UPDATE countries SET cadastral_code = 'Z233' WHERE name = 'Mongolia';
UPDATE countries SET cadastral_code = 'Z234' WHERE name = 'Nepal';
UPDATE countries SET cadastral_code = 'Z235' WHERE name = 'Oman';
UPDATE countries SET cadastral_code = 'Z236' WHERE name = 'Pakistan';
UPDATE countries SET cadastral_code = 'Z237' WHERE name = 'Qatar';
UPDATE countries SET cadastral_code = 'Z240' WHERE name = 'Siria';
UPDATE countries SET cadastral_code = 'Z241' WHERE name = 'Thailandia';
UPDATE countries SET cadastral_code = 'Z243' WHERE name = 'Turchia';
UPDATE countries SET cadastral_code = 'Z246' WHERE name = 'Yemen';
UPDATE countries SET cadastral_code = 'Z247' WHERE name = 'Malesia';
UPDATE countries SET cadastral_code = 'Z248' WHERE name = 'Singapore';
UPDATE countries SET cadastral_code = 'Z249' WHERE name = 'Bangladesh';
UPDATE countries SET cadastral_code = 'Z251' WHERE name = 'Vietnam';
UPDATE countries SET cadastral_code = 'Z252' WHERE name = 'Armenia';
UPDATE countries SET cadastral_code = 'Z253' WHERE name = 'Azerbaigian';
UPDATE countries SET cadastral_code = 'Z254' WHERE name = 'Georgia';
UPDATE countries SET cadastral_code = 'Z255' WHERE name = 'Kazakistan';
UPDATE countries SET cadastral_code = 'Z256' WHERE name = 'Kirghizistan';
UPDATE countries SET cadastral_code = 'Z257' WHERE name = 'Tagikistan';
UPDATE countries SET cadastral_code = 'Z258' WHERE name = 'Turkmenistan';
UPDATE countries SET cadastral_code = 'Z259' WHERE name = 'Uzbekistan';
UPDATE countries SET cadastral_code = 'Z300' WHERE name = 'Namibia';
UPDATE countries SET cadastral_code = 'Z301' WHERE name = 'Algeria';
UPDATE countries SET cadastral_code = 'Z302' WHERE name = 'Angola';
UPDATE countries SET cadastral_code = 'Z305' WHERE name = 'Burundi';
UPDATE countries SET cadastral_code = 'Z306' WHERE name = 'Camerun';
UPDATE countries SET cadastral_code = 'Z307' WHERE name = 'Capo Verde';
UPDATE countries SET cadastral_code = 'Z308' WHERE name = 'Repubblica Centrafricana';
UPDATE countries SET cadastral_code = 'Z309' WHERE name = 'Ciad';
UPDATE countries SET cadastral_code = 'Z310' WHERE name = 'Comore';
UPDATE countries SET cadastral_code = 'Z311' WHERE name = 'Congo';
UPDATE countries SET cadastral_code = 'Z312' WHERE name = 'Repubblica Democratica del Congo';
UPDATE countries SET cadastral_code = 'Z313' WHERE name = 'Costa d''Avorio';
UPDATE countries SET cadastral_code = 'Z314' WHERE name = 'Benin';
UPDATE countries SET cadastral_code = 'Z315' WHERE name = 'Etiopia';
UPDATE countries SET cadastral_code = 'Z316' WHERE name = 'Gabon';
UPDATE countries SET cadastral_code = 'Z317' WHERE name = 'Gambia';
UPDATE countries SET cadastral_code = 'Z318' WHERE name = 'Ghana';
UPDATE countries SET cadastral_code = 'Z319' WHERE name = 'Guinea';
UPDATE countries SET cadastral_code = 'Z320' WHERE name = 'Guinea-Bissau';
UPDATE countries SET cadastral_code = 'Z321' WHERE name = 'Guinea Equatoriale';
UPDATE countries SET cadastral_code = 'Z322' WHERE name = 'Kenya';
UPDATE countries SET cadastral_code = 'Z324' WHERE name = 'Reunion';
UPDATE countries SET cadastral_code = 'Z325' WHERE name = 'Liberia';
UPDATE countries SET cadastral_code = 'Z326' WHERE name = 'Libia';
UPDATE countries SET cadastral_code = 'Z327' WHERE name = 'Madagascar';
UPDATE countries SET cadastral_code = 'Z328' WHERE name = 'Malawi';
UPDATE countries SET cadastral_code = 'Z329' WHERE name = 'Mali';
UPDATE countries SET cadastral_code = 'Z330' WHERE name = 'Marocco';
UPDATE countries SET cadastral_code = 'Z331' WHERE name = 'Mauritania';
UPDATE countries SET cadastral_code = 'Z332' WHERE name = 'Mauritius';
UPDATE countries SET cadastral_code = 'Z333' WHERE name = 'Mozambico';
UPDATE countries SET cadastral_code = 'Z334' WHERE name = 'Niger';
UPDATE countries SET cadastral_code = 'Z335' WHERE name = 'Nigeria';
UPDATE countries SET cadastral_code = 'Z336' WHERE name = 'Egitto';
UPDATE countries SET cadastral_code = 'Z337' WHERE name = 'Zimbabwe';
UPDATE countries SET cadastral_code = 'Z338' WHERE name = 'Ruanda';
--  ["SANT'ELENA (ISOLA)", 'Z340']
UPDATE countries SET cadastral_code = 'Z341' WHERE name = 'Sao Tome e Principe';
UPDATE countries SET cadastral_code = 'Z342' WHERE name = 'Seychelles';
UPDATE countries SET cadastral_code = 'Z343' WHERE name = 'Senegal';
UPDATE countries SET cadastral_code = 'Z344' WHERE name = 'Sierra Leone';
UPDATE countries SET cadastral_code = 'Z345' WHERE name = 'Somalia';
UPDATE countries SET cadastral_code = 'Z347' WHERE name = 'Sud Africa';
--  ['SUDAN', 'Z348']
UPDATE countries SET cadastral_code = 'Z349' WHERE name = 'Swaziland';
UPDATE countries SET cadastral_code = 'Z351' WHERE name = 'Togo';
UPDATE countries SET cadastral_code = 'Z352' WHERE name = 'Tunisia';
UPDATE countries SET cadastral_code = 'Z353' WHERE name = 'Uganda';
UPDATE countries SET cadastral_code = 'Z354' WHERE name = 'Burkina Faso';
UPDATE countries SET cadastral_code = 'Z355' WHERE name = 'Zambia';
UPDATE countries SET cadastral_code = 'Z357' WHERE name = 'Tanzania';
UPDATE countries SET cadastral_code = 'Z358' WHERE name = 'Botswana';
UPDATE countries SET cadastral_code = 'Z359' WHERE name = 'Lesotho';
UPDATE countries SET cadastral_code = 'Z360' WHERE name = 'Mayotte';
UPDATE countries SET cadastral_code = 'Z361' WHERE name = 'Gibuti';
UPDATE countries SET cadastral_code = 'Z368' WHERE name = 'Eritrea';
UPDATE countries SET cadastral_code = 'Z400' WHERE name = 'Bermuda';
UPDATE countries SET cadastral_code = 'Z401' WHERE name = 'Canada';
UPDATE countries SET cadastral_code = 'Z402' WHERE name = 'Groenlandia';
UPDATE countries SET cadastral_code = 'Z403' WHERE name = 'St. Pierre e Miquelon';
UPDATE countries SET cadastral_code = 'Z404' WHERE name = 'Stati Uniti';
UPDATE countries SET cadastral_code = 'Z501' WHERE name = 'Antille Olandesi';
UPDATE countries SET cadastral_code = 'Z502' WHERE name = 'Bahamas';
UPDATE countries SET cadastral_code = 'Z503' WHERE name = 'Costa Rica';
UPDATE countries SET cadastral_code = 'Z504' WHERE name = 'Cuba';
UPDATE countries SET cadastral_code = 'Z505' WHERE name = 'Repubblica Dominicana';
UPDATE countries SET cadastral_code = 'Z506' WHERE name = 'El Salvador';
UPDATE countries SET cadastral_code = 'Z507' WHERE name = 'Giamaica';
UPDATE countries SET cadastral_code = 'Z508' WHERE name = 'Guadalupa';
UPDATE countries SET cadastral_code = 'Z509' WHERE name = 'Guatemala';
UPDATE countries SET cadastral_code = 'Z510' WHERE name = 'Haiti';
UPDATE countries SET cadastral_code = 'Z511' WHERE name = 'Honduras';
UPDATE countries SET cadastral_code = 'Z512' WHERE name = 'Belize';
UPDATE countries SET cadastral_code = 'Z513' WHERE name = 'Martinica';
UPDATE countries SET cadastral_code = 'Z514' WHERE name = 'Messico';
UPDATE countries SET cadastral_code = 'Z515' WHERE name = 'Nicaragua';
UPDATE countries SET cadastral_code = 'Z516' WHERE name = 'Panama';
-- ['PANAMA ZONA DEL CANALE', 'Z517']
UPDATE countries SET cadastral_code = 'Z518' WHERE name = 'Puerto Rico';
UPDATE countries SET cadastral_code = 'Z519' WHERE name = 'Isole Turks e Caicos';
UPDATE countries SET cadastral_code = 'Z520' WHERE name = 'Isole Vergini Statunitensi';
UPDATE countries SET cadastral_code = 'Z522' WHERE name = 'Barbados';
UPDATE countries SET cadastral_code = 'Z524' WHERE name = 'Grenada';
UPDATE countries SET cadastral_code = 'Z525' WHERE name = 'Isole Vergini Britanniche';
UPDATE countries SET cadastral_code = 'Z526' WHERE name = 'Dominica';
UPDATE countries SET cadastral_code = 'Z527' WHERE name = 'St. Lucia';
UPDATE countries SET cadastral_code = 'Z528' WHERE name = 'St. Vincent e Grenadine';
UPDATE countries SET cadastral_code = 'Z529' WHERE name = 'Anguilla';
UPDATE countries SET cadastral_code = 'Z530' WHERE name = 'Isole Cayman';
UPDATE countries SET cadastral_code = 'Z531' WHERE name = 'Montserrat';
UPDATE countries SET cadastral_code = 'Z532' WHERE name = 'Antigua e Barbuda';
UPDATE countries SET cadastral_code = 'Z533' WHERE name = 'St. Kitts e Nevis';
UPDATE countries SET cadastral_code = 'Z600' WHERE name = 'Argentina';
UPDATE countries SET cadastral_code = 'Z601' WHERE name = 'Bolivia';
UPDATE countries SET cadastral_code = 'Z602' WHERE name = 'Brasile';
UPDATE countries SET cadastral_code = 'Z603' WHERE name = 'Cile';
UPDATE countries SET cadastral_code = 'Z604' WHERE name = 'Colombia';
UPDATE countries SET cadastral_code = 'Z605' WHERE name = 'Ecuador';
UPDATE countries SET cadastral_code = 'Z606' WHERE name = 'Guyana';
UPDATE countries SET cadastral_code = 'Z607' WHERE name = 'Guiana Francese';
UPDATE countries SET cadastral_code = 'Z608' WHERE name = 'Suriname';
UPDATE countries SET cadastral_code = 'Z609' WHERE name = 'Isole Falkland';
UPDATE countries SET cadastral_code = 'Z610' WHERE name = 'Paraguay';
UPDATE countries SET cadastral_code = 'Z611' WHERE name = 'Peru';
UPDATE countries SET cadastral_code = 'Z612' WHERE name = 'Trinidad e Tobago';
UPDATE countries SET cadastral_code = 'Z613' WHERE name = 'Uruguay';
UPDATE countries SET cadastral_code = 'Z614' WHERE name = 'Venezuela';
UPDATE countries SET cadastral_code = 'Z700' WHERE name = 'Australia';
UPDATE countries SET cadastral_code = 'Z702' WHERE name = 'Isola di Natale';
UPDATE countries SET cadastral_code = 'Z703' WHERE name = 'Isole Cook';
-- ['VITI', 'Z704']
UPDATE countries SET cadastral_code = 'Z706' WHERE name = 'Guam';
-- ['IRIAN OCCIDENTALE', 'Z707'], ['MACQUARIE (ISOLE)', 'Z708']
UPDATE countries SET cadastral_code = 'Z710' WHERE name = 'Isole Marianne Settentrionali';
UPDATE countries SET cadastral_code = 'Z711' WHERE name = 'Isole Marshall';
-- ['MIDWAY (ISOLE)', 'Z712']
UPDATE countries SET cadastral_code = 'Z713' WHERE name = 'Nauru';
-- ['SAVAGE (ISOLE)', 'Z714']
UPDATE countries SET cadastral_code = 'Z715' WHERE name = 'Isola Norfolk';
UPDATE countries SET cadastral_code = 'Z716' WHERE name = 'Nuova Caledonia';
UPDATE countries SET cadastral_code = 'Z719' WHERE name = 'Nuova Zelanda';
-- ['PITCAIRN (E DIPENDENZE)', 'Z722']
UPDATE countries SET cadastral_code = 'Z722' WHERE name = 'Isola Pitcairn';
UPDATE countries SET cadastral_code = 'Z723' WHERE name = 'Polinesia Francese';
-- ['SALOMONE', 'Z724']
UPDATE countries SET cadastral_code = 'Z725' WHERE name = 'Samoa Americane';
UPDATE countries SET cadastral_code = 'Z726' WHERE name = 'Samoa';
-- ["ISOLE DELL'UNIONE", 'Z727']
UPDATE countries SET cadastral_code = 'Z728' WHERE name = 'Tonga';
UPDATE countries SET cadastral_code = 'Z729' WHERE name = 'Isole Wallis e Futuna';
UPDATE countries SET cadastral_code = 'Z730' WHERE name = 'Papua Nuova Guinea';
UPDATE countries SET cadastral_code = 'Z731' WHERE name = 'Kiribati';
UPDATE countries SET cadastral_code = 'Z732' WHERE name = 'Tuvalu';
UPDATE countries SET cadastral_code = 'Z733' WHERE name = 'Vanuatu';
UPDATE countries SET cadastral_code = 'Z734' WHERE name = 'Palau';
UPDATE countries SET cadastral_code = 'Z735' WHERE name = 'Micronesia, Stati Federati della';
--    ['DIPENDENZE CANADESI', 'Z800']
--    ['DIPENDENZE NORVEGESI ARTICHE', 'Z801']
--    ['DIPENDENZE RUSSE', 'Z802']
--    ['DIPENDENZE AUSTRALIANE', 'Z900']
--    ['DIPENDENZE BRITANNICHE', 'Z901']
--    ['DIPENDENZE FRANCESI', 'Z902']
--    ['DIPENDENZE NEOZELANDESI', 'Z903']
--    ['DIPENDENZE NORVEGESI ANTARTICHE', 'Z904']
--    ['DIPENDENZE STATUNITENSI', 'Z905']
--    ['DIPENDENZE SUDAFRICANE', 'Z906']
--    ['SUD SUDAN', 'Z907']