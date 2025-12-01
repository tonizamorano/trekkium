<?php
// Añadir regiones de países que faltan
add_filter('woocommerce_states', 'trekkium_custom_woocommerce_states');
function trekkium_custom_woocommerce_states($states) {

    // --- MODIFICACIÓN / SOBRESCRITURA DE ESPAÑA (ES) ---
    // Sobrescribimos la lista por defecto de España para incluir
    // Girona, Lleida y otros nombres personalizados.
    $states['ES'] = array(
        'A'  => 'Alicante',
        'AB' => 'Albacete',
        'AL' => 'Almería',
        'AV' => 'Ávila',
        'B' => 'Barcelona',
        'BA' => 'Badajoz',
        'BI' => 'Bizkaia', 
        'BU' => 'Burgos',
        'C'  => 'A Coruña',
        'CA' => 'Cádiz',
        'CC' => 'Cáceres',
        'CE' => 'Ceuta',
        'CO' => 'Córdoba',
        'CR' => 'Ciudad Real',
        'CS' => 'Castellón',
        'CU' => 'Cuenca',
        'GC' => 'Las Palmas',
        'GI' => 'Girona',        
        'GR' => 'Granada',
        'GU' => 'Guadalajara',
        'H'  => 'Huelva',
        'HU' => 'Huesca',
        'J'  => 'Jaén',
        'LE' => 'León',
        'L'  => 'Lleida', 
        'LO' => 'La Rioja',     
        'LU' => 'Lugo',
        'M'  => 'Madrid',
        'MA' => 'Málaga',
        'ML' => 'Melilla',
        'MU' => 'Murcia',
        'NA' => 'Navarra',
        'OR' => 'Ourense',
        'P'  => 'Palencia',
        'PM' => 'Illes Balears',    
        'PO' => 'Pontevedra',
        'S'  => 'Cantabria',
        'SA' => 'Salamanca',
        'SE' => 'Sevilla',
        'SG' => 'Segovia',
        'SO' => 'Soria',
        'SS' => 'Gipuzkoa',
        'T'  => 'Tarragona',
        'TE' => 'Teruel',
        'TF' => 'Santa Cruz de Tenerife',
        'TO' => 'Toledo',
        'V'  => 'Valencia',
        'VA' => 'Valladolid',
        'VI' => 'Araba', 
        'Z'  => 'Zaragoza',
        'ZA' => 'Zamora',
    );
    // --- FIN MODIFICACIÓN ESPAÑA (ES) ---

    $states['FR'] = array(
        'IDF' => 'Île-de-France',
        'ARA' => 'Auvergne-Rhône-Alpes',
        'NAQ' => 'Nouvelle-Aquitaine',
        'OCC' => 'Occitanie',
        'PACA' => 'Provence-Alpes-Côte d\'Azur',
        'HDF' => 'Hauts-de-France',
        'BFC' => 'Bourgogne-Franche-Comté',
        'BRE' => 'Bretagne',
        'CVL' => 'Centre-Val de Loire',
        'GES' => 'Grand Est',
        'PDL' => 'Pays de la Loire',
        'NOR' => 'Normandie',
        'COR' => 'Corse',
    );

    $states['AD'] = array(
        'AND' => 'Andorra la Vella',
        'CAN' => 'Canillo',
        'ENC' => 'Encamp',
        'LUS' => 'La Massana',
        'ORD' => 'Ordino',
        'SAN' => 'Sant Julià de Lòria',
    );

    $states['AT'] = array(
        'BUR'  => 'Burgenland',
        'KTN'  => 'Kärnten',
        'NÖ'   => 'Niederösterreich',
        'OÖ'   => 'Oberösterreich',
        'SBG'  => 'Salzburg',
        'STMK' => 'Steiermark',
        'T'    => 'Tirol',
        'V'    => 'Vorarlberg',
        'W'    => 'Wien',
    );

    $states['NL'] = array(
        'DR' => 'Drenthe',
        'FL' => 'Flevoland',
        'FR' => 'Friesland',
        'GD' => 'Gelderland',
        'GR' => 'Groningen',
        'LB' => 'Limburg',
        'NB' => 'Noord-Brabant',
        'NH' => 'Noord-Holland',
        'OV' => 'Overijssel',
        'UT' => 'Utrecht',
        'ZH' => 'Zuid-Holland',
    );

    $states['PT'] = array(
        'AL'    => 'Alentejo',
        'ALG'   => 'Algarve',
        'ARA'   => 'Região Autónoma dos Açores',
        'RAM'   => 'Região Autónoma da Madeira',
        'LIS'   => 'Lisboa',
        'CTR'   => 'Centro',
        'NORTE' => 'Norte',
    );

    $states['BE'] = array(
        'VAN' => 'Amberes',
        'WBR' => 'Brabante Valón',
        'VBR' => 'Brabante Flamenco',
        'HAI' => 'Hainaut',
        'LIE' => 'Lieja',
        'LIM' => 'Limburgo',
        'LUX' => 'Luxemburgo',
        'NAM' => 'Namur',
        'OVL' => 'Flandes Oriental',
        'WVL' => 'Flandes Occidental'
    );

    $states['CY'] = array(
        'NI' => 'Nicosia',
        'LI' => 'Limassol',
        'LA' => 'Lárnaca',
        'PA' => 'Pafos',
        'FA' => 'Famagusta',
        'KY' => 'Kýrenia'
    );

    $states['DK'] = array(
        'H' => 'Hovedstaden',
        'M' => 'Midtjylland',
        'N' => 'Nordjylland',
        'S' => 'Sjælland',
        'SD' => 'Syddanmark'
    );

    $states['SK'] = array(
        'BA' => 'Bratislava',
        'TA' => 'Trnava',
        'NI' => 'Nitra',
        'TC' => 'Trenčín',
        'ZI' => 'Žilina',
        'BB' => 'Banská Bystrica',
        'PV' => 'Prešov',
        'KE' => 'Košice'
    );

    $states['SI'] = array(
        'GO' => 'Gorenjska',
        'GS' => 'Goriška',
        'KO' => 'Koroška',
        'NO' => 'Notranjsko-kraška',
        'OB' => 'Obalno-kraška',
        'OS' => 'Osrednjeslovenska',
        'PO' => 'Pomurska',
        'POH' => 'Podravska',
        'SA' => 'Savinjska',
        'SP' => 'Spodnjeposavska',
        'ZS' => 'Zasavska'
    );

    $states['EE'] = array(
        'HA' => 'Harju',
        'HI' => 'Hiiu',
        'ID' => 'Ida-Viru',
        'JA' => 'Järva',
        'JO' => 'Jõgeva',
        'LA' => 'Lääne',
        'LV' => 'Lääne-Viru',
        'PA' => 'Pärnu',
        'PO' => 'Põlva',
        'RA' => 'Rapla',
        'SA' => 'Saare',
        'TA' => 'Tartu',
        'VA' => 'Valga',
        'VI' => 'Viljandi',
        'VO' => 'Võru'
    );

    $states['FI'] = array(
        'ES' => 'Uusimaa',
        'VS' => 'Varsinais-Suomi',
        'PI' => 'Pirkanmaa',
        'KA' => 'Kanta-Häme',
        'SA' => 'Satakunta',
        'KE' => 'Keski-Suomi',
        'PO' => 'Pohjanmaa',
        'LA' => 'Lappi',
        'KU' => 'Kymenlaakso',
        'EK' => 'Etelä-Karjala'
    );

    $states['IS'] = array(
        'HO' => 'Höfuðborgarsvæðið',
        'SU' => 'Suðurnes',
        'VE' => 'Vesturland',
        'VF' => 'Vestfirðir',
        'NO' => 'Norðurland vestra',
        'NE' => 'Norðurland eystra',
        'SUO' => 'Suðurland'
    );

    $states['LV'] = array(
        'RI' => 'Riga',
        'KU' => 'Kurzeme',
        'ZE' => 'Zemgale',
        'VI' => 'Vidzeme',
        'LA' => 'Latgale'
    );

    $states['LI'] = array(
        'BA' => 'Balzers',
        'ES' => 'Eschen',
        'GA' => 'Gamprin',
        'MA' => 'Mauren',
        'PL' => 'Planken',
        'RU' => 'Ruggell',
        'SC' => 'Schellenberg',
        'TR' => 'Triesen',
        'TB' => 'Triesenberg',
        'VA' => 'Vaduz'
    );

    $states['LT'] = array(
        'AL' => 'Alytus',
        'KA' => 'Kaunas',
        'KL' => 'Klaipėda',
        'MA' => 'Marijampolė',
        'PA' => 'Panevėžys',
        'SI' => 'Šiauliai',
        'TA' => 'Tauragė',
        'TE' => 'Telšiai',
        'UT' => 'Utena',
        'VI' => 'Vilnius'
    );

    $states['LU'] = array(
        'CA' => 'Capellen',
        'CL' => 'Clervaux',
        'DI' => 'Diekirch',
        'EC' => 'Echternach',
        'ES' => 'Esch-sur-Alzette',
        'GR' => 'Grevenmacher',
        'LU' => 'Luxembourg',
        'ME' => 'Mersch',
        'RE' => 'Remich',
        'VI' => 'Vianden',
        'WI' => 'Wiltz'
    );

    $states['MT'] = array(
        'GO' => 'Gozo',
        'MA' => 'Malta'
    );

    $states['NO'] = array(
        'OS' => 'Oslo',
        'AK' => 'Akershus',
        'RO' => 'Rogaland',
        'TR' => 'Trøndelag',
        'HO' => 'Hordaland',
        'TE' => 'Telemark',
        'FI' => 'Finnmark',
        'MO' => 'Møre og Romsdal',
        'OP' => 'Oppland',
        'HE' => 'Hedmark'
    );

    $states['PL'] = array(
        'DS' => 'Baja Silesia',
        'KP' => 'Cuyavia-Pomerania',
        'LU' => 'Lublin',
        'LB' => 'Lubusz',
        'LD' => 'Łódź',
        'MA' => 'Pequeña Polonia',
        'MZ' => 'Mazovia',
        'OP' => 'Opole',
        'PK' => 'Subcarpacia',
        'PD' => 'Podlaskie',
        'PM' => 'Pomerania',
        'SL' => 'Silesia',
        'SW' => 'Santa Cruz',
        'WM' => 'Varmia-Masuria',
        'WP' => 'Gran Polonia',
        'ZP' => 'Pomerania Occidental'
    );

    $states['CZ'] = array(
        'PR' => 'Praga',
        'ST' => 'Bohemia Central',
        'PL' => 'Pilsen',
        'KA' => 'Karlovy Vary',
        'US' => 'Ústí nad Labem',
        'LB' => 'Liberec',
        'HK' => 'Hradec Králové',
        'PA' => 'Pardubice',
        'OL' => 'Olomouc',
        'MS' => 'Moravia-Silesia',
        'JM' => 'Moravia Meridional',
        'ZL' => 'Zlín'
    );

    $states['SE'] = array(
        'AB' => 'Estocolmo',
        'AC' => 'Västerbotten',
        'BD' => 'Norrbotten',
        'C' => 'Uppsala',
        'D' => 'Södermanland',
        'E' => 'Östergötland',
        'F' => 'Jönköping',
        'G' => 'Kronoberg',
        'H' => 'Kalmar',
        'I' => 'Gotland',
        'K' => 'Blekinge',
        'M' => 'Skåne',
        'N' => 'Halland',
        'O' => 'Västra Götaland',
        'S' => 'Värmland',
        'T' => 'Örebro',
        'U' => 'Västmanland',
        'W' => 'Dalarna',
        'X' => 'Gävleborg',
        'Y' => 'Västernorrland',
        'Z' => 'Jämtland'
    );

    $states['CH'] = array(
        'AG' => 'Aargau',
        'AI' => 'Appenzell Innerrhoden',
        'AR' => 'Appenzell Ausserrhoden',
        'BE' => 'Berna',
        'BL' => 'Basilea-Campiña',
        'BS' => 'Basilea-Ciudad',
        'FR' => 'Friburgo',
        'GE' => 'Ginebra',
        'GL' => 'Glaris',
        'GR' => 'Grisones',
        'JU' => 'Jura',
        'LU' => 'Lucerna',
        'NE' => 'Neuchâtel',
        'NW' => 'Nidwalden',
        'OW' => 'Obwalden',
        'SG' => 'San Galo',
        'SH' => 'Schaffhausen',
        'SO' => 'Soleura',
        'SZ' => 'Schwyz',
        'TG' => 'Turgovia',
        'TI' => 'Tesino',
        'UR' => 'Uri',
        'VD' => 'Vaud',
        'VS' => 'Valais',
        'ZG' => 'Zug',
        'ZH' => 'Zúrich'
    );


    return $states;
}
