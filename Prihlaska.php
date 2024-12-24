<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>prihlaska</title>
</head>
<body>

<form name="prihlaska"  id="prihlaska" method="post">
    <p><i>Pole označené <b style="color: red; font-size: 20px;">*</b> jsou povinné. Bez jejich vyplnění nelze přihlášku odeslat.</i></p>
    <hr>

    <!-- Jméno a příjmení -->
    <label for="jmeno"><b>Jméno a příjmení:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="jmeno" name="jmeno" placeholder="Jméno" maxlength="20" required>
    <input type="text" id="prijmeni" name="prijmeni" placeholder="Příjmení" maxlength="25" required>
    <br><br>

    <!-- Adresa -->
    <label><b>Adresa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="ulice" name="ulice" placeholder="Ulice" maxlength="30" required>
    <input type="text" id="mesto" name="mesto" placeholder="Město" maxlength="30" required>
    <input type="number" id="psc" name="psc" placeholder="PSČ" min="10000" max="99999" required>
    <br><br>

    <!-- Datum narození -->
    <label for="narozeni"><b>Datum narození:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="date" id="narozeni" name="narozeni" required>
    <small>Vyplňte v nezkráceném tvaru (např. 02.01.2015).</small>
    <br><br>

    <hr>

    <!-- Sekce rodiče -->
    <p><b style="color: red;">Další část přihlášky nech vyplnit rodiče.</b></p>

    <!-- Spojení na rodiče -->
    <label><b>Spojení na rodiče / zákonného zástupce:</b></label><br>
    <label>Jméno a kontaktní telefon: <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="jmenorod1" name="jmenorod1" placeholder="Jméno" maxlength="20" required>
    <input type="text" id="prijmenirod1" name="prijmenirod1" placeholder="Příjmení" maxlength="25" required>
    <input type="tel" id="telefonrod1" name="telefonrod1" placeholder="Telefon" pattern="\d{9}" required>
    <br><br>
    <label>Jméno a kontaktní telefon:</label><br>
    <input type="text" id="jmenorod2" name="jmenorod2" placeholder="Jméno" maxlength="20">
    <input type="text" id="prijmenirod2" name="prijmenirod2" placeholder="Příjmení" maxlength="25">
    <input type="tel" id="telefonrod2" name="telefonrod2" placeholder="Telefon" pattern="\d{9}">
    <br><small>Telefon vyplňujte bez předvolby (+420) a bez mezer.</small>
    <br><br>

    <!-- Kontaktní email -->
    <label for="email"><b>Kontaktní emailová adresa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="email" id="email" name="email" placeholder="@" maxlength="30" required>
    <br><br>

    <!-- Plavec / Neplavec -->
    <label><b>Můj syn / Má dcera je:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="radio" id="plavec" name="plavec" value="PLAVEC" required> Plavec
    <input type="radio" id="neplavec" name="plavec" value="NEPLAVEC" required> Neplavec
    <br><br>

    <!-- Místo odjezdu -->
    <label><b>Pojede z odjezdového místa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="radio" name="doprava" value="Ústí n.L." required> Ústí nad Labem
    <input type="radio" name="doprava" value="Praha" required> Praha
    <input type="radio" name="doprava" value="Votice" required> Votice
    <br><br>

    <!-- Platba -->
    <label><b>Platit budeme:</b></label><br>
    <input type="radio" name="platba" value="Převodem"> Převodem
    <input type="radio" name="platba" value="Fakturou"> Fakturou
    <small>(Pro zaměstnavatele k využití příspěvku FKSP.)</small>
    <br><br>

    <!-- Zdravotní potíže -->
    <label for="zdravi"><b>Zdravotní potíže / omezení:</b></label><br>
    <textarea id="zdravi" name="zdravi" rows="4" maxlength="200"></textarea>
    <br><br>

    <!-- Poznámky rodičů -->
    <label for="poznamka"><b>Poznámky rodičů:</b></label><br>
    <textarea id="poznamka" name="poznamka" rows="4" maxlength="200" placeholder="Zde můžete napsat další požadavky."></textarea>
    <br><br>

    <!-- Slevy -->
    <label><b>Žádám o slevu:</b></label><br>
    <input type="checkbox" name="vernostni" value="ANO"> Věrnostní
    <input type="checkbox" name="sourozenec" value="ANO"> Sourozenec
    <input type="checkbox" name="kamarad" value="ANO"> Kamarád
    <input type="text" name="kamaradjmeno" placeholder="Jméno kamaráda/ky" maxlength="30">
    <br><small><a href="sleva.php" target="_blank">Informace ke slevám</a></small>
    <br><br>

    <!-- Souhlas -->
    <input type="checkbox" name="podminky" value="ANO" required>
    <b style="color: red; font-size: 20px;">*</b> <span style="color: red;">Prohlašuji, že jsem vyplnil(a) veškeré údaje pravdivě a seznámil(a) se s <a href="podminky.php" target="_blank">informacemi pro rodiče</a>.</span>
    <br><br>

    <!-- Antispam -->
    <label for="pirat"><b>Ochrana proti SPAM robotům:</b></label><br>
    <span>Napište slovo "želva": </span><input type="text" id="pirat" name="pirat" style="font: 12px Arial" required>
    <br><br>

    <!-- Odeslat -->
    <input type="submit" name="poslat" value="Odeslat přihlášku">
</form>
    
</body>
</html>