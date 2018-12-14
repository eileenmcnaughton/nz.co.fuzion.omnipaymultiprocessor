<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2018                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

use CRM_Omnipaymultiprocessor_ExtensionUtil as E;

/**
 * Class CRM_Core_Payment_PaypalRest.
 *
 * In general the OmnipayMultiProcessor class copes with vagaries of
 * payment processors. However sometime they have anomalies that
 * can't be dealt with by metadata
 *
 * Omnipay supports token payments but not recurring
 */
class CRM_Core_Payment_OmnipayPaypalRest extends CRM_Core_Payment_OmnipayMultiProcessor {

  /**
   * Function to action after pre-approval if supported
   *
   * @param array $params
   *   Parameters from the form
   *
   * Action to do after pre-approval. e.g. PaypalRest returns from offsite &
   * hits the billing plan url to confirm.
   *
   * @throws \CRM_Core_Exception
   */
  public function doPostApproval(&$params) {
    $planResponse = $this->gateway->completeCreateCard(array(
      'transactionReference' => $params['token'],
      'state' => 'ACTIVE',
    ))->send();
    if (!$planResponse->isSuccessful()) {
      throw new CRM_Core_Exception($planResponse->getMessage());
    }
    $params['token'] = $planResponse->getCardReference();
  }

}
