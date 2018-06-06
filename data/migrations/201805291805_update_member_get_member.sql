INSERT INTO promo_codes_info (id, webuser_id, active, insert_ts, type, minutes, valid_from, bonus_duration_days, valid_to, overridden_subscription_cost, bonus_valid_from, bonus_valid_to, discount_percentage, no_standard_bonus) VALUES (nextval('promocodesinfo_id_seq'),NULL, TRUE, now(),'smgm', 45,'2018-05-01 00:00:00',90,'2020-12-31 23:59:59',100,'2018-05-01 00:00:00','2020-12-31 23:59:59',18,TRUE);
INSERT INTO promo_codes (id, promocodesinfo_id, promocode, description, active) VALUES (nextval('promocodes_id_seq'),  (SELECT MAX(id) FROM  promo_codes_info), 'SHARENGO_MGM_NEW','Bonus pacchetto benvenuto con codice amico Share''n Go a 1 euro, 45 minuti e 18% di sconto',TRUE);

INSERT INTO promo_codes_info (id, webuser_id, active, insert_ts, type, minutes, valid_from, bonus_duration_days, valid_to, overridden_subscription_cost, bonus_valid_from, bonus_valid_to, discount_percentage, no_standard_bonus) VALUES (nextval('promocodesinfo_id_seq'),NULL, TRUE, now(),'smgm', 30,'2018-05-01 00:00:00',90,'2020-12-31 23:59:59',0,'2018-05-01 00:00:00','2020-12-31 23:59:59',18,TRUE);
INSERT INTO promo_codes (id, promocodesinfo_id, promocode, description, active) VALUES (nextval('promocodes_id_seq'),  (SELECT MAX(id) FROM  promo_codes_info), 'SHARENGO_MGM_OLD','Bonus codice amico Share''n Go 30 minuti',TRUE);

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

    <body style="margin:0; padding:0;font-family: ''Arial'', sans-serif;" bgcolor="#F0F0F0">
       <table border="0" width="100%%" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0">
            <tr>
                <td align="center" valign="top" bgcolor="#F0F0F0" style="background-color: #F0F0F0;">
                    <table border="0" width="600" cellpadding="0" cellspacing="0" class="container" style="width:600px;max-width:600px">
                        <tr>
                            <td class="container-padding content" align="left" style="background-color:#ffffff">
                                <img style="width:600px; max-width:100%%;" src="http://site.sharengo.it/wp-content/uploads/2018/06/30minutigratis.jpg" alt="" class="banner"></img>
                                <br />
                                <div class="body-text" style="padding-left:15px;padding-right:15px;padding-top:5px;padding-bottom:10px;font-family:''Arial'', sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                    Complimenti, ora sei dei nostri!<br /><br />
                                    hai acquistato il tuo Pacchetto Benvenuto e grazie a %1$s ti sono gi&agrave; stati accreditati 30 minuti gratis.	<br /><br />

                                    La tua Sharengo ti aspetta! <img width="40px" style=" vertical-align: middle;   margin-left: -14px;" src="http://site.sharengo.it/wp-content/uploads/2018/05/cuore_giallo.png" alt=""></img><br /><br />

                                    <span style="vertical-align: top;line-height: 14px;">Il Team</span> <img width="90px" style=" vertical-align: middle; margin-left: 2px;" src="http://site.sharengo.it/wp-content/uploads/2016/10/logo-1.png" alt=""></img>
                                </div>
                            </td>
                            <td width="10"></td>
                        </tr>
                        <tr>
                            <td>
                                <table border="0" width="600" cellpadding="0" cellspacing="0" class="container footer" style="padding: 15px 15px 10px 15px;width:600px;max-width:600px;background-color: #E3D228;">
                                    <tr>
                                        <td class="container-padding content" align="left">
                                            <table width="264" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row social" >
                                                <tr>
                                                    <td class="col" valign="top">
                                                        <a href="https://www.facebook.com/ShareNGo.eu/"> <img src="http://site.sharengo.it/wp-content/uploads/2018/05/fb_icona_new.png" height="30px" style="padding-right: 5px" alt=""></img></a>
                                                        <a href="https://www.instagram.com/share_n_go/"> <img src="http://site.sharengo.it/wp-content/uploads/2018/05/instagram_icona_new.png" height="30px" style="padding-right: 5px" alt=""></img></a>
                                                        <a href="https://twitter.com/share_n_go"><img src="http://site.sharengo.it/wp-content/uploads/2018/05/twitter_icona_new.png" height="30px" style="padding-right: 5px" alt=""></img></a>
                                                        <a href="https://www.linkedin.com/company/share''ngo"> <img src="http://site.sharengo.it/wp-content/uploads/2018/05/linkedin_icona_new.png" height="30px" style="padding-right: 5px" alt=""></img></a>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table width="264" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row copyright">
                                                <tr>
                                                    <td class="col" valign="top" style="font-family:''Arial'', sans-serif;font-size:13px;line-height:16px;text-align:right;color:#333333;width:100%%;">
                                                        <b> Servizio Clienti 0586 1975772</b><br />
                                                        &copy; Sharengo CS Group Spa 2016
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>',
true,
'it',
'22'
);
