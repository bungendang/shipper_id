<?php
/**
 * Shipper API PHP Library
 *
 * Copyright (C) 2019  Endang Kurniawan (bungendang)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author         Endang Kurniawn
 * @copyright      Copyright (c) 2019, Endang Kurniawn
 * @since          Version 1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

namespace Bungendang;

// ------------------------------------------------------------------------

use O2System\Curl;
use O2System\Kernel\Http\Message\Uri;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Rajaongkir
 * @package Steevenz
 */
class Shipper
{
    // use ErrorCollectorTrait;

    /**
     * Constant Account Type
     *
     * @access  public
     * @type    string
     */
    const ACCOUNT_STARTER = 'starter';
    const ACCOUNT_BASIC = 'basic';
    const ACCOUNT_PRO = 'pro';

    /**
     * Rajaongkir::$accountType
     *
     * Rajaongkir Account Type.
     *
     * @access  protected
     * @type    string
     */
    protected $accountType = 'starter';
    protected $path = 'countries';

    /**
     * Rajaongkir::$apiKey
     *
     * Rajaongkir API key.
     *
     * @access  protected
     * @type    string
     */
    protected $apiKey = null;

    /**
     * List of Supported Account Types
     *
     * @access  protected
     * @type    array
     */
    protected $supportedAccountTypes = [
        'starter',
        'basic',
        'pro',
    ];

    /**
     * Supported Couriers
     *
     * @access  protected
     * @type    array
     */
    protected $supportedCouriers = [
        'starter' => [
            'jne',
            'pos',
            'tiki',
        ],
        'basic'   => [
            'jne',
            'pos',
            'tiki',
            'pcp',
            'esl',
            'rpx',
        ],
        'pro'     => [
            'jne',
            'pos',
            'tiki',
            'rpx',
            'esl',
            'pcp',
            'pandu',
            'wahana',
            'sicepat',
            'j&t',
            'pahala',
            'cahaya',
            'sap',
            'jet',
            'indah',
            'slis',
            'expedito*',
            'dse',
            'first',
            'ncs',
            'star',
        ],
    ];

    /**
     * Rajaongkir::$supportedWaybills
     *
     * Rajaongkir supported couriers waybills.
     *
     * @access  protected
     * @type    array
     */
    protected $supportedWayBills = [
        'starter' => [],
        'basic'   => [
            'jne',
        ],
        'pro'     => [
            'jne',
            'pos',
            'tiki',
            'pcp',
            'rpx',
            'wahana',
            'sicepat',
            'j&t',
            'sap',
            'jet',
            'dse',
            'first',
        ],
    ];

    /**
     * Rajaongkir::$couriersList
     *
     * Rajaongkir courier list.
     *
     * @access  protected
     * @type array
     */
    protected $couriersList = [
        'jne'       => 'Jalur Nugraha Ekakurir (JNE)',
        'pos'       => 'POS Indonesia (POS)',
        'tiki'      => 'Citra Van Titipan Kilat (TIKI)',
        'pcp'       => 'Priority Cargo and Package (PCP)',
        'esl'       => 'Eka Sari Lorena (ESL)',
        'rpx'       => 'RPX Holding (RPX)',
        'pandu'     => 'Pandu Logistics (PANDU)',
        'wahana'    => 'Wahana Prestasi Logistik (WAHANA)',
        'sicepat'   => 'SiCepat Express (SICEPAT)',
        'j&t'       => 'J&T Express (J&T)',
        'pahala'    => 'Pahala Kencana Express (PAHALA)',
        'cahaya'    => 'Cahaya Logistik (CAHAYA)',
        'sap'       => 'SAP Express (SAP)',
        'jet'       => 'JET Express (JET)',
        'indah'     => 'Indah Logistic (INDAH)',
        'slis'      => 'Solusi Express (SLIS)',
        'expedito*' => 'Expedito*',
        'dse'       => '21 Express (DSE)',
        'first'     => 'First Logistics (FIRST)',
        'ncs'       => 'Nusantara Card Semesta (NCS)',
        'star'      => 'Star Cargo (STAR)',
    ];

    /**
     * Rajaongkir::$response
     *
     * Rajaongkir response.
     *
     * @access  protected
     * @type    mixed
     */
    protected $response;

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::__construct
     *
     * @access  public
     * @throws  \InvalidArgumentException
     */
    public function __construct($apiKey = null, $accountType = null)
    {
        if (isset($apiKey)) {
            if (is_array($apiKey)) {
                if (isset($apiKey[ 'api_key' ])) {
                    $this->apiKey = $apiKey[ 'api_key' ];
                }

                if (isset($apiKey[ 'account_type' ])) {
                    $accountType = $apiKey[ 'account_type' ];
                }
            } elseif (is_string($apiKey)) {
                $this->apiKey = $apiKey;
            }
        }

        if (isset($accountType)) {
            $this->setAccountType($accountType);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::setApiKey
     *
     * Set Rajaongkir API Key.
     *
     * @param   string $apiKey Rajaongkir API Key
     *
     * @access  public
     * @return  static
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::setAccountType
     *
     * Set Rajaongkir account type.
     *
     * @param   string $accountType RajaOngkir Account Type, can be starter, basic or pro
     *
     * @access  public
     * @return  static
     * @throws  \InvalidArgumentException
     */
    public function setAccountType($accountType)
    {
        $accountType = strtolower($accountType);

        if (in_array($accountType, $this->supportedAccountTypes)) {
            $this->accountType = $accountType;
        } else {
            throw new \InvalidArgumentException('Rajaongkir: Invalid Account Type');
        }

        return $this;
    }




    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::request
     *
     * Curl request API caller.
     *
     * @param string $path
     * @param array  $params
     * @param string $type
     *
     * @access  protected
     * @return  array|bool Returns FALSE if failed.
     */
    protected function request($path, $params = [], $type = 'GET')
    {
        var_dump($path);
        $this->path = $path;
        $apiUrl = 'https://sandbox-api.shipper.id/';

        switch ($this->accountType) {
            default:
            case 'starter':
                $path = 'public/v1/' . $path;
                break;

            case 'basic':
                $path = 'basic/' . $path;
                break;

            case 'pro':
                $apiUrl = 'https://sandbox-api.shipper.id/';
                $path = 'public/v1/' . $path;
                break;
        }

        $uri = $apiUrl.$path;
        $params['apiKey'] = $this->apiKey;
        $param_string = http_build_query($params);
        // var_dump($param_string);
        if ($params) {
            # code...
            $uri = $uri."?".$param_string;
        } else {
            # code...
        }
        

        // $uri = $uri ."&apiKey=".$this->apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'User-Agent: Shipper/'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_exec ($ch);
        if( ! $result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        } 
        curl_close ($ch);

        $result = json_decode($result, true);
        echo $path;
        switch ($this->path) {
            case "countries":
                return $result["data"]["rows"];
                break;

            case "domesticRates":
                return $result["data"]["rates"];
                break;
            default:
                return $result["data"]["rows"];
        }
        // return $result["data"]["rows"];
    }

    public function getCountries(){
        // echo "list";
        return $this->request('countries');
        // return "list countries";
    }

    public function getMerchant(){
        // echo "list";
        return $this->request('merchants');
        // return "list countries";
    }

    public function getProvinces(){
        // echo "list";
        return $this->request('provinces');
        // return "list countries";
    }

    public function getCities($province_id){
        // echo "list";
        return $this->request('cities',['province'=>$province_id]);
        // return "list countries";
    }

    public function getSuburbs($city_id){
        // echo "list";
        return $this->request('suburbs',['city'=>$city_id]);
        // return "list countries";
    }

    public function getAreas($suburbs_id){
        // echo "list";
        var_dump($suburbs_id);
        return $this->request('areas',['suburb'=>$suburbs_id]);
        // return "list countries";
    }

    public function getCourier($data){
        // echo "list";
        var_dump($data);
        return $this->request('domesticRates',$data);
        // return "list countries";
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCost
     *
     * Get cost calculation.
     *
     * @example
     * $rajaongkir->getCost(
     *      ['city' => 1],
     *      ['subdistrict' => 12],
     *      ['weight' => 100, 'length' => 100, 'width' => 100, 'height' => 100, 'diameter' => 100],
     *      'jne'
     * );
     *
     * @see      http://rajaongkir.com/dokumentasi/pro
     *
     * @param array  $origin            City, District or Subdistrict Origin
     * @param array  $destination       City, District or Subdistrict Destination
     * @param array  $metrics           Array of Specification
     *                                  weight      int     weight in gram (required)
     *                                  length      number  package length dimension
     *                                  width       number  package width dimension
     *                                  height      number  package height dimension
     *                                  diameter    number  package diameter
     * @param string $courier           Courier Code
     *
     * @access   public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCost(array $origin, array $destination, $metrics, $courier)
    {
        $params[ 'courier' ] = strtolower($courier);

        $params[ 'originType' ] = strtolower(key($origin));
        $params[ 'destinationType' ] = strtolower(key($destination));

        if ($params[ 'originType' ] !== 'city') {
            $params[ 'originType' ] = 'subdistrict';
        }

        if ( ! in_array($params[ 'destinationType' ], ['city', 'country'])) {
            $params[ 'destinationType' ] = 'subdistrict';
        }

        if (is_array($metrics)) {
            if ( ! isset($metrics[ 'weight' ]) AND
                isset($metrics[ 'length' ]) AND
                isset($metrics[ 'width' ]) AND
                isset($metrics[ 'height' ])
            ) {
                $metrics[ 'weight' ] = (($metrics[ 'length' ] * $metrics[ 'width' ] * $metrics[ 'height' ]) / 6000) * 1000;
            } elseif (isset($metrics[ 'weight' ]) AND
                isset($metrics[ 'length' ]) AND
                isset($metrics[ 'width' ]) AND
                isset($metrics[ 'height' ])
            ) {
                $weight = (($metrics[ 'length' ] * $metrics[ 'width' ] * $metrics[ 'height' ]) / 6000) * 1000;

                if ($weight > $metrics[ 'weight' ]) {
                    $metrics[ 'weight' ] = $weight;
                }
            }

            foreach ($metrics as $key => $value) {
                $params[ $key ] = $value;
            }
        } elseif (is_numeric($metrics)) {
            $params[ 'weight' ] = $metrics;
        }

        switch ($this->accountType) {
            case 'starter':

                if ($params[ 'destinationType' ] === 'country') {
                    $this->errors[ 301 ] = 'Unsupported International Destination. Tipe akun starter tidak mendukung pengecekan destinasi international.';

                    return false;
                } elseif ($params[ 'originType' ] === 'subdistrict' OR $params[ 'destinationType' ] === 'subdistrict') {
                    $this->errors[ 302 ] = 'Unsupported Subdistrict Origin-Destination. Tipe akun starter tidak mendukung pengecekan ongkos kirim sampai kecamatan.';

                    return false;
                }

                if ( ! isset($params[ 'weight' ]) AND
                    isset($params[ 'length' ]) AND
                    isset($params[ 'width' ]) AND
                    isset($params[ 'height' ])
                ) {
                    $this->errors[ 304 ] = 'Unsupported Dimension. Tipe akun starter tidak mendukung pengecekan biaya kirim berdasarkan dimensi.';

                    return false;
                } elseif (isset($params[ 'weight' ]) AND $params[ 'weight' ] > 30000) {
                    $this->errors[ 305 ] = 'Unsupported Weight. Tipe akun starter tidak mendukung pengecekan biaya kirim dengan berat lebih dari 30000 gram (30kg).';

                    return false;
                }

                if ( ! in_array($params[ 'courier' ], $this->supportedCouriers[ $this->accountType ])) {
                    $this->errors[ 303 ] = 'Unsupported Courier. Tipe akun starter tidak mendukung pengecekan biaya kirim dengan kurir ' . $this->couriersList[ $courier ] . '.';

                    return false;
                }

                break;

            case 'basic':

                if ($params[ 'originType' ] === 'subdistrict' OR $params[ 'destinationType' ] === 'subdistrict') {
                    $this->errors[ 302 ] = 'Unsupported Subdistrict Origin-Destination. Tipe akun basic tidak mendukung pengecekan ongkos kirim sampai kecamatan.';

                    return false;
                }

                if ( ! isset($params[ 'weight' ]) AND
                    isset($params[ 'length' ]) AND
                    isset($params[ 'width' ]) AND
                    isset($params[ 'height' ])
                ) {
                    $this->errors[ 304 ] = 'Unsupported Dimension. Tipe akun basic tidak mendukung pengecekan biaya kirim berdasarkan dimensi.';

                    return false;
                } elseif (isset($params[ 'weight' ]) AND $params[ 'weight' ] > 30000) {
                    $this->errors[ 305 ] = 'Unsupported Weight. Tipe akun basic tidak mendukung pengecekan biaya kirim dengan berat lebih dari 30000 gram (30kg).';

                    return false;
                } elseif (isset($params[ 'weight' ]) AND $params[ 'weight' ] < 30000) {
                    unset($params[ 'length' ], $params[ 'width' ], $params[ 'height' ]);
                }

                if ( ! in_array($params[ 'courier' ], $this->supportedCouriers[ $this->accountType ])) {
                    $this->errors[ 303 ] = 'Unsupported Courier. Tipe akun basic tidak mendukung pengecekan biaya kirim dengan kurir ' . $this->couriersList[ $courier ] . '.';

                    return false;
                }

                break;
        }

        $params[ 'origin' ] = $origin[ key($origin) ];
        $params[ 'destination' ] = $destination[ key($destination) ];

        $path = key($destination) === 'country' ? 'internationalCost' : 'cost';

        return $this->request($path, $params, 'POST');
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getWaybill
     *
     * Get detail of waybill.
     *
     * @param   int         $idWaybill Receipt ID
     * @param   null|string $courier   Courier Code
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getWaybill($idWaybill, $courier)
    {
        $courier = strtolower($courier);

        if (in_array($courier, $this->supportedWayBills[ $this->accountType ])) {
            return $this->request('waybill', [
                'key'     => $this->apiKey,
                'waybill' => $idWaybill,
                'courier' => $courier,
            ], 'POST');
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCurrency
     *
     * Get Rajaongkir currency.
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCurrency()
    {
        if ($this->accountType !== 'starter') {
            return $this->request('currency');
        }

        $this->errors[ 301 ] = 'Unsupported Get Currency. Tipe akun starter tidak mendukung pengecekan currency.';

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getSupportedCouriers
     *
     * Gets list of supported couriers by your account.
     *
     * @return array|bool Returns FALSE if failed.
     */
    public function getSupportedCouriers()
    {
        if(isset($this->supportedCouriers[$this->accountType])) {
            return $this->supportedCouriers[$this->accountType];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getSupportedWayBills
     *
     * Gets list of supported way bills based on account type.
     *
     * @return array|bool Returns FALSE if failed.
     */
    public function getSupportedWayBills()
    {
        if(isset($this->supportedWayBills[$this->accountType])) {
            return $this->supportedWayBills[$this->accountType];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getResponse
     *
     * Get original response object.
     *
     * @param   string $offset Response Offset Object
     *
     * @access  public
     * @return  \O2System\Curl\Response|bool Returns FALSE if failed.
     */
    public function getResponse()
    {
        return $this->response;
    }
}
