<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Fullcard member as PDF
 *
 * PHP version 5
 *
 * Copyright © 2011 The Galette Team
 *
 * This file is part of Galette (http://galette.tuxfamily.org).
 *
 * Galette is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Galette is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Galette. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Plugins
 * @package   GaletteFullcard
 *
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2011 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   SVN: $Id$
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.7dev - 2011-05-30
 */

$base_path = '../../';
require_once $base_path . 'includes/galette.inc.php';
require_once $base_path . 'classes/pdf.class.php';
require_once $base_path . 'classes/adherent.class.php';

//If not logged, or if admin without a member id ; print en empty card
if ( !$login->isLogged() or $login->isAdmin() && (!isset($_GET[Adherent::PK]) || trim($_GET[Adherent::PK]) == '') ) {
    $adh = null;
//If admin with a member id
} else if ( $login->isAdmin() && isset($_GET[Adherent::PK]) ) {
    $adh = new Adherent((int)$_GET[Adherent::PK]);
//If user logged in
} else if ( $login->isLogged() ) {
    $adh = new Adherent((int)$login->id);
}

$pdf=new PDF('P', 'mm', 'A4');
$pdf->setMargins(10, 10);

$pdf->SetAutoPageBreak(false, 20);
$pdf->AliasNbPages();
$pdf->Open();
/*$pdf->AddFont('gill', '', 'gill.php');
$pdf->AddFont('gillb', '', 'gillb.php');
$pdf->SetFont('gill', '', 11);*/
/** TODO: vary fonts sizes */
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE);
$pdf->SetTextColor(0, 0, 0);

//$pdf->SetTitle(utf8_decode($heads["titre"]));
$pdf->AddPage();
$picture = new picture(0);
//$pdf->Logo($picture->FILE_PATH);
$pdf->PageHeader();

$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.1);

$pdf->Ln(10);
$pdf->Line($pdf->GetX(), $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(3);
$pdf->SetTextColor(0, 0, 0);
/** TODO: vary fonts sizes */
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE - 1);
$pdf->MultiCell(0, 4, _T("Complete the following form and send it with your funds, in order to complete your subscription."), 0, 'J');

$pdf->ln(2);
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE);
/** TODO: vary fonts sizes */
/** TODO: set Adress to one of the staff members from preferences */
$pdf->MultiCell(0, 4, "Thomas Canniot\nTrésorier de l'association Fedora-fr\n25 avenue Jean-Jaurès\n08000 CHARLEVILLE-MÉZIÈRES - FRANCE", 0, 'C');
$pdf->Ln(3);
$pdf->Line($pdf->GetX(), $pdf->GetY(), 200, $pdf->GetY());

$pdf->Ln(10);
/** TODO: vary fonts sizes */
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE + 2);

//let's draw all fields
$y = $pdf->GetY()+1;
$pdf->Write(5, _T("Required membership:"));
$pdf->SetX($pdf->GetX()+5);
$pdf->Rect($pdf->GetX(), $y, 3, 3);
$pdf->SetX($pdf->GetX()+(($adh === null)?3:0));
$pdf->Cell(3, 5, ($adh !== null && $adh->status == 4) ? "X" : "", 0, 0, 'C');

$pdf->Write(5, _T("Active member"));
$pdf->SetX($pdf->GetX()+5);
$pdf->Rect($pdf->GetX(), $y, 3, 3);
$pdf->SetX($pdf->GetX()+(($adh === null)?3:0));
$pdf->Cell(3, 5, ($adh !== null && $adh->status == 5) ? "X" : "", 0, 0, 'C');
$pdf->Write(5, _T("Benefactor member"));
$pdf->SetX($pdf->GetX()+5);
$pdf->Rect($pdf->GetX(), $y, 3, 3);
$pdf->SetX($pdf->GetX()+3);
$pdf->Write(5, _T("Donation"));
$pdf->Ln();
/** TODO: vary fonts sizes */
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE);
$pdf->Write(4, "Les cotisations minimales pour chacun des types d'adhésions sont fixées ");
$x_orig = $pdf->GetX()+1;
$pdf->Write(4, "sur le site web de l'association.", "http://asso.fedora-fr.org/Comment_devenir_membre_%3F#Cotisation");
$x_end = $pdf->GetX()+1;
$pdf->Line($x_orig, $pdf->GetY()+4, $x_end, $pdf->GetY()+4);
$pdf->Write(4, " Le montant des dons est quant à lui laissé à l'entière appréciation des généreux donnateurs, avec un minimum correspondant à la cotisation annuelle de base.");
$pdf->Ln(20);

/** TODO: vary fonts sizes */
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE + 2);
$y = $pdf->GetY()+1;
$pdf->Cell(30, 5, _T("Politeness"), 0, 0, 'L');
$pdf->Rect($pdf->GetX(), $y, 3, 3);
$pdf->SetX($pdf->GetX() + (($adh === null)? 3 : 0));
$pdf->Cell(3, 5, ($adh !== null && $adh->politeness == Politeness::MISS) ? 'X' : '', 0, 0, 'C');
$pdf->Cell(0, 5, _T("Mrs"), 0, 1, 'L');
$y = $pdf->GetY()+1;
$pdf->Rect($pdf->GetX()+30, $y, 3, 3);
$pdf->SetX($pdf->GetX()+(($adh === null)?33:30));
$pdf->Cell(3, 5, ($adh !== null && $adh->politeness == Politeness::MRS) ? 'X' : '', 0, 0, 'C');
$pdf->Cell(0, 5, _T("Miss"), 0, 1, 'L');
$y = $pdf->GetY()+1;
$pdf->Rect($pdf->GetX()+30, $y, 3, 3);
$pdf->SetX($pdf->GetX()+(($adh === null)?33:30));
$pdf->Cell(3, 5, ($adh !== null && $adh->politeness == Politeness::MR) ? 'X' : '', 0, 0, 'C');
$pdf->Cell(0, 5, _T("Mister"), 0, 1, 'L');
$y = $pdf->GetY()+1;
$pdf->Rect($pdf->GetX()+30, $y, 3, 3);
$pdf->SetX($pdf->GetX()+(($adh === null)?33:30));
$pdf->Cell(3, 5, ($adh !== null && $adh->politeness == Politeness::COMPANY)?"X":"", 0, 0, 'C');
$pdf->Cell(0, 5, _T("Society"), 0, 1, 'L');

$pdf->Cell(30, 7, _T("Name") . " *", 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->name, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);
$pdf->Cell(30, 7, rtrim(_T("First name:"), "&nbsp;:"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->surname, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, rtrim(_T("Address:"), "&nbsp;:"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->adress, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);
$pdf->SetY($pdf->GetY() + 7);
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->adress_continuation, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);
$pdf->SetY($pdf->GetY() + 7);
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$y = $pdf->GetY();
$pdf->Cell(30, 7, rtrim(_T("Zip Code:"), "&nbsp;:"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->zipcode, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, $pdf->GetX()+30+15, $pdf->GetY()-1);
$pdf->SetY($y);
$pdf->SetX($pdf->GetX()+30+15+5);
$pdf->Cell(30, 7, rtrim(_T("City:"), "&nbsp;:"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->town, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30+15+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, rtrim(_T("Country:"), "&nbsp;:"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->country, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, _T("Email adress"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->email, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, rtrim(_T("Username:"), "&nbsp;:")." **", 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->login, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Ln(6);
$pdf->Cell(30, 7, rtrim(_T("Amount:"), "&nbsp;:"), 0, 1, 'L');
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Ln(10);
$pdf->Write(4, "Par la présente, je m'engage à respecter les statuts ainsi que ");
$pdf->Write(4, "le règlement intérieur de l'association Fedora-fr.");
$pdf->Ln(10);
$pdf->Cell(64, 5, "Fait à ", 0, 0, 'L');
$pdf->Cell(0, 5, "Le            /            /            ", 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(0, 5, "Signature", 0, 1, 'L');


$pdf->SetY(260);
/** TODO: vary fonts sizes */
$pdf->SetFont(PDF::FONT, '', PDF::FONT_SIZE - 2);
$pdf->Cell(0, 3, "* Raison sociale pour les personnes morales", 0, 1, 'R');
$pdf->Cell(0, 3, "** Identifiant sur le site de l'association, si applicable", 0, 1, 'R');

$pdf->Output(_T("fullcard") . '.pdf', 'D');

?>
