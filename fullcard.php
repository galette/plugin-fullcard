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

if ( !$login->isLogged() or $login->isAdmin()
    && (!isset($_GET[Adherent::PK]) || trim($_GET[Adherent::PK]) == '')
) {
    //If not logged, or if admin without a member id ; print en empty card
    $adh = null;
} else if ( $login->isAdmin() && isset($_GET[Adherent::PK]) ) {
    //If admin with a member id
    $adh = new Adherent((int)$_GET[Adherent::PK]);
} else if ( $login->isLogged() ) {
    //If user logged in
    $adh = new Adherent((int)$login->id);
}

define('FULLCARD_FONT', PDF::FONT_SIZE-2);

$pdf=new PDF('P', 'mm', 'A4');
$pdf->setMargins(10, 10);

$pdf->SetAutoPageBreak(false, 20);
$pdf->AliasNbPages();
$pdf->Open();

$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT);
$pdf->SetTextColor(0, 0, 0);

$pdf->AddPage();
$picture = new picture(0);
$pdf->PageHeader();

$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.1);

$pdf->Ln(10);
$pdf->Line($pdf->GetX(), $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(3);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT - 1);
$pdf->MultiCell(0, 4, _T("Complete the following form and send it with your funds, in order to complete your subscription."), 0, 'J');

$pdf->ln(2);
$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT);
/** TODO: set Adress to one of the staff members from preferences */
$pdf->MultiCell(0, 4, "Thomas Canniot\nTrésorier de l'association Fedora-fr\n25 avenue Jean-Jaurès\n08000 CHARLEVILLE-MÉZIÈRES - FRANCE", 0, 'C');
$pdf->Ln(3);
$pdf->Line($pdf->GetX(), $pdf->GetY(), 200, $pdf->GetY());

$pdf->Ln(10);
$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT + 2);

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
$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT);
$pdf->Write(4, _T("The minimum contribution for each type of membership are defined on the website of the association. The amount of donations are free to be dcided by the generous donor."));
$pdf->Ln(20);

$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT + 2);
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
$pdf->Cell(30, 7, _T("First name"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->surname, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, _T("Address"), 0, (($adh === null)?1:0), 'L');
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
$pdf->Cell(30, 7, _T("Zip Code"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->zipcode, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, $pdf->GetX()+30+15, $pdf->GetY()-1);
$pdf->SetY($y);
$pdf->SetX($pdf->GetX()+30+15+5);
$pdf->Cell(30, 7, _T("City"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->town, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30+15+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, _T("Country"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->country, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, _T("Email adress"), 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->email, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Cell(30, 7, _T("Username") ." **", 0, (($adh === null)?1:0), 'L');
if ( $adh !== null ) {
    $pdf->Cell(0, 7, $adh->login, 0, 1, 'L');
}
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Ln(6);
$pdf->Cell(30, 7, _T("Amount"), 0, 1, 'L');
$pdf->Line($pdf->GetX()+30, $pdf->GetY()-1, 190, $pdf->GetY()-1);

$pdf->Ln(10);
$pdf->Write(
    4,
    preg_replace(
        '/%s/',
        $preferences->pref_nom,
        _T("Hereby, I agree to comply to %s association's statutes and its rules.")
    )
);
$pdf->Ln(10);
$pdf->Cell(64, 5, _T("At "), 0, 0, 'L');
$pdf->Cell(0, 5, _T("On            /            /            "), 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(0, 5, _T("Signature"), 0, 1, 'L');


$pdf->SetY(260);
$pdf->SetFont(PDF::FONT, '', FULLCARD_FONT - 2);
$pdf->Cell(0, 3, _T("* Company names for compagnies"), 0, 1, 'R');
$pdf->Cell(0, 3, _T("** Galette identifier, if applicable"), 0, 1, 'R');

$pdf->Output(_T("fullcard") . '.pdf', 'D');

?>
