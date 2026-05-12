<?php

use yii\db\Migration;

class m260512_182357_seed_clientes extends Migration
{
    private function emails(string $raw): string
    {
        if (!trim($raw)) return '[]';
        $parts = preg_split('/\s*[;\/\n]+\s*/', $raw);
        $out = [];
        foreach ($parts as $p) {
            $p = trim(preg_replace('/^(Cliente|correo[:\s]+)/i', '', trim($p)));
            $p = preg_replace('/\s.*$/', '', $p);
            if (filter_var($p, FILTER_VALIDATE_EMAIL)) {
                $out[] = ['label' => '', 'valor' => strtolower($p)];
            }
        }
        return json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function tel(string $raw): string
    {
        $raw = trim($raw);
        if (!$raw || $raw === '5.21662E+12') return '[]';
        return json_encode([['label' => '', 'valor' => $raw]], JSON_UNESCAPED_UNICODE);
    }

    private function wa(string $raw): string
    {
        $raw = trim($raw);
        if (!$raw) return '[]';
        if (preg_match('/^(.+?)\s+(\+?[\d][\d\s\-\.]{8,})$/', $raw, $m)
            && preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/', trim($m[1]))) {
            return json_encode([['label' => trim($m[1]), 'valor' => trim($m[2])]], JSON_UNESCAPED_UNICODE);
        }
        return json_encode([['label' => '', 'valor' => $raw]], JSON_UNESCAPED_UNICODE);
    }

    private function ins(array $r): void
    {
        [$nombre, $rfc, $contacto, $telRaw, $waRaw, $emailRaw] = $r;
        try {
            $this->insert('clientes', [
                'Nombre'            => trim($nombre),
                'Razon_social'      => trim($nombre),
                'RFC'               => $rfc ? strtoupper(trim($rfc)) : null,
                'Contacto_nombre'   => $contacto ? trim($contacto) : null,
                'Telefono'          => $this->tel($telRaw),
                'Whatsapp_contacto' => $this->wa($waRaw),
                'Correo'            => $this->emails($emailRaw),
                'Tiempo'            => 0,
                'Tipo_servicio'     => 'POLIZA',
                'Prioridad'         => 'Media',
                'Criticidad'        => 'Baja',
                'Estado'            => 1,
                'created_at'        => time(),
                'updated_at'        => time(),
            ]);
        } catch (\Exception $e) {
            echo "  [skip] {$nombre} — " . $e->getMessage() . "\n";
        }
    }

    public function up()
    {
        // [nombre, rfc, contacto_principal, telefono, whatsapp, emails]
        $clientes = [
            ['ABDDAN RECICLADOS SA DE CV','ARE1012091B6','AGUSTIN VARGAS','8116802560','Agustin Vargas 8116802560','hruvalcaba@abddan.com.mx;ruvalcabaster@gmail.com'],
            ['ABARROTES Y CARNES DIAZ','ACD171107RG4','MAYELA','+52 1 826 135 4874','Mayela Salazar 8261354874','gladys.gonzalez@desficosc.com/abarrotesdiaz219@hotmail.com'],
            ['ADRIAN CAVAZOS CAVAZOS','CACX681223J8A','CINTHIA LEAL / GLADYS GONZALEZ','+52 1 81 1822 7988','Cynthia Leal 8118227988','gladys.gonzalez@desficosc.com/oficina.viverosregionales@gmail.com'],
            ['ADMINISTRADORA Y OPERADORA DE CONCEPTOS LT (BUTCHER)','SSM220613KR2','ABEL SANTIAGO / FIDEL LANDAVERDE','13-52-37-31','FIDEL LANDAVERDE 5515075101','abel@looptonic.com;gerente.administrativo@looptonic.com'],
            ['ALFREDO NEGRETE','NESA870330I34','ALFREDO NEGRETE','+52 1 81 1475 1410','','anegrete.ocaba@outlook.com;alfredo.negrete.sanchez@gmail.com'],
            ['ALIMENTOS SANOS DEL CENTRO SA DE CV (POLLO FELIZ)','ASC1104193JA','C.P. OSVALDO ARREOLA','+52 1 442 274 4913','Osvaldo Arreola 4427449139','facturaspfch@gmail.com'],
            ['ALMACENES DE TRANSPORTES GARY ELIZABETH TORRE','TOLE771021JS4','SONIA','8116390292','Sonia 8180913803','logistica@tgary.com.mx;transportesgary@hotmail.com'],
            ['ANTAR GLOBAL SERVICES','AGS920917NN9','RUBEN TREVIÑO / MIRZA TORRES / NORMA','13-52-37-31','Mirza 8116310987','rubent@antar.com.mx;mirzat@antar.com.mx'],
            ['ALAN GONZALEZ','GOGA960515J17','ALAN GONZALEZ','81 2152 2244','Alan Gonzalez 8121522244','transportes.aogg@gmail.com'],
            ['AOF ADMINISTRADORA DE FRANQUICIAS','STM1312101E7','DIANA CEPEDA / C.P. CLEMENTE MARTINEZ','','C.P. CLEMENTE MARTINEZ 8180936667','clementemartinezgarcia@outlook.com;contabilidad.dominos@outlook.com'],
            ['AUTOLINEAS ADVANCE','AAD220805DD4','SERGIO ALANIS','81 1544 1208','Sergio Alanis 8115441208','autolineasadvance.pagos@gmail.com'],
            ['ASESORES EN SISTEMAS COMPUTACIONALES','','Denia Islas','','Denia Islas 6621872101','denia.islas@compuventas.com.mx'],
            ['ASOCIACION DE VECINOS DE COLONIA PRIVADA VALLE ALTO AC','AVC9102267W3','LIC. KARINA SAN MIGUEL','8115905559','Karina San Miguel 8115905559','coloniavallealto@gmail.com'],
            ['ASYSOP','ASY180808A93','ISRAEL NIETO','+52 1 81 8709 5236','ISRAEL NIETO 8187095236','israel.nieto@asysop.com.mx'],
            ['AUDIOMANIA SA DE CV','AUD98060564A','LUCY VILLAGRAN','8315-2669','Lucy Villagran 8183665470','lucy@audiomania.mx'],
            ['AVANT ROBOTICS','ARO140930BU0','Gabriela González','20-92-33-21','Gabriela Gonzalez 8180211130','contabilidad@avantrobotics.com;silvia@avantrobotics.com'],
            ['EL PAN DEL NUEVO MILENIO (BIEN JUGADOS CAFE)','PNM170502J47','CARLOS TREJO','+52 1 81 1611 8660','','ctrejo@panbenell.com;admon@panbenell.com;capitalhumano@panbenell.com'],
            ['BOCAPALMA CLUB DE SKI AC','BCS7808311U4','CP JOSE RODRIGO','81 8465 5716','Rodrigo Marquez 8184655716','contabilidad@bocapalma.com'],
            ['BRANDFORD','BPS010116VDA','LIC EVELYN GONZALEZ','87-48-91-00','Lic. Evelyn Gonzalez 8118008082','evelyn.gonzalez@bradfordcompany.com'],
            ['BEST TOOL & INJECTION 21','BTI210212BI0','LIC ERIKA TAMEZ','(81) 1100 5858','Erika Tamez 8118008008','besttool21@gmail.com'],
            ['BODEGAS DEL VIENTO (AGROPECUARIA EL TALENTO)','ATA0403258X7','ING. HERNAN DAVILA','52 1 844 263 2615','Ing. Hernan Davila 8442632615','mariafernanda@tracento.mx;hernandavila@tracento.mx'],
            ['BUHO MEDIA SHOP','BMS120126868','ING RAUL GARCIA / ALEJANDRO ZAMORA','81 8052 7317','Debanhi de la Cruz 8121128988','azamora@buhoms.com;adejesus@buhoms.com;auxiliarcontable@buhoms.com'],
            ['BYATLAS DE MAUT','BYA220223SNA','Claudia de Maut','+52 1 81 8687 7107','Claudia de Maut 8186877107','claudia@maut.mx'],
            ['CARNES EGH','CEG170831FB6','LOURDES FERNANDEZ','81 3646 6544','Jaqueline Salazar 8180915066','facturascarneshernandez@gmail.com'],
            ['C.I.ROMA SA DE CV','CRO1606034I6','ELIZABETH PERALES','8121707844','Elizabeth 8113683128','s.alimentacionroma@hotmail.com;facturacion.ciroma@gmail.com'],
            ['CAMISAS SERSA','CSE700107SN6','NOHEMI GARZA','8340-1850','Nohemi Garza 8182609616','garzanohemi@hotmail.com'],
            ['CAPITAL HUMANA SA DE CV','','C.P. ANDY MARTINEZ','81 2398 4626','Andy Martinez 8123984626','amartinez@capitalnatural.com.mx'],
            ['CARLOS DE LUNA DECANINI','LUDC6304222J6','CARLOS DE LUNA','+52 1 81 8250 6193','Norma Lozano 8117444201','c.deluna.d@gmail.com;normalozano978@gmail.com'],
            ['CARROCERIAS REFRIGERADAS SA DE CV (CARESA)','CRE980625213','ING. ARNOLDO DE LA MORA','8114-8426','Ing Arnoldo de la Mora 8180642841','arnoldo@carroceriascaresa.com.mx;administracion@carroceriascaresa.com.mx'],
            ['CAYAM (CENTRO DE ADAPTACION Y ATENCION AL MENOR AC)','CAA070712S79','EDITH VAZQUEZ','1580-0229','Eduardo Hernandez 8115285869','edithvazquez5@hotmail.com;cayam2000@hotmail.com;proyectoscayam@gmail.com'],
            ['CEIA (CENTRO EDUCACION INTEGRAL AVANZADA ABP)','CEI880614M46','MARIANA MACIAS','','Mariana 8120163416','ceiamtyadmi@hotmail.com;ceiamty@hotmail.com'],
            ['CENTRO DE CIRUGIA ENDOSCOPICA','CCE010105837','MINERVA RAZO / CAROLINA DE LA CRUZ','868 149-1123','Minerva Razo 8681977481','centrodecirugiaendoscopica@hotmail.com;cceauxiliar@gmail.com'],
            ['CERO GRADOS (ANTONIO FERNANDO VALENZUELA CASTELLANOS)','VACA590422A94','CP ANTONIO VALENZUELA','8338-6395','Maribel Martinez 8110021796','facturas@cerogradosmexico.com;ventas@cerogradosmexico.com'],
            ['CERO SIETE EDIFICACIONES (GUAJARDO CANTU)','CSE201208Q76','Yuliana Gonzalez','10-01-69-94','Yuliana Gonzalez 8181634710','ygonzalez@guajardocantusc.com;marinadeleon@guajardocantusc.com'],
            ['CHRISTIAN GUADALUPE LOPEZ ZAPATA','LOZC820801EH4','Christian Guadalupe Lopez Zapata','81 2239 0894','Homero Perez 8126153628','hperez@itecnomx.com;clopez@itecnomx.com'],
            ['CITRO GRUAS','CGR170302C7A','MARIANA RUBIO','+52 1 826 136 0292','Mariana 8261360292','administracion@citrogruas.com'],
            ['CNC INDUSTRIAL','CIN971013SP0','NANCY PEREZ / JOEL CERDA','81-89-01-09-05','Nancy Perez 8113807210','joel.cerda@cncindustrial.com.mx;nancykperez@cncindustrial.com.mx;recursos.humanos@cncindustrial.com.mx'],
            ['COAVIS MEXICO SA DE CV','KMS070601C44','PERLA E. LUCERO / SANDRA DONJUAN','81 8245 6520','Sandra San Juan 8112372316','perla.lucero@coavis.com;sandra.donjuan@coavis.com;alberto.marquez@coavis.com'],
            ['COLONOS DEL PORTAL DEL HUAJUCO','CPH930427L33','Aracely Castaneda Huerta','+52 1 81 3122 3983','+52 1 81 3122 3983','contabilidad@portaldelhuajuco.com'],
            ['COMERCIALIZADORA DE POLLOS CARNES CAVAZOS','CPC010426U17','MARIO SANTOS','8261010913','Mario Santos 8261010913','mario.santos@desficosc.com;contabilidad@kavazos.com.mx'],
            ['COMERCIALIZADORA DE ACCESORIOS EQUIPOS Y SUMINISTROS IND','CAE181002N10','IVONNE GPE URESTI LOPEZ','81-33-88-13-77','Isidro Jaramillo 8115187824','Ventas1@comercializadoraind.com.mx'],
            ['COINHOMEX (VIVEROS VALLE ALTO)','CIH981030CDA','ALFREDO CLEMENTE / HORACIO MTZ','52 1 81 2515 7365','Horacio Martinez 8125157365','aclemente@coinho.com.mx;contador@coinho.com.mx'],
            ['CONSORCIO EMPRESARIAL DE CONSULTORIA Y DESARROLLO','CECO50721FA7','MARIA TERESA LOPEZ','55 5995 5183','Maria Teresa Lopez 5559955183','mariateresa.l@codesamx.com'],
            ['CONSULTORIA LEGAL ARP','CLA081010EA6','KARLA ROSALES / KARLYN OSUNA','84784752','Karlyn Osuna 8180215587','karly_rosales@hotmail.com'],
            ['CONTEMOS JUNTOS PARA SUMAR AC','CJS2012185T3','C.P. JOSE LUIS JARQUIN','8111795115','Jose Luis Jarquin 8111795115','direccion@contemosjuntos.org;contabilidad@contemosjuntos.org;gerencia@contemosjuntos.org'],
            ['CRISTAL SEGURO SA DE CV','CSE070116K75','YESENIA J. AVENDANO RDZ.','81 1253 0109','Yesenia 8182809515','yesenia.avendano@cristalseguro.com.mx;contabilidad@cristalseguro.com.mx'],
            ['DANIEL RAMIREZ JIMENEZ','RAJD830509463','DANIEL RAMIREZ JIMENEZ','8112541581','Ray Lopez 8112541581','rlopez@raficonsultores.com'],
            ['DANIELA TREVINO','','DANIELA TREVINO','81 1908 3313','Daniela Trevino 8119083313','daniela.trevinos@outlook.com'],
            ['DANA MELISA MARTINEZ MEDINA','MAMD8407269G6','DANA MELISA MARTINEZ MEDINA','81 2010 5409','Danna Melisa Martinez 8120105409','dannamel21@gmail.com'],
            ['DAIRY QUEEN (CONSTRUCTORA GARZA CAVAZOS)','','C.P. MIRIAM SANDOVAL','866 212 6137','Luis Coronado 8442720530','facturaciongpogc@gmail.com;ing.roberto@gmail.com;finanzas@industrialpartners.mx'],
            ['DAIRY QUEEN (SERVICIOS TECNICOS ADMINISTRATIVOS GARZA)','','ING. ROBERTO GARZA VILLARREAL','844 461 9560','Miriam Sandoval 8662126137','ing.roberto@gmail.com;dairyqueen.adm@gmail.com;finanzas@industrialpartners.mx'],
            ['DAIRY QUEEN SALTILLO INDUSTRIAL LAND','SIL170324HT6','C.P. LUIS LANDEROS','844 160 4162','Luis Landeros 8441604162','finanzas@industrialpartners.mx'],
            ['DELTA DOBLE','DDO050707NXA','AIDEE VAZQUEZ / C.P. LEIDY MENDEZ','812 377-8876','Leidy 9932082277','aideemvr@hotmail.com;ladymdz@gmail.com'],
            ['DESPACHO COIN','TOME750715451','ELBA TORRES MADRIGAL','831 162 1626','Mariana Torres 8311152992','coin.citas@hotmail.com'],
            ['DESFICO SC','DSC90061175A','C.P. MISAEL MONTALVO','826 125 6503','Sandy Garza 8261256503','contacto@desficosc.com;miry.leon@desficosc.com'],
            ['DESPACHO LECHUGA ASOCIADOS','DLA210715T20','FERNANDO LECHUGA ZARZA','8040-2339','Laura 5563262540','anahi@despacholechuga.com;fernando@despacholechuga.com;esanchez@despacholechuga.com'],
            ['DIHMOSA (DISTRIBUIDORA HIDRAULICA MOBIL DE MEXICO)','DHM011206AM8','LAURA RANGEL','8120227223','Laura 8120227223','recursoshumanos@dihmosa.com;contabilidad@dihmosa.com'],
            ['DISTRIBEX DE MEXICO SA DE CV','DME950807V24','SOLEDAD HERNANDEZ / NORMA MARTINEZ','8340-0551','Sol Hernandez 8116528940','sol@distribex.net;normamtz@distribex.net'],
            ['DISTRIBUIDORA HERNANDEZ FLORES','DHF170831UQ1','GLADYS GONZALEZ','8261257653','Alma Hernandez 8117823297','gladys.gonzalez@desficosc.com;facturascarneshernandez@gmail.com'],
            ['DOEG SA DE CV','DOE180406IR4','LIC DIEGO ESCAMILLA','8116353213','Diego Escamilla 8116353213','diego@pound.mx;enrique@pound.mx'],
            ['ELIZABETH GONZALEZ MASEGOSA','ROME701217BZA','','','Elizabeth Rodriguez 8180271080',''],
            ['ELSA PATRICIA RAMIREZ GARZA','RAGE660128SZ2','ELSA PATRICIA RAMIREZ GARZA','+52 1 81 1049 4921','Elsa Patricia Ramirez 8110494921','patyraga@yahoo.com.mx'],
            ['EMPACADORA LA FAMA','EFA751001HMA','RUBI BRACAMONTES','+52 1 826 101 3780','Empacadora La Fama 8261013780','facturas@empacadoralafama.com'],
            ['ERIKA BALANDRANO (JULIAN MONTELONGO MARTINEZ)','MOMJ640109QI2','ERIKA BALANDRANO','8310-6476','Erika 8112371732','aideebalandrano@hotmail.com'],
            ['ESPECIALIDADES Y REPUESTOS PETROQUIMICOS (ESPYRE)','ERP000119818','ADRIANA VEGA / NANCY CHAPA','8355-4575','Nancy Chapa 8123511670','espyrep@espyre.com.mx;avega@espyre.com.mx;nchapa@espyre.com.mx'],
            ['ESPECIALISTAS EN FINANZAS Y ECONOMIA (CAPITA RIGHT PEOPLE)','EFE100408JN00','JAQUELINE JUAREZ','+52 1 664 312 9515','Angie Solis 5547279812','accounting@capitaworks.com;jjuarez@gcefe.com;asolis@gcefe.com'],
            ['EXPRESS TERAN','ETE081201I72','WENDY SILVA','826 135 8347','Wendy Silva 8261358347','wendy.silva@expressteran.com.mx'],
            ['FABRICACIONES TECNICAS INDUSTRIALES (FATISA)','FTI061027E70','ANGIE CERDA','8183546374','Angie Cerda 8126099431','administracion@fatisa.com.mx;ventas@fatisa.com.mx'],
            ['FELIPE DE JESUS ESCAMILLA','GUEF880605N34','JESSICA REYES MORALES','826 108 5447','Jessica Reyes 8261085447','jerm_2587@hotmail.com'],
            ['FERNANDA CESTELLOS','CEL820218UA2','FERNANDA CESTELLOS','+52 1 55 9191 5109','Yves Casas 5591915109','fernanda.cestellos@gmail.com'],
            ['FORRAJES ESCOBEDO SA DE CV','FES880924BN8','SRA. MARTHA / ERIKA CANTU','81 8384-2444','Gladys Cantu 8115304658','forrajesescobedo@yahoo.com.mx;gladys_cantu@hotmail.com'],
            ['FREDY OTONIEL CRUZ PEREZ','CUPF880219CV1','Fredy Otoniel Cruz Perez','+52 1 81 1229 5848','Fredy Cruz 8112295848','cyltex.cruper@gmail.com'],
            ['FRIDA CHILAQUILES / CAMUZZA','CAM160816KZ0','FERNANDO CARRILLO / CARLOS GARCIA','811 509 2902','Eduardo Garcia 8118021109','fernando.carrillo@camuzza.com;carlos.garcia@camuzza.com'],
            ['FUNDACION DE AGENTES ADUANALES PARA ESTANCIA INFANTIL','FAA040609S21','LIC. MARIA CORTES','5533007500','Deisy Guerrero 5520892740','mariadejesus.cortes@caaarem.mx;contabilidad.fundacion@caaarem.mx'],
            ['FUNDACION PARA UNIR Y DAR AC','FUD1203261M9','SANDRA RANGEL','19-72-10-91','Sandra Rangel 8187771427','s.rangel@comunidar.org;l.arroyo@comunidar.org'],
            ['GA DIESEL PARTS','GDP1409017S0','LIC RENE GARCIA','828 289 3253','Alvaro 5229225399','facturacion@dieselparts.mx;rh@dieselparts.mx'],
            ['GRUPO DAVINA / DAVA GERENCIA','DGP150724AL3','MELINA HERNANDEZ / EUGENIO GARZA','811-029-24-92','Eugenio Garza 8115144801','melina@grupodavina.com;eugenio.garza@davaproyectos.mx'],
            ['GRUPO HOSPITALARIO RODIVA (FONDA ARGENTINA)','GHR140331IL6','TOMAS VERDEJA / LIC JOSE ANTONIO MENCHERO','55 2770 3879','Tomas Verdeja 5527703879','tomas.verdeja@fondaargentina.com;jose.antonio@fondaargentina.com'],
            ['GRUPO PRO (SIBARITAS PROFESIONALES)','SPR1112156X9','ROGELIO ACOSTA / BRANDON GARCIA','8127559008','Roxana 8127559008','rogelio.acosta@grupopro.mx;contabilidad@grupopro.mx'],
            ['GRUPO RESTAURANTERO DE POKE SA DE CV','GRP1709295C7','SANDRA LOPEZ','','Sandra Lopez 8126981465','facturas@hoku.mx;sandra@hoku.mx'],
            ['GRUPO SIAYEC SA DE CV','GSI9211132TA','DAVID VALDEZ','+52 1 55 2715 6889','David Valdez 5527156889','dvaldez@grupo-siayec.com.mx;egomez@grupo-siayec.com.mx'],
            ['GUADALUPE DE JESUS SANTOS FRANCO','SAFG790304AL4','GUADALUPE DE JESUS SANTOS FRANCO','8112048131','Guadalupe Santos 8112048131','rockolasmonterreysadecv@gmail.com'],
            ['GUAJARDO CANTU Y ASOCIADOS SC','GCA990114CV6','CP ROMEO GUAJARDO / YULIANA','8340-5625','Romeo Guajardo 8114733567','ygonzalez@guajardocantusc.com;contabilidad@guajardocantusc.com;marinadeleon@guajardocantusc.com'],
            ['HALZAMEDIC SERVICIOS DE MEXICO','HSM1708183Z8','JESSICA GUTIERREZ','8123948420','Jessica Gutierrez 8123948420','halzamedic.servicios@gmail.com'],
            ['HERIBERTO BENSOR ZAPATA','BEZH8811256TA','HERIBERTO BENSOR ZAPATA','52 1 81 2665 5135','Heriberto Bensor 8126655135','heriberto251988@gmail.com'],
            ['HOMERO PEREZ','','HOMERO PEREZ','','+52 1 81 2615 3628',''],
            ['HUSKY MEXICO','HIM940415G15','CARLOS ROMERO / NORMA RIVERA','01 55 5089-1160','Norma Rivero 5591990218','nrivera@husky.ca;cromero@husky.ca;facturacionMexico@husky.ca'],
            ['HERSOTEC','HST011121H15','HUMBERTO CASTILLO','81 2006 8887','Ing Humberto Castillo 8120068887','dayala@hersotec.com'],
            ['IDEAS COMPUTADORAS','BACA870820FHA','GEORGINA','81 8997 6807','Gina Chavez 8189976807','ideacomputadoras@hotmail.com'],
            ['INSTALACIONES ELECTRICAS EN ALTA Y BAJA TENSION ACEVEDO','IEA900427QV4','PATRICIA ACEVEDO','8115719486','Patricia Acevedo 8115719486','patyacevedo@ieacevedo.com.mx;asistente@ieacevedo.com.mx'],
            ['INMOBILIARIA CUMA LAREDO','ICL060606D96','GERARDO JIMENEZ','+52 1 56 1014 7827','Gerardo Jimenez 4272272934','contabilidad@paradorcazadero.com'],
            ['INMOBILIARIA DISTRIBEX','IDI120627CS8','ING JAVIER ARGUELLEZ / C.P. NORMA MARTINEZ','81 1217 1685','Norma Martinez 8112171685','javierarguellesl@distribex.net;normamtz@distribex.net'],
            ['ING ALEJANDRO MARIANO','','ING ALEJANDRO MARIANO','4424246535','Alejandro Mariano 4424246535','buzon.donga@outlook.com'],
            ['INNOVAMED','INN140512MR0','LUIS ANTONIO ROBLEDO DAVILA','10-88-83-40','Luis Robledo 8117372112','luisantoniorobledo@innovamed.com.mx;yeraldhisanchez@innovamed.com.mx'],
            ['IRRI DREN DE MEXICO','IDM9305114B5','LIC RAYMUNDO GARCIA','668 130 0112','Luis Enrique 6688283159','carlos.baldenebro@irridren.com'],
            ['INOVA SA DE CV','INO760602912','CP LAZARO REYNA','8317-8250','Melissa Inova 8125227093','lrc@inova-mexico.com;rmg@inova-mexico.com;jcm@inova-mexico.com'],
            ['JOSE ALFREDO GOMEZ SANCHEZ','GOSA6406164Z4','JOSE ALFREDO GOMEZ SANCHEZ','','Alfredo Gomez 8120405748','agomez@gpomas.mx'],
            ['JOSE FERNANDO DE LA ROCHA','RORF610218610','JOSE FERNANDO DE LA ROCHA','52 46206028345','Ma. Dolores Castaneda 4620602834','facturasjosefernando@gmail.com;facturaspfch@gmail.com'],
            ['JOSE LUIS MARROQUIN','MARL741105D68','JOSE LUIS MARROQUIN','52 8261581852','Andrea 8261581852','tallerjal@outlook.com'],
            ['JP SAMSA','JPS991203DE4','CP PATRICIA LEAL / HECTOR MIGUEL CORTEZ','81 2139-6996','Hector Cortes 8182762287','hector.cortes@rfn.mx;kevin.hernandez@rfn.mx'],
            ['KLOECKNER METALS DE MEXICO','MPA021205K24','Dalia M. Rodriguez Carranza','81 1253 0800','Dalia 8115890449','drodriguez@kloecknermetals.com;cuentasxpagar@kloecknermetals.com'],
            ['LA COSTA REGIA','CRE130518P2A','GRACIELA GONZALEZ','81-18-72-77-44','Graciela Gonzalez 8131604644','lacostaregiafacturas@hotmail.com;grace.gzz93@gmail.com'],
            ['LA TORRADA (RENACIMIENTO GOURMET)','RGO1305133G0','OMAR GONZALEZ / GERARDO PARRA','+52 1 81 2040 0728','Omar Gonzalez 8120400728','cesar.gonzalezp@rgourmet.mx;gerardo.parra@rgourmet.mx'],
            ['LIDAG SA DE CV','LID8602076S1','GLORIA RODRIGUEZ','','Francisco 9612672703','inventarios@lidag.com;vicky.arellano@lidag.com'],
            ['MANUEL MATA RUIZ','MARM660522MR4','MANUEL MATA RUIZ / IRIS MATA','8327-8702','Iris Mata 8118149511','manuelmataruiz@prodigy.net.mx;volks.lpz@live.com'],
            ['MARGEN AROMA Y DESARROLLO','MAD051024RW2','Luisa Garcia','','+52 1 81 1330 5249','luisa.araujo@mardesa.com.mx'],
            ['MARIO ALBERTO PENA CANTU','PECM761213H6A','MARIO PENA','8342-3945','Mario Pena 8181851558','skydiver_mc@hotmail.com'],
            ['MARIO ALBERTO SANTOS ROBLEDO','SARM940911FP0','MARIO SANTOS','826 101 0913','Mario Santos 8261010913','santos9450@hotmail.com'],
            ['MARTHA CRISTINA SANTOS SALAS','SASM6306234K8','MARTHA CRISTINA SANTOS SALAS','81 1863 2728','Cristina Santos 8118632728','cristinasantos_despacho@hotmail.com'],
            ['MEXICO GLOBAL ALLIANCE NORESTE SC (MXGA)','MGA100603A89','C.P. CRISTINA MARTINEZ','8356-3300','Cristina Martinez 8185256960','cristina.martinez@mxga.mx;facturacion2@mxga.mx;nominas1@mxga.mx'],
            ['MESA COMPARTIDA (LE PAIN)','MCO230324BW8','LUISELA CRUZ / ARELY','461 169 9456','Arely Perez 4776509298','luisela@lpq.com.mx'],
            ['MIGUEL ROCHA','','MIGUEL ROCHA','811-015-86-19','Miguel Rocha 8110158619',''],
            ['MILLA INTERIORES','MIN240206AL0','','','+52 1 81 1908 3313','daniela@millainteriores.com'],
            ['MODELOS Y HERRAMENTALES DE MONTERREY','MHM0102023MA','ELIZA','8367-4408','Eliza 8182533787','ventas@myhmsa.com'],
            ['MONTALVO LAVER SC','MLA211008AW6','JAZMIN OSUNA','81 2567 8796','Jazmin Montalvo 8136014454','contacto@montalvolaver.com;jazosuna@montalvolaver.com'],
            ['MTZ INMOBILIARIA SA DE CV','MMO0501276P6','HILDA MARTINEZ','01 834 110 0723','Hilda Martinez 8341332612','nominas@mtzinmobiliaria.com'],
            ['MULTIELECTRICO SA DE CV','MUL841116182','LIC. ROSY MALIBRAN','(834) 318-2850','Rosy Malibran 8341596149','rmalibran@multielectrico.com;javierserna@multielectrico.com'],
            ['MULTILLANTAS EL POLACO','MPO040217MP9','LIC. AIDE HERNANDEZ','52 1 826 154 6315','Aide Hernandez 8261546315','multillantaselpolaco@live.com.mx'],
            ['NAMYANG NEXMO MEXICO','NNM210423GQ4','GABY SOSA','(811)2979276','Gaby 8120022747','gabrielasanjuan.olarte@nynexmo.com;marlene.manzanares@nynexmo.com'],
            ['NPC INDUSTRIAL DE MONTERREY SA DE CV','NIM1510213RA','GABRIELA SOSA SAN JUAN','811-278-6535','Alma Aranda 8121084520','npcmonte@gmail.com;alma.aranda@npcmex.com'],
            ['OES ENCLOSURES MANUFACTURING MEXICO','OEM1701232RA','C.P LUIS LAURO','52 1 81 1908 4029','C.P Luis Lauro 8119084029','luis.ruiz@oldcastle.com;Angel.Pecina@oldcastle.com'],
            ['OLUFIN','OLU2110068U9','C.P. EDUARDO SANCHEZ / JUAN CARLOS ABRAHAM','+52 1 222 769 3496','Jenny Fonseca 8115308383','juancarlos@olufin.com;eduardo@olufin.com'],
            ['OPERADORA TITOS DE ALIMENTOS SA DE CV','OTA180606RT8','LIC VERONICA MEIXUEIRO','81 1675 9593','Lic. Veronica Meixueiro 8116759593','titosalitas@gmail.com;vmeixueiro@hotmail.com'],
            ['ORCOTEC','CAZA671022H69','BEATRIZ CASANOVA / PAOLA CHAVEZ','83-47-97-77','Paola Chavez 8117935020','bcasanova@orcotec.com;facturacion@orcotec.com.mx;contabilidad@orcotec.com.mx'],
            ['OUM-HOLDING','OUM21110356A','MIGUEL RAMIREZ','52 1 866 174 1539','MIGUEL RAMIREZ 8120768071','Mramirez@creo.us;facturacion@oum.mx'],
            ['PAMELA OVIEDO','','PAMELA OVIEDO','8181382867','Pamela Oviedo 8181382867',''],
            ['PINTURAS OXIDOS DE MEXICO SA DE CV','PAB091020QV7','MARCELA BENAVIDES','8191-0760','Marcela Benavides 8115009533','marceb@oxi-mex.com;marcelabenavidesg@hotmail.com'],
            ['PROCESADORA DE ALIMENTOS MAX (LUGAR DE MAX)','PAM131209F41','RICARDO CUELLAR','8121469099','Ricardo Cuellar 8121469099','administracion@lugardemax.com'],
            ['PROVEEDORA DE DULCES Y DESECHABLES','PDD031204KL5','EDGARDO MORENO','8261010913','Edgardo Moreno 8261010913','admonproveedora@infinitummail.com;emr_18@hotmail.com'],
            ['PROVEEDORES INDUSTRIAL SALGAT (BUTCHER & SONS)','PIS230210HQ7','FIDEL LANDAVERDE / ABEL SANTIAGO','729 152 1896','Dulce 5518286279','abel@looptonic.com;contabilidad@randys.com.mx'],
            ['PROANSA SA DE CV','PRO950711170','JESSICA AYALA / FRANCISCO MORALES','','Lourdes 8117980356','contabilidad@proansa.com.mx;fmorales@proansa.com.mx'],
            ['PRODUCTORES DE ARBOLES Y PALMAS LOS ENCINOS','PAP060906N22','FIDENCIO TAMEZ','826 265 7429','Fidencio Tamez 8131269249','administracion@viverolosencinos.com'],
            ['PRONATURA NORESTE AC','PNO981230HQ4','SANTIAGO BARRIOS / LAURA LEIJA','83-45-10-45','Santiago Barrios 8187999053','sbarrios@pronaturane.org;lleija@pronaturane.org;agamboa@pronaturane.org'],
            ['PUNTO VALLE (VALORES PATRIMONIALES)','VPD1508183I3','ANGEL GUAJARDO','+52 1 81 8458 4736','ANGEL GUAJARDO 8184584736','aguajardo@puntovalle.com'],
            ['QUIMICOS INTEGRALES HALN SA DE CV','QIH071023CA6','CP. LORENA ARAMBURO','8320-3155','Lorena Aramburo 8180211395','laramburo@prodigy.net.mx'],
            ['RAPIDWIRE','RAP2111015M9','RUBICEL HERNANDEZ CONTRERAS','8113561235','Rubicel 9331205521','rubicel.rapidwire@gmail.com;marichalarrapidwire@gmail.com'],
            ['REFACCIONES Y EQUIPOS PARA AUTOTANQUES SA DE CV','REA200904USA','ALBERTO VALDEZ','81 2010 9914','Alberto Valdes 8120109902','administracion@repasa.com.mx'],
            ['REFACCIONARIA CAVAZOS','RCA02051572A','YARERI CAVAZOS','81 1941 1386','Yareri Cavazos 8119411386','yareri_nickte@hotmail.com'],
            ['REMOLQUES PYMES','RPY190731CI5','KAREN TAMEZ','+52 1 826 135 5673','KAREN TAMEZ 8261355673','karentmz0607@gmail.com'],
            ['RENCO INDUSTRIAL','RIN050310HF8','ARQ. GUSTAVO GONZALEZ','+52 1 81 1179 7355','ARQ. GUSTAVO GONZALEZ 8111797355','gustavo.sentoindustrial@gmail.com'],
            ['RESTAURANT LOS CURRICANES (ASESORES EMPRESARIALES REGORI)','AER160728CU2','FLOR GONZALEZ / MELIDA SOSA','FLOR GONZALEZ 812 1068873','Flor Gonzalez 8121068873','flor.gonzalez@loscurricanes.mx;contador.general@loscurricanes.mx'],
            ['RESPALDO FISCAL','RFN1505272G8','HECTOR CORTES','8182762287','Hector Cortes 8182762287','hector.cortes@rfn.mx;esmeralda.acosta@rfn.mx'],
            ['RFL LOGISTICS','RLO220726BS8','ALEJANDRA','','',''],
            ['ROBERT LOWE','LOFR630427QA6','ROBERT LOW','044 81-8029-1126','Dr Robert Low 8180291126','drlowe92@gmail.com'],
            ['ROLLER PRINT DE MEXICO','RPM080314CL3','DENISSE LOPEZ','81 2079 3821','Denisse Lopez 8120793821','administracion@rollerprint.com.mx'],
            ['SAMBO MOTORS','SMO1605022Y4','IVAN VAZQUEZ / CESAR OLMEDO / ISARI GARCIA','+52 1 81 2572 1018','Ivan Vazquez 8125721018','ivan.vazquez@sambomotors.com;isarigarcia@sambomotors.com'],
            ['SANTO RESTAURANT (GRUPO TARGJ)','GTA230317AI3','LIC. FABIAN VERASTEGUI','+52 1 55 5909 8588','LIC. FABIAN VERASTEGUI 5559098588','fabian.verastegui@santorestaurants.com;aux@santorestaurants.com'],
            ['SERVICIOS SEMAQSA','SSE1803085E9','VALERIA GARZA','+52 1 826 261 6362','Valeria Garza 8262616362','administracion@semaqsa.com.mx'],
            ['SERVICIOS EMPRESARIALES 77','SES180621KZ1','Contador Carlos Lopez','+52 1 81 1377 8560','Monse Moreno 8118201423','clopez@colds.com.mx'],
            ['SUPER DIAZ','SDI981102D82','ROSY DIAZ','826 170 2361','Rosy Diaz 8261702361','superdiaz_sdi98@hotmail.com'],
            ['SELENE BONILLA (SERVICIOS INTEGRALES MONTELONGO)','SIM1109059WA','SELENE BONILLA','811489-2592','Selene Bonilla 8119030876','selenebonilla1975@gmail.com'],
            ['SENTO INDUSTRIAL SA DE CV','SIN200320RN8','ARQ. GUSTAVO GONZALEZ','8188500728','Arq. Gustavo Gonzalez 8111797355','gustavo.sentoindustrial@gmail.com'],
            ['SERVICIOS INDUSTRIALES Y SOLUCIONES SISSA SA DE CV','SLO150112BZ2','JAVIER CISNEROS / Nathaly Banda','(81) 1771-5541','Javier Cisneros 8118171858','javier.cisneros@sissa.mx;guillermo.casillas@sissa.mx'],
            ['SHAKA (ODRS SRL DE CV)','ODR161115UEA','SERGIO SHIPPERS','5518334137','Sergio Shippers 5518334137','sergio@shaka.mx;info@shaka.mx'],
            ['SALDANA TORRES','STS950904A45','SOLEDAD SALDANA TORRES','821121 6297','Saldana Torres 8132592687','delfinosalda@gmail.com'],
            ['SITAARQUITECTOS','SIT231030855','ASTRID MENDOZA','8127337919','Astrid Mendoza 8127337919','astridmendoza@sitaarquitectos.com'],
            ['SOLUCIONES HEXAGONO','SHE190614KG0','MIRYAM HERNANDEZ','81 1470 1279','Miryam Hernandez 8114701279','miryam9815@hotmail.com'],
            ['SOLUCIONES TECNOLOGICAS','STN020425UU3','LAURA MACIAS','(81)8299-2139','Laura Macias 8181619297','lmacias@soluciones-tecnologicas.com'],
            ['SUMINISTROS DE INGENIERIA Y EQUIPOS DEL NORTE','SIE091202P92','CP ESMERALDA QUISTIAN RMZ SIEN','+52 1 444 511 7838','CP ESMERALDA QUISTIAN 4445117838',''],
            ['SUPER SERVICIO EL FRAILE SA DE CV (GARCIA)','SSF080605AD0','DIANA GONZALEZ','1492-7111','Alfredo Gomez 8120405748','superelfraile@gmail.com;contabilidad@gpomas.mx'],
            ['SUSHI DESTILADOS (LITIBU GRUPO EMPRESARIAL PRIVADO)','LGE201013FD4','ENRIQUE VAZQUEZ','52 1 33 1410 0615','Enrique Velazquez 3314100615','lcastillo@sushicentral.mx;finanzas@sushicentral.mx'],
            ['SUSHI DESTILADOS (CIRUN GRUPO EMPRESARIAL PRIVADO)','CGE150602JD5','ENRIQUE VAZQUEZ','52 1 33 1410 0615','Enrique Velazquez 3314100615','lcastillo@sushicentral.mx;finanzas@sushicentral.mx'],
            ['SUSHI DESTILADOS (MOVA COMERCIOS TURISTICOS)','MCT211217T16','ENRIQUE VAZQUEZ','52 1 33 1410 0615','Enrique Velazquez 3314100615','lcastillo@sushicentral.mx;finanzas@sushicentral.mx'],
            ['TACO QUE CIERRA NO ES FLAUTA','','','','Alberto Arevalo 8123524873',''],
            ['TAE SUNG MEXICO SA DE CV','TSM0210104V7','ING. SALVADOR SANCHEZ','81 1106 4258','Ing. Salvador Sanchez 8121149016','sistemas@taesungmexico.com.mx;luna.kim@taesungmexico.com.mx;contabilidad@taesungmexico.com.mx'],
            ['TALLER MECANICO VECTORI (EDUARDO ALANIS CASTILLO)','AACE7411016Q9','MERARI CAMARILLO','(81) 8479-4387','Alejandra Salinas 8120656081','grupo.vectori@outlook.com;facturaciongpovectori@gmail.com'],
            ['TIMELESS BRANDS','TBR2211027J8','LIC.LIZ CERVANTES','52 1 664 238 6369','Liz Cervantes 6642386369','lcervantes@capitaworks.com'],
            ['TODO INSUMO DE EMPANADAS (TODO EMPANADAS)','TIE2204058E0','DAVID GZZ / AURELIA','David 8122880905','DAVID LOPEZ 8122880905','cmartinez@todoempanadas.com.mx;dcepeda@todoempanadas.com.mx'],
            ['TORTAS EL REY (LUGAN RESTAURANTES)','LRE180626T48','LIC. FATIMA ROBLEDO','+52 1 444 334 7671','','ingresos@torteriaelrey.com'],
            ['TRANSPORTES GYM INTERNATIONAL','TGI2302173Y1','JORGE BALDERAS ROJAS','+52 1 442 457 2543','','contabilidad@tgi.com.mx'],
            ['TRANSPORTES Y MAQUINARIA MAS (GARCIA)','TMM940819LV7','ALVARO PEREZ / CP ALFREDO GOMEZ','2089-0171','Alfredo Gomez 8120405748','contabilidad@gpomas.mx;agomez@gpomas.mx'],
            ['TRANSPORTES TMGR (DAVID MILAN RIVERA)','MIRD8007091N5','ING JAIME','81 2945 5825','Ing. Jaime 8136392212','jaime.sistemas@tmgr.mx'],
            ['VITALI ALIMENTOS Y SERVICIOS SA DE CV','VAS1702177U5','MONICA ECHEVERRIA','(866) 633 2828','Luz 8661232189','icaro.conta@gmail.com;contabilidad@grupovitali.com.mx'],
            ['WANSOFT','WAN061023A90','MELISSA LIMON','4445 3800','Ricardo Uriel 5544694263','mlimon@wansoft.net;aramirez@wansoft.net'],
            ['WORK CESLA INDUSTRIAL','WCS161128BN6','OLGA PENA','+52 1 81 1910 6101','Anely Rodriguez 8119106101','ejecutivo1@workcs.mx'],
            ['ZUNAGAR SA DE CV','ZUN120615GZ4','MAGDALENA GONZALEZ','83-97-53-70','Magdalena Gonzalez 8110113379','ventaszunagar@yahoo.com.mx'],
            ['DENDRIPLEX','DEN1809277N1','','','C.P Elizabeth Rodriguez 8180271080',''],
        ];

        foreach ($clientes as $r) {
            $this->ins($r);
        }
    }

    public function down()
    {
        echo "m260512_182357_seed_clientes no puede revertirse.\n";
        return false;
    }
}
