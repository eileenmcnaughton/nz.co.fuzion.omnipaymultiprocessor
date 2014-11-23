<?php
namespace Omnipay\Skrill\Message;

/**
 * Skrill Payment Method
 *
 * Codes required for applicable payment methods when using the Split Gateway.
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 6.5 Skrill Payment Gateway Integration Guide
 */
abstract class PaymentMethod
{
    /**
     * Skrill Direct
     */
    const SKRILL_DIRECT               = 'MBD';

    /**
     * Skrill Digital Wallet
     */
    const SKRILL_DIGITAL_WALLET       = 'WLT';


    // Credit/Debit Cards

    /**
     * All Card Types
     * Countries: All
     */
    const ALL_CARD_TYPES              = 'ACC';

    /**
     * Visa
     * Countries: All
     */
    const VISA                        = 'VSA';

    /**
     * MasterCard
     * Countries: All
     */
    const MASTERCARD                  = 'MSC';

    /**
     * Visa Delta/Debit
     * Countries: United Kingdom
     */
    const VISA_DELTA_DEBIT            = 'VSD';

    /**
     * Visa Electron
     * Countries: All
     */
    const VISA_ELECTRON               = 'VSE';

    /**
     * Maestro
     * Countries: United Kingdom, Spain, Austria
     */
    const MAESTRO                     = 'MAE';

    /**
     * American Express
     * Countries: All
     */
    const AMERICAN_EXPRESS            = 'AMX';

    /**
     * Diners
     * Countries: All
     */
    const DINERS                      = 'DIN';

    /**
     * JCB
     * Countries: All
     */
    const JCB                         = 'JCB';

    /**
     * Laser
     * Countries: Rep. of Ireland
     */
    const LASER                       = 'LSR';

    /**
     * Carte Bleue
     * Countries: France
     */
    const CARTE_BLEUE                 = 'GCB';

    /**
     * Dankort
     * Countries: Denmark
     */
    const DANKORT                     = 'DNK';

    /**
     * PostePay
     * Countries: Italy
     */
    const POSTEPAY                    = 'PSP';

    /**
     * CartaSi
     * Countries: Italy
     */
    const CARTASI                     = 'CSI';


    // Instant Banking Options

    /**
     * Skrill Direct (Online Bank Transfer)
     * Countries: Germany, United Kingdom, France, Italy, Spain, Hungary, Austria
     */
    const ONLINE_BANK_TRANSFER        = 'OBT';

    /**
     * Giropay
     * Countries: Germany
     */
    const GIROPAY                     = 'GIR';

    /**
     * Direct Debit / ELV
     * Countries: Germany
     */
    const DIRECT_DEBIT_ELV            = 'DID';

    /**
     * Sofortüberweisung
     * Countries: Germany, Austria, Belgium, Netherlands, Switzerland, United Kingdom
     */
    const SOFORTUEBERWEISUNG          = 'SFT';

    /**
     * eNETS
     * Countries: Singapore
     */
    const ENETS                       = 'ENT';

    /**
     * Nordea Solo
     * Countries: Sweden
     */
    const NORDEA_SOLO_SWE             = 'EBT';

    /**
     * Nordea Solo
     * Countries: Finland
     */
    const NORDEA_SOLO_FIN             = 'SO2';

    /**
     * iDEAL
     * Countries: Netherlands
     */
    const IDEAL                       = 'IDL';

    /**
     * EPS (Netpay)
     * Countries: Austria
     */
    const EPS_NETPAY                  = 'NPY';

    /**
     * POLi
     * Countries: Australia
     */
    const POLI                        = 'PLI';

    /**
     * All Polish Banks
     * Countries: Poland
     */
    const ALL_POLISH_BANKS            = 'PWY';

    /**
     * ING Bank Śląski
     * Countries: Poland
     */
    const ING_BANK_SLASKI             = 'PWY5';

    /**
     * PKO BP (PKO Inteligo)
     * Countries: Poland
     */
    const PKO_BP_PKO_INTELIGO         = 'PWY6';

    /**
     * Multibank (Multitransfer)
     * Countries: Poland
     */
    const MULTIBANK_MULTITRANSFER     = 'PWY7';

    /**
     * Lukas Bank
     * Countries: Poland
     */
    const LUKAS_BANK                  = 'PWY14';

    /**
     * Bank BPH
     * Countries: Poland
     */
    const BANK_BPH                    = 'PWY15';

    /**
     * InvestBank
     * Countries: Poland
     */
    const INVEST_BANK                 = 'PWY17';

    /**
     * PeKaO S.A.
     * Countries: Poland
     */
    const PEKAO_SA                    = 'PWY18';

    /**
     * Citibank handlowy
     * Countries: Poland
     */
    const CITIBANK_HANDLOWY           = 'PWY19';

    /**
     * Bank Zachodni WBK (Przelew24)
     * Countries: Poland
     */
    const BANK_ZACHODNI_WBK_PRZELEW24 = 'PWY20';

    /**
     * BGŻ
     * Countries: Poland
     */
    const BGZ                         = 'PWY21';

    /**
     * Millenium
     * Countries: Poland
     */
    const MILLENIUM                   = 'PWY22';

    /**
     * mBank (mTransfer)
     * Countries: Poland
     */
    const MBANK_MTRANSFER             = 'PWY25';

    /**
     * Płacę z Inteligo
     * Countries: Poland
     */
    const PLACE_Z_INTELIGO            = 'PWY26';

    /**
     * Bank Ochrony Środowiska
     * Countries: Poland
     */
    const BANK_OCHRONY_SRODOWISKA     = 'PWY28';

    /**
     * Nordea
     * Countries: Poland
     */
    const NORDEA                      = 'PWY32';

    /**
     * Fortis Bank
     * Countries: Poland
     */
    const FORTIS_BANK                 = 'PWY33';

    /**
     * Deutsche Bank PBC S.A.
     * Countries: Poland
     */
    const DEUTSCHE_BANK_PBC_SA        = 'PWY36';

    /**
     * ePay.bg
     * Countries: Bulgaria
     */
    const EPAY_BG                     = 'EPY';
}
