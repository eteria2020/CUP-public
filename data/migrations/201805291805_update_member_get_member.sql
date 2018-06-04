INSERT INTO promo_codes_info (id, webuser_id, active, insert_ts, type, minutes, valid_from, bonus_duration_days, valid_to, overridden_subscription_cost, bonus_valid_from, bonus_valid_to, discount_percentage, no_standard_bonus) VALUES (nextval('promocodesinfo_id_seq'),NULL, TRUE, now(),'smgm', 30,'2018-05-01 00:00:00',30,'2020-12-31 23:59:59',100,'2018-05-01 00:00:00','2020-12-31 23:59:59',18,TRUE);
INSERT INTO promo_codes (id, promocodesinfo_id, promocode, description, active) VALUES (nextval('promocodes_id_seq'),  (SELECT MAX(id) FROM  promo_codes_info), 'SHARENGO_MGM_NEW','Iscrizione a 1 euro, 30 minuti e 18% di sconto per promo codice amico Share''n Go (utente nuovo)',TRUE);

INSERT INTO promo_codes_info (id, webuser_id, active, insert_ts, type, minutes, valid_from, bonus_duration_days, valid_to, overridden_subscription_cost, bonus_valid_from, bonus_valid_to, discount_percentage, no_standard_bonus) VALUES (nextval('promocodesinfo_id_seq'),NULL, TRUE, now(),'smgm', 30,'2018-05-01 00:00:00',30,'2020-12-31 23:59:59',0,'2018-05-01 00:00:00','2020-12-31 23:59:59',18,TRUE);
INSERT INTO promo_codes (id, promocodesinfo_id, promocode, description, active) VALUES (nextval('promocodes_id_seq'),  (SELECT MAX(id) FROM  promo_codes_info), 'SHARENGO_MGM_OLD','Iscrizione a 0 euro, 30 minuti e 18% di sconto per promo codice amico Share''n Go (utente registarto)',TRUE);

INSERT INTO mails (id, subject,content,enable,language, category) VALUES (
nextval('mails_id_seq'),
'Share''ngo: Codice amico',
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>SHARE''NGO</title>
        <link href=''https://fonts.googleapis.com/css?family=Lato:400,900'' rel=''stylesheet'' type=''text/css''></link>
    </head>

    <body style="text-decoration:none !important;">
        <table width="500" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="500" height="45"><a href="http://site.sharengo.it" target="_blank"><img src="http://www.sharengo.it/images/mails/Sharengo_Header_LOGO_500x45.gif" width="500" height="45" alt="logo_sharengo" /></a></td></tr>
            <tr>
                <td  width="500" height="158"><a href="http://site.sharengo.it" target="_blank"><img src="http://www.sharengo.it/images/mails/VISUAL_un_anno_insieme.gif" width="500" height="158" alt="visual_sharengo" /></a></td></tr>
            <tr><td width="500" height="20"></td></tr>
            <tr>
                <td>
                    <!-- TABELLA TESTO -->
                    <table width="500" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="10"></td>
                            <td width="480" bgcolor="#FFFFFF" style="font-family:''Lato'', arial, sans-serif; font-size:14px; color:#333;  font-weight:regular; line-height:20px; text-align: justify">		  
                                Ciao %1$s,<br />
                                ti informiamo che hai ricevuto %2$d minuti bonus, grazie al promocode <strong>codice Amico Share&#39;Go</strong>.<br /><br />
                                Buon viaggio con Share&#39;ngo! <br /><br />
                                Un caro saluto, 
                            </td>
                            <td width="10"></td>
                        </tr>
                    </table>
                    <!-- FINE TABELLA TESTO -->
                </td>
            </tr>
            <tr><td width="500" height="20"></td></tr>
            <tr>
                <td  width="500" height="40" bgcolor="#FFFFFF" style="font-family:''Lato'', arial, sans-serif; font-size:12px; color:#333;  font-weight:regular; text-align:center;"><img src="http://www.sharengo.it/images/mails/Sharengo_Footer_TEAM_500x40.gif" width="500" height="40" alt="sharengo_team" /></td></tr>
            <tr>
                <td  width="500" height="40" bgcolor="#FFFFFF" style="font-family:''Lato'', arial, sans-serif; font-size:12px; color:#333;  font-weight:regular; text-align:center;">Info e regolamento su <a href="http://site.sharengo.it" target="_blank" style="color:#08bd53; text-decoration:none;">www.sharengo.it</a></td></tr>
            <tr>
                <td  width="500" height="80" bgcolor="#1c2429" style="font-family:''Lato'', arial, sans-serif; font-size:12px; color:#333;  font-weight:regular; text-align:left;">
                    <!-- TABELLA FOOTER -->
                    <table width="500" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="10"></td>
                            <td style="font-family:''Lato'', arial, sans-serif; font-size:12px; color:#08bd53;  font-weight:regular; text-align:left; vertical-align:bottom;">SEGUICI SU</td>
                            <td></td>
                            <td width="10"></td>
                        </tr>
                        <tr>
                            <td width="10"></td>
                            <td>
                                <!-- TABELLA SOCIAL -->
                                <table width="100"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td><a href="https://www.facebook.com/ShareNGo.eu" target="_blank" style="color:#08bd53; text-decoration:none;"><img src="http://www.sharengo.it/images/mails/Sharengo_Social_FB.gif" width="30" height="30" alt="sharengo facebook" /></a></td>
                                        <td width="5"></td>
                                        <td><a href="https://twitter.com/share_n_go" target="_blank" style="color:#08bd53; text-decoration:none;"><img src="http://www.sharengo.it/images/mails/Sharengo_Social_TW.gif" width="30" height="30" alt="sharengo twitter" /></a></td>
                                        <td width="5"></td>
                                        <td><a href="https://www.linkedin.com/company/share&#39;ngo" target="_blank" style="color:#08bd53; text-decoration:none;"><img src="http://www.sharengo.it/images/mails/Sharengo_Social_IN.gif" width="30" height="30" alt="sharengo in" /></a></td>
                                    </tr>
                                </table>
                                <!-- FINE TABELLA SOCIAL -->
                            </td>
                            <td style="font-family:''Lato'', arial, sans-serif; font-size:12px; color:#999;  font-weight:regular; text-align:right;">Servizio clienti <span style="color:#ccc;">0586 1975772</span><br/>
                            © Share’ngo CS Group Spa 2016</td>
                            <td width="10"></td>
                        </tr>
                    </table>
                    <!-- FINE TABELLA FOOTER -->
                </td>
            </tr>
        </table>
    </body>
</html>',
true,
'it',
'22'
);
