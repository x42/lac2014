<h1>Registration policy</h1>
<p>
Registration and admittance to the LAC 2014 is <span class="standout">free</span> (gratis) but if you want to attend 
<span class="standout">you need to register</span> (scroll down).
Note: Presenters and workshop-leaders are no exception to this rule. Please register.
</p>

This is for a number of reasons:
<ul>
<li> Estimate - We need the number of attendees for insurance and fire-regulations.</li>
<li> ID - We'd like a name to print on your badge.</li>
<li> Concerts - Some concert venues might want to charge for the concerts. We
want to make sure that all registered attendees (those with a badge) get free entrance to the concerts.</li>
<li> We want to be able to contact you (see under Privacy).</li>
</ul>

<p>
If you have registered and for whatever reason will not come to the
conference, please let us know as soon as you can by sending an email to
<?=$config['txtemail']?>.
</p>

<p>This registration covers the attendance to the main conference venue only. Concerts in clubs and similar evening-sessions may require reservation or purchase of an entrance ticket at the venue.</p>

<h1>Privacy</h1>
<p>
You are asked for your e-mail address so we can follow up on your
registration on a later date. We will not use your email address for any
other purpose than to inform you of conference details,
last minute changes or to confirm unsubscription requests and
will keep mail traffic to an absolute minimum.
Personal data provided by you in the registration form will be kept confidential and is not shared with any 3rd party.
</p>

<h1>Registration<a name="registration"></a></h1>

<div id="registration">
<p>Please enter your registration information; fields marked with a
 <span class="error">*</span> are mandatory.</p>

<?php

  echo $errmsg;

?>

<?php 
  function _ck($k, $c) {
    if (isset($_POST[$k]) && $_POST[$k] == $c) echo ' checked="checked"';
    if (!isset($_POST[$k]) && $c=='0') echo ' checked="checked"';
  }

  function _cl($k, $c) {
    if (isset($_POST[$k]) && $_POST[$k] == $c) return ' checked="checked"';
    return '';
  }

  function _sl($k, $c) {
    if (isset($_POST[$k]) && $_POST[$k] == $c) return ' selected="selected"';
    return '';
  }

  function gen_reg_options ($d,$k) {
    foreach ($d as $v => $t) {
      echo '    <option value="'.$v.'"'._sl($k,$v).'>'.$t.'</option>'."\n";
    }
  }

  function gen_checktd ($d,$r=3) {
    $cnt=0;
    foreach ($d as $v => $t) {
      echo '    <td><label><input type="checkbox" name="'.$v.'" value="1"'._cl($v,1).'/>'.$t.'</label></td>'."\n";
      if (++$cnt%$r == 0) echo "    </tr><tr>\n";
    }
  }

  $ages=array (
   '' => 'Please select your age group..',
   'A0015' => '15 years or younger',
   'A1620' => '16-20 years',
   'A2125' => '21-25 years',
   'A2530' => '25-30 years',
   'A3135' => '31-35 years',
   'A3540' => '35-40 years',
   'A4145' => '41-45 years',
   'A4550' => '45-50 years',
   'A5155' => '51-55 years',
   'A5560' => '55-60 years',
   'A6165' => '61-65 years',
   'A6570' => '65-70 years',
   'A71XX' => '71 and older'
  );

  # vi-macro uppercase first letter, lowercase word.
  # :map <F5> :.s/\([A-Z]\)\([A-Za-z][A-Za-z][A-Za-z]*\)/\1\L\2/g<CR>:.s/\sOR\s\\|\sAND\s\\|\sOF[, )]\\|\sTHE[, \)]/\L&/gi<CR>
  $ctry=array (
      '' => 'Please select your country..',
    'AF' => 'AF (Afghanistan)',
    'AX' => 'AX (Aland Islands)',
    'AL' => 'AL (Albania)',
    'DZ' => 'DZ (Algeria)',
    'AS' => 'AS (American Samoa)',
    'AD' => 'AD (Andorra)',
    'AO' => 'AO (Angola)',
    'AI' => 'AI (Anguilla)',
    'AQ' => 'AQ (Antarctica)',
    'AG' => 'AG (Antigua and Barbuda)',
    'AR' => 'AR (Argentina)',
    'AM' => 'AM (Armenia)',
    'AW' => 'AW (Aruba)',
    'AU' => 'AU (Australia)',
    'AT' => 'AT (Austria)',
    'AZ' => 'AZ (Azerbaijan)',
    'BS' => 'BS (Bahamas)',
    'BH' => 'BH (Bahrain)',
    'BD' => 'BD (Bangladesh)',
    'BB' => 'BB (Barbados)',
    'BY' => 'BY (Belarus)',
    'BE' => 'BE (Belgium)',
    'BZ' => 'BZ (Belize)',
    'BJ' => 'BJ (Benin)',
    'BM' => 'BM (Bermuda)',
    'BT' => 'BT (Bhutan)',
    'BO' => 'BO (Bolivia, Plurinational State of)',
    'BA' => 'BA (Bosnia and Herzegovina)',
    'BW' => 'BW (Botswana)',
    'BV' => 'BV (Bouvet Island)',
    'BR' => 'BR (Brazil)',
    'IO' => 'IO (British Indian Ocean Territory)',
    'BN' => 'BN (Brunei Darussalam)',
    'BG' => 'BG (Bulgaria)',
    'BF' => 'BF (Burkina Faso)',
    'BI' => 'BI (Burundi)',
    'KH' => 'KH (Cambodia)',
    'CM' => 'CM (Cameroon)',
    'CA' => 'CA (Canada)',
    'CV' => 'CV (Cape Verde)',
    'KY' => 'KY (Cayman Islands)',
    'CF' => 'CF (Central African Republic)',
    'TD' => 'TD (Chad)',
    'CL' => 'CL (Chile)',
    'CN' => 'CN (China)',
    'CX' => 'CX (Christmas Island)',
    'CC' => 'CC (Cocos (Keeling) Islands)',
    'CO' => 'CO (Colombia)',
    'KM' => 'KM (Comoros)',
    'CG' => 'CG (Congo)',
    'CD' => 'CD (Congo, the Democratic Republic of the)',
    'CK' => 'CK (Cook Islands)',
    'CR' => 'CR (Costa Rica)',
    'CI' => 'CI (Cote D\'Ivoire)',
    'HR' => 'HR (Croatia)',
    'CU' => 'CU (Cuba)',
    'CY' => 'CY (Cyprus)',
    'CZ' => 'CZ (Czech Republic)',
    'DK' => 'DK (Denmark)',
    'DJ' => 'DJ (Djibouti)',
    'DM' => 'DM (Dominica)',
    'DO' => 'DO (Dominican Republic)',
    'EC' => 'EC (Ecuador)',
    'EG' => 'EG (Egypt)',
    'SV' => 'SV (El Salvador)',
    'GQ' => 'GQ (Equatorial Guinea)',
    'ER' => 'ER (Eritrea)',
    'EE' => 'EE (Estonia)',
    'ET' => 'ET (Ethiopia)',
    'FK' => 'FK (Falkland Islands (Malvinas))',
    'FO' => 'FO (Faroe Islands)',
    'FJ' => 'FJ (Fiji)',
    'FI' => 'FI (Finland)',
    'FR' => 'FR (France)',
    'GF' => 'GF (French Guiana)',
    'PF' => 'PF (French Polynesia)',
    'TF' => 'TF (French Southern Territories)',
    'GA' => 'GA (Gabon)',
    'GM' => 'GM (Gambia)',
    'GE' => 'GE (Georgia)',
    'DE' => 'DE (Germany)',
    'GH' => 'GH (Ghana)',
    'GI' => 'GI (Gibraltar)',
    'GR' => 'GR (Greece)',
    'GL' => 'GL (Greenland)',
    'GD' => 'GD (Grenada)',
    'GP' => 'GP (Guadeloupe)',
    'GU' => 'GU (Guam)',
    'GT' => 'GT (Guatemala)',
    'GG' => 'GG (Guernsey)',
    'GN' => 'GN (Guinea)',
    'GW' => 'GW (Guinea-Bissau)',
    'GY' => 'GY (Guyana)',
    'HT' => 'HT (Haiti)',
    'HM' => 'HM (Heard Island and Mcdonald Islands)',
    'VA' => 'VA (Holy See (Vatican City State))',
    'HN' => 'HN (Honduras)',
    'HK' => 'HK (Hong Kong)',
    'HU' => 'HU (Hungary)',
    'IS' => 'IS (Iceland)',
    'IN' => 'IN (India)',
    'ID' => 'ID (Indonesia)',
    'IR' => 'IR (Iran, Islamic Republic of)',
    'IQ' => 'IQ (Iraq)',
    'IE' => 'IE (Ireland)',
    'IM' => 'IM (Isle of Man)',
    'IL' => 'IL (Israel)',
    'IT' => 'IT (Italy)',
    'JM' => 'JM (Jamaica)',
    'JP' => 'JP (Japan)',
    'JE' => 'JE (Jersey)',
    'JO' => 'JO (JORDAN)',
    'KZ' => 'KZ (Kazakhstan)',
    'KE' => 'KE (Kenya)',
    'KI' => 'KI (Kiribati)',
    'KP' => 'KP (Korea, Democratic People\'s Republic of)',
    'KR' => 'KR (Korea, Republic of)',
    'KW' => 'KW (Kuwait)',
    'KG' => 'KG (Kyrgyzstan)',
    'LA' => 'LA (Lao People\'s Democratic Republic)',
    'LV' => 'LV (Latvia)',
    'LB' => 'LB (Lebanon)',
    'LS' => 'LS (Lesotho)',
    'LR' => 'LR (Liberia)',
    'LY' => 'LY (Libyan Arab Jamahiriya)',
    'LI' => 'LI (Liechtenstein)',
    'LT' => 'LT (Lithuania)',
    'LU' => 'LU (Luxembourg)',
    'MO' => 'MO (Macao)',
    'MK' => 'MK (Macedonia, the Former Yugoslav Republic of)',
    'MG' => 'MG (Madagascar)',
    'MW' => 'MW (Malawi)',
    'MY' => 'MY (Malaysia)',
    'MV' => 'MV (Maldives)',
    'ML' => 'ML (Mali)',
    'MT' => 'MT (Malta)',
    'MH' => 'MH (Marshall Islands)',
    'MQ' => 'MQ (Martinique)',
    'MR' => 'MR (Mauritania)',
    'MU' => 'MU (Mauritius)',
    'YT' => 'YT (Mayotte)',
    'MX' => 'MX (Mexico)',
    'FM' => 'FM (Micronesia, Federated States of)',
    'MD' => 'MD (Moldova, Republic of)',
    'MC' => 'MC (Monaco)',
    'MN' => 'MN (Mongolia)',
    'ME' => 'ME (Montenegro)',
    'MS' => 'MS (Montserrat)',
    'MA' => 'MA (Morocco)',
    'MZ' => 'MZ (Mozambique)',
    'MM' => 'MM (Myanmar)',
    'NA' => 'NA (Namibia)',
    'NR' => 'NR (Nauru)',
    'NP' => 'NP (Nepal)',
    'NL' => 'NL (Netherlands)',
    'AN' => 'AN (Netherlands Antilles)',
    'NC' => 'NC (New Caledonia)',
    'NZ' => 'NZ (New Zealand)',
    'NI' => 'NI (Nicaragua)',
    'NE' => 'NE (Niger)',
    'NG' => 'NG (Nigeria)',
    'NU' => 'NU (Niue)',
    'NF' => 'NF (Norfolk Island)',
    'MP' => 'MP (Northern Mariana Islands)',
    'NO' => 'NO (Norway)',
    'OM' => 'OM (Oman)',
    'PK' => 'PK (Pakistan)',
    'PW' => 'PW (Palau)',
    'PS' => 'PS (Palestinian Territory, Occupied)',
    'PA' => 'PA (Panama)',
    'PG' => 'PG (Papua New Guinea)',
    'PY' => 'PY (Paraguay)',
    'PE' => 'PE (Peru)',
    'PH' => 'PH (Philippines)',
    'PN' => 'PN (PITCAIRN)',
    'PL' => 'PL (Poland)',
    'PT' => 'PT (Portugal)',
    'PR' => 'PR (Puerto Rico)',
    'QA' => 'QA (Qatar)',
    'RE' => 'RE (Reunion)',
    'RO' => 'RO (Romania)',
    'RU' => 'RU (Russian Federation)',
    'RW' => 'RW (Rwanda)',
    'BL' => 'BL (Saint Barthelemy)',
    'SH' => 'SH (Saint Helena)',
    'KN' => 'KN (Saint Kitts and Nevis)',
    'LC' => 'LC (Saint Lucia)',
    'MF' => 'MF (Saint Martin)',
    'PM' => 'PM (Saint Pierre and Miquelon)',
    'VC' => 'VC (Saint Vincent and The Grenadines)',
    'WS' => 'WS (Samoa)',
    'SM' => 'SM (San Marino)',
    'ST' => 'ST (Sao Tome and Principe)',
    'SA' => 'SA (Saudi Arabia)',
    'SN' => 'SN (Senegal)',
    'RS' => 'RS (Serbia)',
    'SC' => 'SC (Seychelles)',
    'SL' => 'SL (Sierra Leone)',
    'SG' => 'SG (Singapore)',
    'SK' => 'SK (Slovakia)',
    'SI' => 'SI (Slovenia)',
    'SB' => 'SB (Solomon Islands)',
    'SO' => 'SO (Somalia)',
    'ZA' => 'ZA (South Africa)',
    'GS' => 'GS (South Georgia and Sandwich Islands)',
    'ES' => 'ES (Spain)',
    'LK' => 'LK (Sri Lanka)',
    'SD' => 'SD (Sudan)',
    'SR' => 'SR (Suriname)',
    'SJ' => 'SJ (Svalbard and Jan Mayen)',
    'SZ' => 'SZ (Swaziland)',
    'SE' => 'SE (Sweden)',
    'CH' => 'CH (Switzerland)',
    'SY' => 'SY (Syrian Arab Republic)',
    'TW' => 'TW (Taiwan, Province of China)',
    'TJ' => 'TJ (Tajikistan)',
    'TZ' => 'TZ (Tanzania, United Republic of)',
    'TH' => 'TH (Thailand)',
    'TL' => 'TL (Timor-Leste)',
    'TG' => 'TG (Togo)',
    'TK' => 'TK (Tokelau)',
    'TO' => 'TO (Tonga)',
    'TT' => 'TT (Trinidad and Tobago)',
    'TN' => 'TN (Tunisia)',
    'TR' => 'TR (Turkey)',
    'TM' => 'TM (Turkmenistan)',
    'TC' => 'TC (Turks and Caicos Islands)',
    'TV' => 'TV (Tuvalu)',
    'UG' => 'UG (Uganda)',
    'UA' => 'UA (Ukraine)',
    'AE' => 'AE (United Arab Emirates)',
    'GB' => 'GB (United Kingdom)',
    'US' => 'US (United States)',
    'UM' => 'UM (United States Minor Outlying Islands)',
    'UY' => 'UY (Uruguay)',
    'UZ' => 'UZ (Uzbekistan)',
    'VU' => 'VU (Vanuatu)',
    'VE' => 'VE (Venezuela, Bolivarian Republic of)',
    'VN' => 'VN (Vietnam)',
    'VG' => 'VG (Virgin Islands, British)',
    'VI' => 'VI (Virgin Islands, U.S.)',
    'WF' => 'WF (Wallis and Futuna)',
    'EH' => 'EH (Western Sahara)',
    'YE' => 'YE (Yemen)',
    'ZM' => 'ZM (Zambia)',
    'ZW' => 'ZW (Zimbabwe)',
    'OTHER' => "- other -"
  );
/*
  $about=array (
    'reg_vmusician'     => 'Musician or composer',
    'reg_vdj'           => 'DJ',
    'reg_vswdeveloper'  => 'Software Developer',
    'reg_vhwdeveloper'  => 'Hardware Developer',
    'reg_vswuser'       => 'Software User',
//  'reg_vmediapro'     => 'Media Professional',
    'reg_vmproducer'    => 'Music Producer',
    'reg_vvproducer'    => 'Video Producer',
    'reg_vresearcher'   => 'Researcher',
    'reg_vpress'        => 'Press',
    'reg_vinterested'   => 'Just interested',
    'reg_vother'        => 'Other'
	);
 */
?>




<form action="index.php#registration" method="post">

<fieldset class="fs">
  <input name="page" type="hidden" value="<?php echo $page;?>"/>
  <legend>Personal Information:</legend>
  <label class="la" for="reg_prename"><span class="error">*</span>Given Name(s):</label>
  <input id="reg_prename" name="reg_prename" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_prename'])) echo rawurldecode($_POST['reg_prename']);?>"/>
  <br />
  <label class="la" for="reg_name"><span class="error">*</span>Family Name:</label>
  <input id="reg_name" name="reg_name" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_name'])) echo rawurldecode($_POST['reg_name']);?>"/>
  <br />
  <label class="la" for="reg_tagline">Tagline<small>(Affiliation, Company, Pseudonym,&hellip;)</small>:</label>
  <input id="reg_tagline" name="reg_tagline" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_tagline'])) echo rawurldecode($_POST['reg_tagline']);?>"/>
  <br/>
  <label class="ls">Note: The tagline will appear with your name on the badge.</label>
  <br/>
  <label class="la" for="reg_email"><span class="error">*</span>E-Mail address:</label>
  <input id="reg_email" name="reg_email" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_email'])) echo rawurldecode($_POST['reg_email']);?>"/>
  <div style="display:none;">Humans, leave this empty:</div><input name="reg_email_confirm" type="text" size="50" maxlength="100" class="fx" value=""/>
  <br/>
  <label class="la" for="reg_country"><span class="error">*</span>Country:</label>
  <select name="reg_country" id="reg_country" size="1">
<?php gen_reg_options($ctry, 'reg_country'); ?>
  </select>
  <br/>
  <label class="la" for="reg_agegroup">Age:</label>
  <select name="reg_agegroup" id="reg_agegroup" size="1">
<?php gen_reg_options($ages, 'reg_agegroup'); ?>
  </select>
  <br/>
</fieldset>

<fieldset class="fa">
<legend>Conference specific:</legend>
  <label>There will be conference proceedings
  available for a fee of about &euro;20.</label><br/>
  <div class="la"><label class="la"><span class="error">*</span>Are you interested in buying a copy?</label></div>
  <div class="ra">
    <label><input type="radio" name="reg_proceedings" value="0"<?php _ck('reg_proceedings',0);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_proceedings" value="1"<?php _ck('reg_proceedings',1);?>/>Yes</label>
  </div>
  <br/>
	<label>There will be an <a href="<?=local_url('excursion')?>" rel="external">excursion on Sunday</a>.</label><br/>
  <div class="la"><label class="la"><span class="error">*</span>Are you interested in joining?</label></div>
  <div class="ra">
    <label><input type="radio" name="reg_burschenschank" value="0"<?php _ck('reg_burschenschank',0);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_burschenschank" value="1"<?php _ck('reg_burschenschank',1);?>/>Yes</label>
  </div>
  <br/>
  <label>There will be conference T-Shirts
  available for a fee of about &euro;15 (various sizes and colours will be available).</label><br/>
  <div class="la"><label class="la"><span class="error">*</span>Are you interested in buying a one?</label></div>
  <div class="ra">
    <label><input type="radio" name="reg_teeshirt" value="0"<?php _ck('reg_teeshirt',0);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_teeshirt" value="1"<?php _ck('reg_teeshirt',1);?>/>Yes</label>
  </div>
  <label class="ls">Note: Neither proceedings nor t-shirts and excussion-participation are binding orders; we just like an estimated count.</label>
  <label class="ls">Another note: Selecting "Yes" does not guarantee you a T-shirt (in your favourite colour and size) nor a seat in the bus. Please check back at the registration desk.</label>
  <br/>
  <br/>
  <label>Allow public listing of your name and affiliation in the "Who else is coming" list.</label><br/>
  <div class="la"><label class="la"><span class="error">*</span>Include me?</label></div>
  <div class="ra">
    <label><input type="radio" name="reg_whoelselist" value="0"<?php _ck('reg_whoelselist',0);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_whoelselist" value="1"<?php _ck('reg_whoelselist',1);?>/>Yes</label>
  </div>
</fieldset>

<fieldset class="fa">
  <legend>About yourself:</legend>
  <div class="la"><label class="la">Do you work for a professional audio company?</label></div>
  <div class="ra">
  <span>
    <label><input type="radio" name="reg_audiopro" value="1"<?php _ck('reg_audiopro',1);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_audiopro" value="2"<?php _ck('reg_audiopro',2);?>/>Yes</label>
  </span>
  </div>
  <div class="la"><label class="la">Profession:</label></div>
  <div class="ra">
  <span>
  <label><input type="radio" name="reg_profession" value="Student"<?php _ck('reg_profession','Student');?>/>Student</label> &nbsp;
  <label><input type="radio" name="reg_profession" value="Employed"<?php _ck('reg_profession','Employed');?>/>Employed</label> &nbsp;
  <label><input type="radio" name="reg_profession" value="Freelance"<?php _ck('reg_profession','Freelance');?>/>Freelance</label> &nbsp;
  <label><input type="radio" name="reg_profession" value="Other"<?php _ck('reg_profession','Other');?>/>Other</label>
  </span>
  </div>
  <div class="la"><label class="la">You are using GNU/Linux&hellip;</label></div>
  <div class="ra">
   <span>
    <label><input type="checkbox" name="reg_useathome" value="1"<?php _ck('reg_useathome',1);?>/>&hellip;at home.</label> &nbsp; &nbsp;
    <label><input type="checkbox" name="reg_useatwork" value="1"<?php _ck('reg_useatwork',1);?>/>&hellip;at work.</label>
   </span>
  </div>
  <label class="la" for="reg_about">What is your relation to music, sound, Linux and/or Open Source Software? (composer, programmer, engineer, etc)</label><br/>
  <div class="la"><label class="la">I am &hellip;</label></div>
  <div class="ra">
	  <input id="reg_about" name="reg_about" type="text" size="60" maxlength="255" value="<?php if (isset($_POST['reg_about'])) echo rawurldecode($_POST['reg_about']);?>"/>
  </div>
</fieldset>

<fieldset class="fs">
<legend>Miscellaneous:</legend>
  <label class="la" for="reg_notes" style="float:left;">Remarks:</label>
  <textarea id="reg_notes" name="reg_notes" rows="3" cols="60"><?php if (isset($_POST['reg_notes'])) echo rawurldecode($_POST['reg_notes']);?></textarea><br/>
</fieldset><p></p>
<div>
  <div style="float:right;">
<input type="submit" class="button" value="Submit registration"/></div>
  <div>
<input type="reset" class="button" value="Reset form"/></div>
</div>

</form>
</div>

