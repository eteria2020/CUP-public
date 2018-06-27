INSERT INTO mails (subject, content, enable, language, category)
VALUES (
'Recesso cliente avvenuto',
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SHARE&#39;NGO</title>
<link href=''https://fonts.googleapis.com/css?family=Lato:400,900'' rel=''stylesheet'' type=''text/css'' />
</head>
<body>
<table>
<tbody>
<tr>
<td width="10">&nbsp;</td>
<td>
<p>Buongiorno,</p>
<p>la presente per comunicare che è stata completata la procedura di recesso per il cliente con id: %1$s</p>
<p>Informare il team legale del recesso appena avvenuto.</p>
</td>
<td width="10">&nbsp;</td>
</tr>
</tbody>
</table>
</body>
</html>',
TRUE,
'it',
24
);