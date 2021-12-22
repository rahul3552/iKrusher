#!/bin/sh
#
# Magento Enterprise Edition
#
# NOTICE OF LICENSE
#
# This source file is subject to the Magento Enterprise Edition End User License Agreement
# that is bundled with this package in the file LICENSE_EE.txt.
# It is also available through the world-wide-web at this URL:
# http://www.magento.com/license/enterprise-edition
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@magento.com so we can send you a copy immediately.
#
# DISCLAIMER
#
# Do not edit or add to this file if you wish to upgrade Magento to newer
# versions in the future. If you wish to customize Magento for your
# needs please refer to http://www.magento.com for more information.
#
# @category    Mage
# @package     Mage
# @copyright Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
# @license http://www.magento.com/license/enterprise-edition
#
# location of the php binary
if [ ! "$1" = "" ] ; then
    CRONSCRIPT=$1
else
    CRONSCRIPT=i95devPullDataCron.php
fi

MODE=""
if [ ! "$2" = "" ] ; then
	MODE=" $2"
fi

PHP_BIN=`which php`
# absolute path to magento installation
CHKSLASH=`echo $0 | tail -c 2`
INSTALLBASEDIR=`echo $0`

if [[ $CHKSLASH == *"/"* ]]; then
  INSTALLBASEDIR = `echo ${INSTALLBASEDIR::-1}`
fi

INSTALLDIR=`echo $INSTALLBASEDIR | sed 's/i95devPullDataCron\.sh//g'`

#	prepend the intallation path if not given an absolute path
if [ "$INSTALLDIR" != "" -a "`expr index $CRONSCRIPT /`" != "1" ];then
    $PHP_BIN $INSTALLDIR$CRONSCRIPT$MODE
fi
