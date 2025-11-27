<?php
/*
============================================================
 Cajeer Engine - by Cajeer Team 
------------------------------------------------------------
 https://cajeer.com/
------------------------------------------------------------
 Copyright (c) 2013-2025 Cajeer Team 
============================================================
 All rights reserved.
 All trademarks are the property of their respective owners.
============================================================
 File: functions.php
============================================================
*/

function FormChars ($p1) {
	return nl2br(htmlspecialchars(trim($p1), ENT_QUOTES), false);
}
function GenPass ($p1, $p2) {
	return md5('5D0PyCF6HILdP8tnzh1sP6POGCRM4tEkgX0pnfThEYUyhjAIgZCqyXpFbvuvXzbRQgKzmAvOQCpE8xgZo8rU3Xgf4OE46nUaMvH'.md5('hJqiQ7HOup4EzZ2lS1lMXePWNXg7NgZQfNWuAI3fkMex5J58aBt2ZphWNb0lEBGm8ThXKONByTFrbj4S8sG6sBcQsOgsQDuB3Zk'.$p1.'wUYsLOAWriHZIRnuEYbSWx3aqikk8aIMuAIYXttlGrk4FxTXkYCurCpgmpktoEoa0loQgIl8YmOfr87itDhpivCR0tzZMnBsPpw').md5('CCjIYPLsgXkv7ctWf9Lf61qSSgEHvvGmYtcaKikMSzHUL9u8YWkrc4Pi1wPBTyd0XajkKtONx1CU35iCYBE9LlrLolnGw0Os9t9'.$p2.'IrS8x2FpvC4c5gx3N7wGmfY4djSO4YdsTFI9rKxTF6ch8Q8ewZ9bXjIp69GI6mYndPhLcmjOR9znyGlSaAfxHwtbRAR2betdLs0'));
}
function DataEncryption ($p1) {
    return md5('BQeCO0LC9cqxkitkLhj65qdVJK3M3eoBRTKAS5YpBYGnic9f0ANn9VB8dA0IT9UvkUn15NBgpiuSvXaZ9hyhn3cR6nkvfKCOCwB'.md5('vSnYWfVgLUSLyTaqmfxIWb1DWVgXgdthiFpp3DdW470dbnasAmPnTcASrbY9n7qn4hvsWOKuQr9Yn6YA7n8sL65yLqtoMKFYFCZ'.md5('0FudBSiwjZNSm2oug2pCOFxJ1bomFK7J2Q6bmvvQbDDfvnnJuZt7gefISfT8tbYX5byAt0omPlSZknbAkL0ZylZd3ZBctdh3ux7'.$p1.'rZ8Gu6uxHgm6KqZW7bayvRwUdaiUHrnbu3q0MEfdw06uGo1tfvZevxSCGEQROvCsZWuVTkgM3hHyq9qm5BrTeInHHzZRmAZzeFn').'Zomxl6hyuCc6pQbh0L1LWQbDy1uJOwkvs4ENqmdP8WBSDMnf66G0qEueIfvBYNRvUZBlKiWnbxraSu43Y945a85czqXJsopismC').'isWy34kwxFGGVDCkSFNjTyd6pKYASIRg7zeZHm1stUJytxAuKK8TMefpkfTp38O4a3BtygvKw2kJ5o9dWX7oIXU3XxkChxvcjh2');
}
function MessageSend ($p1, $p2, $p3 = '', $p4 = 1) {
    if ($p1 == 1) $p1 = LANGSYS07;
    else if ($p1 == 2) $p1 = LANGSYS08;
    else if ($p1 == 3) $p1 = LANGSYS09;
    $_SESSION['message'] = '<div class="MessageBlock"><b>'.$p1.':</b> '.$p2.'</div>';
    if ($p4) {
        if ($p3) $_SERVER['HTTP_REFERER'] = $p3;
        exit(header('Location: '.$_SERVER['HTTP_REFERER']));
    }
}
function MessageShow () {
    $Message = '';
    if ($_SESSION['message']) $Message = $_SESSION['message'];
    echo $Message;
    $_SESSION['message'] = array();
}
function ULogin ($p1) {
    if ($p1 <= 0 and $_SESSION['USER_ACTIVE'] != $p1) MessageSend(1, LANGSYS10, '/');
    else if ($_SESSION['USER_ACTIVE'] != $p1) MessageSend(1, LANGSYS11, '/');
}
function UGen ($p1) {
    if ($p1 == 0) return 'Not specified';
    else if ($p1 == 1) return 'Male';
    else if ($p1 == 2) return 'Woman';
}
function UGroup ($p1) {
    if ($p1 == -1) return 'Locked';
    else if ($p1 == 0) return 'User';
    else if ($p1 == 1) return 'V.I.P.';
    else if ($p1 == 2) return 'Verified';
    else if ($p1 == 3) return 'Partner';
    else if ($p1 == 4) return 'Moderator';
    else if ($p1 == 5) return 'Employee';
    else if ($p1 == 6) return 'Administrator';
}
function UAccess ($p1) {
    if ($_SESSION['USER_GROUP'] < $p1) MessageSend(1, LANGSYS12, '/');
}
function UCity ($p1) {
    if ($p1 == 0) return 'Not specified';
    else if ($p1 == 1) return '';
    else if ($p1 == 2) return '';
    else if ($p1 == 3) return '';
    else if ($p1 == 4) return '';
    else if ($p1 == 5) return '';
    else if ($p1 == 6) return '';
    else if ($p1 == 7) return '';
    else if ($p1 == 8) return '';
    else if ($p1 == 9) return '';
    else if ($p1 == 10) return '';
    else if ($p1 == 11) return '';
    else if ($p1 == 12) return '';
    else if ($p1 == 13) return '';
    else if ($p1 == 14) return '';
    else if ($p1 == 15) return '';
    else if ($p1 == 16) return '';
    else if ($p1 == 17) return '';
    else if ($p1 == 18) return '';
    else if ($p1 == 19) return '';
    else if ($p1 == 20) return '';
    else if ($p1 == 21) return '';
    else if ($p1 == 22) return '';
    else if ($p1 == 23) return '';
    else if ($p1 == 24) return '';
    else if ($p1 == 25) return '';
    else if ($p1 == 26) return '';
    else if ($p1 == 27) return '';
    else if ($p1 == 28) return '';
    else if ($p1 == 29) return '';
    else if ($p1 == 30) return '';
    else if ($p1 == 31) return '';
    else if ($p1 == 32) return '';
    else if ($p1 == 33) return '';
    else if ($p1 == 34) return '';
    else if ($p1 == 35) return '';
    else if ($p1 == 36) return '';
    else if ($p1 == 37) return '';
    else if ($p1 == 38) return '';
    else if ($p1 == 39) return '';
    else if ($p1 == 40) return '';
    else if ($p1 == 41) return '';
    else if ($p1 == 42) return '';
    else if ($p1 == 43) return '';
    else if ($p1 == 44) return '';
    else if ($p1 == 45) return '';
    else if ($p1 == 46) return '';
    else if ($p1 == 47) return '';
    else if ($p1 == 48) return '';
    else if ($p1 == 49) return '';
    else if ($p1 == 50) return '';
    else if ($p1 == 51) return '';
    else if ($p1 == 52) return '';
    else if ($p1 == 53) return '';
    else if ($p1 == 54) return '';
    else if ($p1 == 55) return '';
    else if ($p1 == 56) return '';
    else if ($p1 == 57) return '';
    else if ($p1 == 58) return '';
    else if ($p1 == 59) return '';
    else if ($p1 == 60) return '';
    else if ($p1 == 61) return '';
    else if ($p1 == 62) return '';
    else if ($p1 == 63) return '';
    else if ($p1 == 64) return '';
    else if ($p1 == 65) return '';
    else if ($p1 == 66) return '';
    else if ($p1 == 67) return '';
    else if ($p1 == 68) return '';
    else if ($p1 == 69) return '';
    else if ($p1 == 70) return '';
    else if ($p1 == 71) return '';
    else if ($p1 == 72) return '';
    else if ($p1 == 73) return '';
    else if ($p1 == 74) return '';
    else if ($p1 == 75) return '';
    else if ($p1 == 76) return '';
    else if ($p1 == 77) return '';
    else if ($p1 == 78) return '';
    else if ($p1 == 79) return '';
    else if ($p1 == 80) return '';
    else if ($p1 == 81) return '';
    else if ($p1 == 82) return '';
    else if ($p1 == 83) return '';
    else if ($p1 == 84) return '';
    else if ($p1 == 85) return '';
    else if ($p1 == 86) return '';
    else if ($p1 == 87) return '';
    else if ($p1 == 88) return '';
    else if ($p1 == 89) return '';
    else if ($p1 == 90) return '';
    else if ($p1 == 91) return '';
    else if ($p1 == 92) return '';
    else if ($p1 == 93) return '';
    else if ($p1 == 94) return '';
    else if ($p1 == 95) return '';
    else if ($p1 == 96) return '';
    else if ($p1 == 97) return '';
    else if ($p1 == 98) return '';
    else if ($p1 == 99) return '';
    else if ($p1 == 100) return '';
    else if ($p1 == 101) return '';
    else if ($p1 == 102) return '';
    else if ($p1 == 103) return '';
    else if ($p1 == 104) return '';
    else if ($p1 == 105) return '';
    else if ($p1 == 106) return '';
    else if ($p1 == 107) return '';
    else if ($p1 == 108) return '';
    else if ($p1 == 109) return '';
    else if ($p1 == 110) return '';
    else if ($p1 == 111) return '';
    else if ($p1 == 112) return '';
    else if ($p1 == 113) return '';
    else if ($p1 == 114) return '';
    else if ($p1 == 115) return '';
    else if ($p1 == 116) return '';
    else if ($p1 == 117) return '';
    else if ($p1 == 118) return '';
    else if ($p1 == 119) return '';
    else if ($p1 == 120) return '';
    else if ($p1 == 121) return '';
    else if ($p1 == 122) return '';
    else if ($p1 == 123) return '';
    else if ($p1 == 124) return '';
    else if ($p1 == 125) return '';
    else if ($p1 == 126) return '';
    else if ($p1 == 127) return '';
    else if ($p1 == 128) return '';
    else if ($p1 == 129) return '';
    else if ($p1 == 130) return '';
    else if ($p1 == 131) return '';
    else if ($p1 == 132) return '';
    else if ($p1 == 133) return '';
    else if ($p1 == 134) return '';
    else if ($p1 == 135) return '';
    else if ($p1 == 136) return '';
    else if ($p1 == 137) return '';
    else if ($p1 == 138) return '';
    else if ($p1 == 139) return '';
    else if ($p1 == 140) return '';
    else if ($p1 == 141) return '';
    else if ($p1 == 142) return '';
    else if ($p1 == 143) return '';
    else if ($p1 == 144) return '';
    else if ($p1 == 145) return '';
    else if ($p1 == 146) return '';
    else if ($p1 == 147) return '';
    else if ($p1 == 148) return '';
    else if ($p1 == 149) return '';
    else if ($p1 == 150) return '';
    else if ($p1 == 151) return '';
    else if ($p1 == 152) return '';
    else if ($p1 == 153) return '';
    else if ($p1 == 154) return '';
    else if ($p1 == 155) return '';
    else if ($p1 == 156) return '';
    else if ($p1 == 157) return '';
    else if ($p1 == 158) return '';
    else if ($p1 == 159) return '';
    else if ($p1 == 160) return '';
    else if ($p1 == 161) return '';
    else if ($p1 == 162) return '';
    else if ($p1 == 163) return '';
    else if ($p1 == 164) return '';
    else if ($p1 == 165) return '';
    else if ($p1 == 166) return '';
    else if ($p1 == 167) return '';
    else if ($p1 == 168) return '';
    else if ($p1 == 169) return '';
    else if ($p1 == 170) return '';
    else if ($p1 == 171) return '';
    else if ($p1 == 172) return '';
    else if ($p1 == 173) return '';
    else if ($p1 == 174) return '';
    else if ($p1 == 175) return '';
    else if ($p1 == 176) return '';
    else if ($p1 == 177) return '';
    else if ($p1 == 178) return '';
    else if ($p1 == 179) return '';
    else if ($p1 == 180) return '';
    else if ($p1 == 181) return '';
    else if ($p1 == 182) return '';
    else if ($p1 == 183) return '';
    else if ($p1 == 184) return '';
    else if ($p1 == 185) return '';
    else if ($p1 == 186) return '';
    else if ($p1 == 187) return '';
    else if ($p1 == 188) return '';
    else if ($p1 == 189) return '';
    else if ($p1 == 190) return '';
    else if ($p1 == 191) return '';
    else if ($p1 == 192) return '';
    else if ($p1 == 193) return '';
    else if ($p1 == 194) return '';
    else if ($p1 == 195) return '';
    else if ($p1 == 196) return '';
    else if ($p1 == 197) return '';
    else if ($p1 == 198) return '';
    else if ($p1 == 199) return '';
    else if ($p1 == 200) return '';
}
function ULang ($p1) {
    if ($p1 == 0) return 'Not specified';
    else if ($p1 == 1) return 'Abkhazian';  
    else if ($p1 == 2) return 'Azerbaijani';
    else if ($p1 == 3) return 'Aymara';
    else if ($p1 == 4) return 'Albanian';
    else if ($p1 == 5) return 'English';
    else if ($p1 == 6) return 'Arab';
    else if ($p1 == 7) return 'Armenian';
    else if ($p1 == 8) return 'Assamese';                       
    else if ($p1 == 9) return 'Afrikaans';
    else if ($p1 == 10) return 'Bashkir';
    else if ($p1 == 11) return 'Belorussian';
    else if ($p1 == 12) return 'Bengal';
    else if ($p1 == 13) return 'Bulgarian';
    else if ($p1 == 14) return 'Breton';
    else if ($p1 == 15) return 'Welsh';
    else if ($p1 == 16) return 'Hungarian';
    else if ($p1 == 17) return 'Vietnamese';
    else if ($p1 == 18) return 'Galician';
    else if ($p1 == 19) return 'Dutch';
    else if ($p1 == 20) return 'Greek';
    else if ($p1 == 21) return 'Georgian';
    else if ($p1 == 22) return 'Guarani';
    else if ($p1 == 23) return 'Danish';
    else if ($p1 == 24) return 'Zulu';
    else if ($p1 == 25) return 'Hebrew';
    else if ($p1 == 26) return 'Yiddish';
    else if ($p1 == 27) return 'Indonesian';
    else if ($p1 == 28) return 'Interlingua';
    else if ($p1 == 29) return 'Irish';
    else if ($p1 == 30) return 'Icelandic';
    else if ($p1 == 31) return 'Spanish';
    else if ($p1 == 32) return 'Italian';
    else if ($p1 == 33) return 'Kazakh';
    else if ($p1 == 34) return 'Cambodian';
    else if ($p1 == 35) return 'Catalan';
    else if ($p1 == 36) return 'Kashmiri';
    else if ($p1 == 37) return 'Quechua';
    else if ($p1 == 38) return 'Kyrgyz';
    else if ($p1 == 39) return 'Chinese';
    else if ($p1 == 40) return 'Korean';
    else if ($p1 == 41) return 'Corsican';
    else if ($p1 == 42) return 'Kurdish';
    else if ($p1 == 43) return 'Laotian';
    else if ($p1 == 44) return 'Latvian';
    else if ($p1 == 45) return 'Latin';
    else if ($p1 == 46) return 'Lithuanian';
    else if ($p1 == 47) return 'Malagasy';
    else if ($p1 == 48) return 'Malay';
    else if ($p1 == 49) return 'Maltese';
    else if ($p1 == 50) return 'Maori';
    else if ($p1 == 51) return 'Macedonian';
    else if ($p1 == 52) return 'Moldavian';
    else if ($p1 == 53) return 'Mongolian';
    else if ($p1 == 54) return 'Nauru';
    else if ($p1 == 55) return 'Deutsch';
    else if ($p1 == 56) return 'Nepali';
    else if ($p1 == 57) return 'Norwegian';
    else if ($p1 == 58) return 'Punjabi';
    else if ($p1 == 59) return 'Persian';
    else if ($p1 == 60) return 'Polish';
    else if ($p1 == 61) return 'Portuguese';
    else if ($p1 == 62) return 'Pashtun';
    else if ($p1 == 63) return 'Romansh';
    else if ($p1 == 64) return 'Romanian';
    else if ($p1 == 65) return 'Russian';
    else if ($p1 == 66) return 'Samoan';
    else if ($p1 == 67) return 'Sanskrit';
    else if ($p1 == 68) return 'Serbian';
    else if ($p1 == 69) return 'Slovak';
    else if ($p1 == 70) return 'Slovenian';
    else if ($p1 == 71) return 'Somalia';
    else if ($p1 == 72) return 'Swahili';
    else if ($p1 == 73) return 'Sudanese';
    else if ($p1 == 74) return 'Tagalog';
    else if ($p1 == 75) return 'Tajik';
    else if ($p1 == 76) return 'Thai';
    else if ($p1 == 77) return 'Tamil';
    else if ($p1 == 78) return 'Tatar';
    else if ($p1 == 79) return 'Tibetan';
    else if ($p1 == 80) return 'Tonga';
    else if ($p1 == 81) return 'Turkish';
    else if ($p1 == 82) return 'Turkmen';
    else if ($p1 == 83) return 'Uzbek';
    else if ($p1 == 84) return 'Ukrainian';
    else if ($p1 == 85) return 'Urdu';
    else if ($p1 == 86) return 'Fiji';
    else if ($p1 == 87) return 'Finnish';
    else if ($p1 == 88) return 'French';
    else if ($p1 == 89) return 'Frisian';
    else if ($p1 == 90) return 'Hausa';
    else if ($p1 == 91) return 'Hindi';
    else if ($p1 == 92) return 'Croatian';
    else if ($p1 == 93) return 'Czech';
    else if ($p1 == 94) return 'Swedish';
    else if ($p1 == 95) return 'Esperanto';
    else if ($p1 == 96) return 'Estonian';
    else if ($p1 == 97) return 'Javanese';
    else if ($p1 == 98) return 'Japanese';
}
function UPLang ($p1) {
    if ($p1 == 0) return 'Not specified';
    else if ($p1 == 1) return 'PHP';
    else if ($p1 == 2) return 'GNU bc';
    else if ($p1 == 3) return 'Euphoria';
    else if ($p1 == 4) return 'Limbo';
    else if ($p1 == 5) return 'Lua';
    else if ($p1 == 6) return 'Maple';
    else if ($p1 == 7) return 'MATLAB';
    else if ($p1 == 8) return 'Occam';
    else if ($p1 == 9) return 'PureBasic';
    else if ($p1 == 10) return 'Scilab';
    else if ($p1 == 11) return 'Active Oberon';
    else if ($p1 == 12) return 'Algol';
    else if ($p1 == 13) return 'B';
    else if ($p1 == 14) return 'COBOL';
    else if ($p1 == 15) return 'Modula-2';
    else if ($p1 == 16) return 'Modula-3';
    else if ($p1 == 17) return 'Oberon';
    else if ($p1 == 18) return 'Pascal';
    else if ($p1 == 19) return 'Rapier';
    else if ($p1 == 20) return 'C';
    else if ($p1 == 21) return 'Golang';
    else if ($p1 == 22) return 'Action Script';
    else if ($p1 == 23) return 'C++/CLI';
    else if ($p1 == 24) return 'C#';
    else if ($p1 == 25) return 'ColdFusion';
    else if ($p1 == 26) return 'D';
    else if ($p1 == 27) return 'Dart';
    else if ($p1 == 28) return 'Object Pascal';
    else if ($p1 == 29) return 'Dylan';
    else if ($p1 == 30) return 'Eiffel';
    else if ($p1 == 31) return 'GML';
    else if ($p1 == 32) return 'Groovy';
    else if ($p1 == 33) return 'Haxe';
    else if ($p1 == 34) return 'Io';
    else if ($p1 == 35) return 'Java';
    else if ($p1 == 36) return 'JavaScript';
    else if ($p1 == 37) return 'MC#';
    else if ($p1 == 38) return 'Objective-C';
    else if ($p1 == 39) return 'Perl';
    else if ($p1 == 40) return 'Pike';
    else if ($p1 == 41) return 'Python';
    else if ($p1 == 42) return 'Ruby';
    else if ($p1 == 43) return 'Self';
    else if ($p1 == 44) return 'Simula';
    else if ($p1 == 45) return 'Smalltalk';
    else if ($p1 == 46) return 'Swift';
    else if ($p1 == 47) return 'Vala';
    else if ($p1 == 48) return 'Visual Basic';
    else if ($p1 == 49) return 'Visual DataFlex';
    else if ($p1 == 50) return 'Zonnon';
    else if ($p1 == 51) return 'Ada';
    else if ($p1 == 52) return 'Oberon-2';
    else if ($p1 == 53) return 'C++';
    else if ($p1 == 54) return 'Kotlin';
    else if ($p1 == 55) return 'Delphi';
    else if ($p1 == 56) return 'Erlang';
    else if ($p1 == 57) return 'Mathematica';
    else if ($p1 == 58) return 'Mozart';
    else if ($p1 == 59) return 'Nemerle';
    else if ($p1 == 60) return 'Rust';
    else if ($p1 == 61) return 'Scala';
    else if ($p1 == 62) return 'Swift';
    else if ($p1 == 63) return 'Component Pascal';
    else if ($p1 == 64) return 'Julia';
}
function UCountry ($p1) {
    if ($p1 == 0) return 'Not specified';
    else if ($p1 == 1) return 'Australia';
    else if ($p1 == 2) return 'Austria';
    else if ($p1 == 3) return 'Azerbaijan';
    else if ($p1 == 4) return 'Albania';
    else if ($p1 == 5) return 'Algeria';
    else if ($p1 == 6) return 'Angola';
    else if ($p1 == 7) return 'Andorra';
    else if ($p1 == 8) return 'Antigua';
    else if ($p1 == 9) return 'Argentina';
    else if ($p1 == 10) return 'Armenia';
    else if ($p1 == 11) return 'Afghanistan';
    else if ($p1 == 12) return 'Bahamas';
    else if ($p1 == 13) return 'Bangladesh';
    else if ($p1 == 14) return 'Barbados';
    else if ($p1 == 15) return 'Bahrain';
    else if ($p1 == 16) return 'Belarus';
    else if ($p1 == 17) return 'Belize';
    else if ($p1 == 18) return 'Belgium';
    else if ($p1 == 19) return 'Benin';
    else if ($p1 == 20) return 'Bulgaria';
    else if ($p1 == 21) return 'Bolivia';
    else if ($p1 == 22) return 'Bosnia and Herzegovina';
    else if ($p1 == 23) return 'Botswana';
    else if ($p1 == 24) return 'Brazil';
    else if ($p1 == 25) return 'Brunei';
    else if ($p1 == 26) return 'Burkina';
    else if ($p1 == 27) return 'Burundi';
    else if ($p1 == 28) return 'Bhutan';
    else if ($p1 == 29) return 'Vanuatu';
    else if ($p1 == 30) return 'Great Britain';
    else if ($p1 == 31) return 'Ireland';
    else if ($p1 == 32) return 'Hungary';
    else if ($p1 == 33) return 'Venezuela';
    else if ($p1 == 34) return 'East Timor';
    else if ($p1 == 35) return 'Vietnam';
    else if ($p1 == 36) return 'Gabon';
    else if ($p1 == 37) return 'Haiti';
    else if ($p1 == 38) return 'Guyana';
    else if ($p1 == 39) return 'Gambia';
    else if ($p1 == 40) return 'Ghana';
    else if ($p1 == 41) return 'Guatemala';
    else if ($p1 == 42) return 'Guinea';
    else if ($p1 == 43) return 'Guinea-Bissau';
    else if ($p1 == 44) return 'Germany';
    else if ($p1 == 45) return 'Honduras';
    else if ($p1 == 46) return 'Grenada';
    else if ($p1 == 47) return 'Greece';
    else if ($p1 == 48) return 'Georgia';
    else if ($p1 == 49) return 'Denmark';
    else if ($p1 == 50) return 'Djibouti';
    else if ($p1 == 51) return 'Dominica';
    else if ($p1 == 52) return 'Dominican';
    else if ($p1 == 53) return 'Egypt';
    else if ($p1 == 54) return 'Zambia';
    else if ($p1 == 55) return 'Zimbabwe';
    else if ($p1 == 56) return 'Israel';
    else if ($p1 == 57) return 'India';
    else if ($p1 == 58) return 'Indonesia';
    else if ($p1 == 59) return 'Jordan';
    else if ($p1 == 60) return 'Iraq';
    else if ($p1 == 61) return 'Iran';
    else if ($p1 == 62) return 'Ireland';
    else if ($p1 == 63) return 'Iceland';
    else if ($p1 == 64) return 'Spain';
    else if ($p1 == 65) return 'Italy';
    else if ($p1 == 66) return 'Yemen';
    else if ($p1 == 67) return 'Cape';
    else if ($p1 == 68) return 'Kazakhstan';
    else if ($p1 == 69) return 'Cambodia';
    else if ($p1 == 70) return 'Cameroon';
    else if ($p1 == 71) return 'Canada';
    else if ($p1 == 72) return 'Qatar';
    else if ($p1 == 73) return 'Kenya';
    else if ($p1 == 74) return 'Cyprus';
    else if ($p1 == 75) return 'Kyrgyzstan';
    else if ($p1 == 76) return 'Kiribati';
    else if ($p1 == 77) return 'China';
    else if ($p1 == 78) return 'Colombia';
    else if ($p1 == 79) return 'Comoros';
    else if ($p1 == 80) return 'Congo';
    else if ($p1 == 81) return 'DR Congo';
    else if ($p1 == 82) return 'DPRK';
    else if ($p1 == 83) return 'Korea';
    else if ($p1 == 84) return 'Costa Rica';
    else if ($p1 == 85) return 'Ivory Coast';
    else if ($p1 == 86) return 'Cuba';
    else if ($p1 == 87) return 'Kuwait';
    else if ($p1 == 88) return 'Laos';
    else if ($p1 == 89) return 'Latvia';
    else if ($p1 == 90) return 'Lesotho';
    else if ($p1 == 91) return 'Liberia';
    else if ($p1 == 92) return 'Lebanon';
    else if ($p1 == 93) return 'Libya';
    else if ($p1 == 94) return 'Lithuania';
    else if ($p1 == 95) return 'Liechtenstein';
    else if ($p1 == 96) return 'Luxembourg';
    else if ($p1 == 97) return 'Mauritius';
    else if ($p1 == 98) return 'Mauritania';
    else if ($p1 == 99) return 'Madagascar';
    else if ($p1 == 100) return 'Malawi';
    else if ($p1 == 101) return 'Malaysia';
    else if ($p1 == 102) return 'Mali';
    else if ($p1 == 103) return 'Maldives';
    else if ($p1 == 104) return 'Malta';
    else if ($p1 == 105) return 'Morocco';
    else if ($p1 == 106) return 'Marshall Islands';
    else if ($p1 == 107) return 'Mexico';
    else if ($p1 == 108) return 'Micronesia';
    else if ($p1 == 109) return 'Mozambique';
    else if ($p1 == 110) return 'Moldova';
    else if ($p1 == 111) return 'Monaco';
    else if ($p1 == 112) return 'Mongolia';
    else if ($p1 == 113) return 'Myanmar';
    else if ($p1 == 114) return 'Namibia';
    else if ($p1 == 115) return 'Nauru';
    else if ($p1 == 116) return 'Nepal';
    else if ($p1 == 117) return 'Niger';
    else if ($p1 == 118) return 'Nigeria';
    else if ($p1 == 119) return 'Netherlands';
    else if ($p1 == 120) return 'Nicaragua';
    else if ($p1 == 121) return 'New Zealand';
    else if ($p1 == 122) return 'Norway';
    else if ($p1 == 123) return 'UAE';
    else if ($p1 == 124) return 'Oman';
    else if ($p1 == 125) return 'Pakistan';
    else if ($p1 == 126) return 'Palau';
    else if ($p1 == 127) return 'Panama';
    else if ($p1 == 128) return 'Papua New Guinea';
    else if ($p1 == 129) return 'Paraguay';
    else if ($p1 == 130) return 'Peru';
    else if ($p1 == 131) return 'Poland';
    else if ($p1 == 132) return 'Portugal';
    else if ($p1 == 133) return 'Russia';
    else if ($p1 == 134) return 'Rwanda';
    else if ($p1 == 135) return 'Romania';
    else if ($p1 == 136) return 'El Salvador';
    else if ($p1 == 137) return 'Samoa';
    else if ($p1 == 138) return 'San Marino';
    else if ($p1 == 139) return 'Sao Tome and Principe';
    else if ($p1 == 140) return 'Saudi Arabia';
    else if ($p1 == 141) return 'North Macedonia';
    else if ($p1 == 142) return 'Seychelles';
    else if ($p1 == 143) return 'Senegal';
    else if ($p1 == 144) return 'Saint Vincent and Grenadines';
    else if ($p1 == 145) return 'Saint Kitts and Nevis';
    else if ($p1 == 146) return 'Nevis';
    else if ($p1 == 147) return 'Saint Lucia';
    else if ($p1 == 148) return 'Republic of Serbia';
    else if ($p1 == 149) return 'Singapore';
    else if ($p1 == 150) return 'Syria';
    else if ($p1 == 151) return 'Slovakia';
    else if ($p1 == 152) return 'Slovenia';
    else if ($p1 == 153) return 'USA';
    else if ($p1 == 154) return 'Solomon islands';
    else if ($p1 == 155) return 'Somalia';
    else if ($p1 == 156) return 'Sudan';
    else if ($p1 == 157) return 'Suriname';
    else if ($p1 == 158) return 'Sierra Leone';
    else if ($p1 == 159) return 'Tajikistan';
    else if ($p1 == 160) return 'Thailand';
    else if ($p1 == 161) return 'Tanzania';
    else if ($p1 == 162) return 'Togo';
    else if ($p1 == 163) return 'Tonga';
    else if ($p1 == 164) return 'Trinidad and Tobago';
    else if ($p1 == 165) return 'Tuvalu';
    else if ($p1 == 166) return 'Tunisia';
    else if ($p1 == 167) return 'Turkmenistan';
    else if ($p1 == 168) return 'Turkey';
    else if ($p1 == 169) return 'Uganda';
    else if ($p1 == 170) return 'Uzbekistan';
    else if ($p1 == 171) return 'Ukraine';
    else if ($p1 == 172) return 'Uruguay';
    else if ($p1 == 173) return 'Fiji';
    else if ($p1 == 174) return 'Philippines';
    else if ($p1 == 175) return 'Finland';
    else if ($p1 == 176) return 'France';
    else if ($p1 == 177) return 'Croatia';
    else if ($p1 == 178) return 'CAR';
    else if ($p1 == 179) return 'Chad';
    else if ($p1 == 180) return 'Montenegro';
    else if ($p1 == 181) return 'Czech';
    else if ($p1 == 182) return 'Chile';
    else if ($p1 == 183) return 'Switzerland';
    else if ($p1 == 184) return 'Sweden';
    else if ($p1 == 185) return 'Sri Lanka';
    else if ($p1 == 186) return 'Ecuador';
    else if ($p1 == 187) return 'Equatorial Guinea';
    else if ($p1 == 188) return 'Eritrea';
    else if ($p1 == 189) return 'Eswatini';
    else if ($p1 == 190) return 'Estonia';
    else if ($p1 == 191) return 'Ethiopia';
    else if ($p1 == 192) return 'South Africa';
    else if ($p1 == 193) return 'South Sudan';
    else if ($p1 == 194) return 'Jamaica';
    else if ($p1 == 195) return 'Japan';
    else if ($p1 == 196) return 'Israel';
    else if ($p1 == 197) return 'Korea';
    else if ($p1 == 198) return 'North Korea';
    else if ($p1 == 199) return 'China';
    else if ($p1 == 200) return 'Cyprus';
    else if ($p1 == 201) return 'Armenia';
    else if ($p1 == 202) return 'Abkhazia';
    else if ($p1 == 203) return 'Kosovo';
    else if ($p1 == 204) return 'Palestine';
    else if ($p1 == 205) return 'SADR';
    else if ($p1 == 206) return 'Taiwan';
    else if ($p1 == 207) return 'TRNC';
    else if ($p1 == 208) return 'South Ossetia';
}
function STemp ($p1) {
    if ($p1 == 0) return 'Default';
    else if ($p1 == 1) return 'Cajeer';
}
function SLang ($p1) {
    if ($p1 == 0) return 'English';
    else if ($p1 == 1) return 'French';
    else if ($p1 == 2) return 'German';
    else if ($p1 == 3) return 'Italian';
    else if ($p1 == 4) return 'Russian';
    else if ($p1 == 5) return 'Spanish';
}
function RandomString ($p1) {
    $Char = '0123456789abcdefghijklmnopqrstuvwxyz';
    for ($i = 0; $i < $p1; $i++) $String .= $Char[rand(0, strlen($Char) - 1)];
    return $String;
}
function HideEmail ($p1) {
    $Explode = explode('@', $p1);
    return $Explode[0].'@******.***';
}
function PageSelector ($p1, $p2, $p3, $p4 = 5, $p5 = 2) {
    $Page = ceil($p3[0] / $p4);
    if ($Page > 1) {
        echo '<div class="PageSelector">';
        for ($i = ($p2 - $p5); $i < ($Page + 1); $i++) {
            if ($i > 0 and $i <= ($p2 + $p5)) {
                if ($p2 == $i) $Swch = 'SwitchItemCur';
                else $Swch = 'SwitchItem';
                echo '<a class="'.$Swch.'" href="'.$p1.$i.'">'.$i.'</a>';
            }
        }
        echo '</div>';
    }
}
function Parse ($p1, $p2, $p3) {
    $num1 = strpos($p1, $p2);
    if ($num1 === false) return 0;
    $num2 = substr($p1, $num1);
    return strip_tags(substr($num2, 0, strpos($num2, $p3)));
}
function SendNotice ($p1, $p2) {
    global $CONNECT;
    $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `users` WHERE `login` = '$p1'"));
    if (!$Row['id']) echo 'Error';
    mysqli_query($CONNECT, "INSERT INTO `notice` VALUES ('', $Row[id], 0, NOW(), '$p2')");
}
function check_login ($p1) {
    if (!preg_match('/^[A-Za-z-0-9]{3,16}$/', $p1)) MessageSend(1, LANGSITE93);
}
function check_password ($p1) {
    if (!preg_match('/^[A-Za-z-0-9]{8,64}$/', $p1)) MessageSend(1, LANGSITE95);
}
function check_antibot ($p1) {
    if (!preg_match('/^[0-9]{6}$/', $p1)) MessageSend(1, 'No more and no less than six digits');
}
function check_name ($p1) {
    if (!preg_match('/^[A-Za-z]{3,16}$/', $p1)) MessageSend(1, LANGSITE99);
}
function check_email ($p1) {
    if (!preg_match('/^([A-z0-9_\.-]+)@([A-z0-9_\.-]+)\.([A-z\.]{2,6})$/', $p1)) MessageSend(1, 'Email is not correct.');
}
function NotFound () {
    header('HTTP/1.0 404 Not Found');
    $_SESSION['USER_ERROR'] = 404;
    exit(include ROOT_DIR.('/templates/errors.php'));
}
?>
