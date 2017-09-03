#!/bin/bash
#//==============================================================================
#// RHUL Survey Project - SurveyMonkey API Synchronisation System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for Economics Department
#// Copyright (c) 2015 Jamie J. Alnasir, All Rights Reserved
#//==============================================================================
#// Version: Python edition
#//==============================================================================

#// This script serves to ensure there are no stale voucher QR codes accummulating
#// on the server - "stale" refers to voucher QR code .png files for which there
#// is no corresponding database entry, i.e. voucher QR code images that have been
#// generated but are not longer in use.
#//
#// How is this achieved? The syncronisation system generates a MANIFEST file containing
#// a list of all current, genuine vouchers, the QR code images files of which reside
#// in the VOUCHERS_FOLDER, the VOUCHER_PURGE file contains a list of vouchers no longer
#// in circulation and which should be deleted (or moved to a PURGE FOLDER) by this script.
#//

VOUCHERS_MANIFEST="/home/mxba001/RHUL_Survey_SAS/VOUCHERS_MANIFEST.txt";
VOUCHERS_PURGE="/home/mxba001/RHUL_Survey_SAS/VOUCHER_PURGE.txt";
VOUCHERS_FOLDER="/srv/www/survey/choose-vouchers";
VOUCHER_PURGE_FOLDER="/srv/www/survey/choose-vouchers/PURGED";


# old method, problem is rm fails on interation encountering null string
#cd $VOUCHERS_FOLDER;
#ls -1 $VOUCHERS_FOLDER | egrep -f $VOUCHERS_PURGE | xargs rm

# failsafe method
while read v;
	do
	echo "Withdraw/purge: $v";
	mv $VOUCHERS_FOLDER/$v $VOUCHER_PURGE_FOLDER
done < $VOUCHERS_PURGE

# Empty voucher purge file
echo "">$VOUCHERS_PURGE


