<?php
/**
 * Created by PhpStorm.
 * User: Coders Earth
 * Date: 4/12/2018
 * Time: 3:49 PM
 */

namespace Drupal\crowdfundingproject;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class HelperServices {


  public function getMapLocations() {

    $project_ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'crowdfunding_project')
      ->sort('nid', 'DESC')
      ->range(0, 5000)
      ->execute();

    $locationData = [];

    foreach ($project_ids as $project_id) {
      $project = Node::load($project_id);
      if ($project instanceof \Drupal\node\Entity\Node) {
        $locationData[] = [
          'projectTitle' => $project->getTitle(),
          'projectSummary' => $project->get('body')->summary,
          'projectUrl' => '/node/' . $project->id(),
          'latitude' => $project->get('field_geolocation')->lat,
          'longitude' => $project->get('field_geolocation')->lng,
          'address1' => $project->get('field_address')->address_line1,
          'address2' => $project->get('field_address')->address_line2,
          'postalCode' => $project->get('field_address')->postal_code,
          'city' => $project->get('field_address')->locality,
        ];
      }
    }

    return $locationData;
  }

  public function getDonationList($project_id, $status = 'paid', $limit = 10, $offset = 0) {

    $payment_ids = \Drupal::entityQuery('node')
      ->condition('field_status', $status)
      ->condition('field_project', $project_id)
      ->condition('type', 'payment')
      ->sort('nid', 'DESC')
      ->range($offset, $limit)
      ->execute();

    $paymentObjs = [];
    foreach ($payment_ids as $payment_id) {
      $paymentObjs[] = Node::load($payment_id);
    }

    return $paymentObjs;
  }

  public function getDaysToGo($date) {
    $days = 0;
    if (!empty($date)) {
      $cuttent_time = time();
      $end_time = strtotime($date);
      if ($cuttent_time < $end_time) {
        $diff = $end_time - $cuttent_time;
        $days = round($diff / (60 * 60 * 24));
      }
    }
    return $days;
  }

  public function getDaysAgo($ptime) {

    $time_val = 0;
    if ($ptime) {

      $estimate_time = time() - $ptime;

      if ($estimate_time < 1) {
        return 'less than 1 second ago';
      }

      $condition = [
        12 * 30 * 24 * 60 * 60 => 'år',
        30 * 24 * 60 * 60 => 'måned',
        24 * 60 * 60 => 'dag',
        60 * 60 => 'time',
        60 => 'minutt',
        1 => 'sekund',
      ];

      foreach ($condition as $secs => $str) {
        $d = $estimate_time / $secs;

        if ($d >= 1) {
          $r = round($d);
          return $r . ' ' . $str . ($r > 1 ? 'en' : '') . ' geleden';
        }
      }
    }
    return $time_val;
  }

  public function getTopDonor($project_id, $limit) {
    $payment_ids = \Drupal::entityQuery('node')
      ->condition('field_status', 'paid')
      ->condition('type', 'payment')
      ->sort('field_amount', 'DESC')
      ->range(0, $limit)
      ->execute();

    $users = [];
    if (!empty($payment_ids)) {
      foreach ($payment_ids as $payment_id) {
        $payment = Node::load($payment_id);
        if ($payment instanceof \Drupal\node\Entity\Node) {
          $user_id = $payment->get('field_user')->target_id;
          if ($user_id) {
            $user = User::load($user_id);
            if ($user instanceof \Drupal\user\Entity\User) {

              $image_id = $user->get('user_picture')->target_id;
              $image_url = \Drupal::service('crowdfundingproject.helper')
                ->getImageUrl($image_id);

              $users[] = [
                'user_id' => $user->id(),
                'name' => $user->get('field_firstname')->value . ' ' . $user->get('field_lastname')->value,
                'picture' => $image_url,
              ];
            }
          }
        }
      }
    }

    return $users;
  }

  public function getAllPackages() {
    $package_ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'project_packages')
      ->sort('nid', 'DESC')
      ->execute();
    $packages = [];
    if (!empty($package_ids)) {
      foreach ($package_ids as $package_id) {
        $packages[] = Node::load($package_id);
      }
    }
    return $packages;
  }

  public function getCountryList() {
    $countries = [
      'AF' => 'Afghanistan',
      'AX' => 'Åland Islands',
      'AL' => 'Albania',
      'DZ' => 'Algeria',
      'AS' => 'American Samoa',
      'AD' => 'Andorra',
      'AO' => 'Angola',
      'AI' => 'Anguilla',
      'AQ' => 'Antarctica',
      'AG' => 'Antigua and Barbuda',
      'AR' => 'Argentina',
      'AM' => 'Armenia',
      'AW' => 'Aruba',
      'AU' => 'Australia',
      'AT' => 'Austria',
      'AZ' => 'Azerbaijan',
      'BS' => 'Bahamas',
      'BH' => 'Bahrain',
      'BD' => 'Bangladesh',
      'BB' => 'Barbados',
      'BY' => 'Belarus',
      'BE' => 'Belgium',
      'BZ' => 'Belize',
      'BJ' => 'Benin',
      'BM' => 'Bermuda',
      'BT' => 'Bhutan',
      'BO' => 'Bolivia, Plurinational State of',
      'BQ' => 'Bonaire, Sint Eustatius and Saba',
      'BA' => 'Bosnia and Herzegovina',
      'BW' => 'Botswana',
      'BV' => 'Bouvet Island',
      'BR' => 'Brazil',
      'IO' => 'British Indian Ocean Territory',
      'BN' => 'Brunei Darussalam',
      'BG' => 'Bulgaria',
      'BF' => 'Burkina Faso',
      'BI' => 'Burundi',
      'KH' => 'Cambodia',
      'CM' => 'Cameroon',
      'CA' => 'Canada',
      'CV' => 'Cape Verde',
      'KY' => 'Cayman Islands',
      'CF' => 'Central African Republic',
      'TD' => 'Chad',
      'CL' => 'Chile',
      'CN' => 'China',
      'CX' => 'Christmas Island',
      'CC' => 'Cocos (Keeling) Islands',
      'CO' => 'Colombia',
      'KM' => 'Comoros',
      'CG' => 'Congo',
      'CD' => 'Congo, the Democratic Republic of the',
      'CK' => 'Cook Islands',
      'CR' => 'Costa Rica',
      'CI' => 'Côte d\'Ivoire',
      'HR' => 'Croatia',
      'CU' => 'Cuba',
      'CW' => 'Curaçao',
      'CY' => 'Cyprus',
      'CZ' => 'Czech Republic',
      'DK' => 'Denmark',
      'DJ' => 'Djibouti',
      'DM' => 'Dominica',
      'DO' => 'Dominican Republic',
      'EC' => 'Ecuador',
      'EG' => 'Egypt',
      'SV' => 'El Salvador',
      'GQ' => 'Equatorial Guinea',
      'ER' => 'Eritrea',
      'EE' => 'Estonia',
      'ET' => 'Ethiopia',
      'FK' => 'Falkland Islands (Malvinas)',
      'FO' => 'Faroe Islands',
      'FJ' => 'Fiji',
      'FI' => 'Finland',
      'FR' => 'France',
      'GF' => 'French Guiana',
      'PF' => 'French Polynesia',
      'TF' => 'French Southern Territories',
      'GA' => 'Gabon',
      'GM' => 'Gambia',
      'GE' => 'Georgia',
      'DE' => 'Germany',
      'GH' => 'Ghana',
      'GI' => 'Gibraltar',
      'GR' => 'Greece',
      'GL' => 'Greenland',
      'GD' => 'Grenada',
      'GP' => 'Guadeloupe',
      'GU' => 'Guam',
      'GT' => 'Guatemala',
      'GG' => 'Guernsey',
      'GN' => 'Guinea',
      'GW' => 'Guinea-Bissau',
      'GY' => 'Guyana',
      'HT' => 'Haiti',
      'HM' => 'Heard Island and McDonald Islands',
      'VA' => 'Holy See (Vatican City State)',
      'HN' => 'Honduras',
      'HK' => 'Hong Kong',
      'HU' => 'Hungary',
      'IS' => 'Iceland',
      'IN' => 'India',
      'ID' => 'Indonesia',
      'IR' => 'Iran, Islamic Republic of',
      'IQ' => 'Iraq',
      'IE' => 'Ireland',
      'IM' => 'Isle of Man',
      'IL' => 'Israel',
      'IT' => 'Italy',
      'JM' => 'Jamaica',
      'JP' => 'Japan',
      'JE' => 'Jersey',
      'JO' => 'Jordan',
      'KZ' => 'Kazakhstan',
      'KE' => 'Kenya',
      'KI' => 'Kiribati',
      'KP' => 'Korea, Democratic People\'s Republic of',
      'KR' => 'Korea, Republic of',
      'KW' => 'Kuwait',
      'KG' => 'Kyrgyzstan',
      'LA' => 'Lao People\'s Democratic Republic',
      'LV' => 'Latvia',
      'LB' => 'Lebanon',
      'LS' => 'Lesotho',
      'LR' => 'Liberia',
      'LY' => 'Libya',
      'LI' => 'Liechtenstein',
      'LT' => 'Lithuania',
      'LU' => 'Luxembourg',
      'MO' => 'Macao',
      'MK' => 'Macedonia, the former Yugoslav Republic of',
      'MG' => 'Madagascar',
      'MW' => 'Malawi',
      'MY' => 'Malaysia',
      'MV' => 'Maldives',
      'ML' => 'Mali',
      'MT' => 'Malta',
      'MH' => 'Marshall Islands',
      'MQ' => 'Martinique',
      'MR' => 'Mauritania',
      'MU' => 'Mauritius',
      'YT' => 'Mayotte',
      'MX' => 'Mexico',
      'FM' => 'Micronesia, Federated States of',
      'MD' => 'Moldova, Republic of',
      'MC' => 'Monaco',
      'MN' => 'Mongolia',
      'ME' => 'Montenegro',
      'MS' => 'Montserrat',
      'MA' => 'Morocco',
      'MZ' => 'Mozambique',
      'MM' => 'Myanmar',
      'NA' => 'Namibia',
      'NR' => 'Nauru',
      'NP' => 'Nepal',
      'NL' => 'Netherlands',
      'NC' => 'New Caledonia',
      'NZ' => 'New Zealand',
      'NI' => 'Nicaragua',
      'NE' => 'Niger',
      'NG' => 'Nigeria',
      'NU' => 'Niue',
      'NF' => 'Norfolk Island',
      'MP' => 'Northern Mariana Islands',
      'NO' => 'Norway',
      'OM' => 'Oman',
      'PK' => 'Pakistan',
      'PW' => 'Palau',
      'PS' => 'Palestine, State of',
      'PA' => 'Panama',
      'PG' => 'Papua New Guinea',
      'PY' => 'Paraguay',
      'PE' => 'Peru',
      'PH' => 'Philippines',
      'PN' => 'Pitcairn',
      'PL' => 'Poland',
      'PT' => 'Portugal',
      'PR' => 'Puerto Rico',
      'QA' => 'Qatar',
      'RE' => 'Réunion',
      'RO' => 'Romania',
      'RU' => 'Russian Federation',
      'RW' => 'Rwanda',
      'BL' => 'Saint Barthélemy',
      'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
      'KN' => 'Saint Kitts and Nevis',
      'LC' => 'Saint Lucia',
      'MF' => 'Saint Martin (French part)',
      'PM' => 'Saint Pierre and Miquelon',
      'VC' => 'Saint Vincent and the Grenadines',
      'WS' => 'Samoa',
      'SM' => 'San Marino',
      'ST' => 'Sao Tome and Principe',
      'SA' => 'Saudi Arabia',
      'SN' => 'Senegal',
      'RS' => 'Serbia',
      'SC' => 'Seychelles',
      'SL' => 'Sierra Leone',
      'SG' => 'Singapore',
      'SX' => 'Sint Maarten (Dutch part)',
      'SK' => 'Slovakia',
      'SI' => 'Slovenia',
      'SB' => 'Solomon Islands',
      'SO' => 'Somalia',
      'ZA' => 'South Africa',
      'GS' => 'South Georgia and the South Sandwich Islands',
      'SS' => 'South Sudan',
      'ES' => 'Spain',
      'LK' => 'Sri Lanka',
      'SD' => 'Sudan',
      'SR' => 'Suriname',
      'SJ' => 'Svalbard and Jan Mayen',
      'SZ' => 'Swaziland',
      'SE' => 'Sweden',
      'CH' => 'Switzerland',
      'SY' => 'Syrian Arab Republic',
      'TW' => 'Taiwan, Province of China',
      'TJ' => 'Tajikistan',
      'TZ' => 'Tanzania, United Republic of',
      'TH' => 'Thailand',
      'TL' => 'Timor-Leste',
      'TG' => 'Togo',
      'TK' => 'Tokelau',
      'TO' => 'Tonga',
      'TT' => 'Trinidad and Tobago',
      'TN' => 'Tunisia',
      'TR' => 'Turkey',
      'TM' => 'Turkmenistan',
      'TC' => 'Turks and Caicos Islands',
      'TV' => 'Tuvalu',
      'UG' => 'Uganda',
      'UA' => 'Ukraine',
      'AE' => 'United Arab Emirates',
      'GB' => 'United Kingdom',
      'US' => 'United States',
      'UM' => 'United States Minor Outlying Islands',
      'UY' => 'Uruguay',
      'UZ' => 'Uzbekistan',
      'VU' => 'Vanuatu',
      'VE' => 'Venezuela, Bolivarian Republic of',
      'VN' => 'Viet Nam',
      'VG' => 'Virgin Islands, British',
      'VI' => 'Virgin Islands, U.S.',
      'WF' => 'Wallis and Futuna',
      'EH' => 'Western Sahara',
      'YE' => 'Yemen',
      'ZM' => 'Zambia',
      'ZW' => 'Zimbabwe',
    ];

    return $countries;
  }

  public function getCountryName($code) {
    $country = '';

    if ($code) {
      $countries = $this->getCountryList();
      $country = $countries[$code];
    }

    return $country;
  }

  public function getNameAbbr($user) {
    $abbr = 'A';
    if ($user instanceof \Drupal\user\Entity\User) {
      $firstName = $user->get('field_firstname')->value;
      $lastName = $user->get('field_lastname')->value;
      $displayName = $user->getDisplayName();
      if ($firstName) {
        $abbr = $firstName[0] . @$lastName[0];
      }
      else {
        $abbr = $displayName[0];
      }
    }
    return $abbr;
  }

  public function getImageUrl($target_id, $style = 'thumbnail') {
    $url = '';
    if ($target_id) {
      $imageObj = File::load($target_id);
      if ($imageObj instanceof \Drupal\file\Entity\File) {
        $url = ImageStyle::load($style)
          ->buildUrl($imageObj->getFileUri());
      }
    }
    return $url;
  }

  public function getUserBasicDetails($user) {

    $user_picture_obj = '';
    $user_name = 'Anonymous';
    $role = '';
    $user_id = 0;

    if ($user instanceof User) {
      $firstname = $user->get('field_firstname')->value;
      $lastname = $user->get('field_lastname')->value;
      $displayname = $user->getDisplayName();
      $role = $user->getRoles();
      $user_id = $user->id();

      // user picture
      $user_picture_id = $user->get('user_picture')->target_id;
      if ($user_picture_id) {
        $user_picture_obj = '<img src="' . \Drupal::service('crowdfundingproject.helper')
            ->getImageUrl($user_picture_id) . '" />';
      }
      else {
        $user_picture_obj = '<span>' . \Drupal::service('crowdfundingproject.helper')
            ->getNameAbbr($user) . '</span>';
      }
      if ($firstname) {
        $user_name = $firstname . ' ' . $lastname;
      }
      else {
        $user_name = $displayname;
      }
    }
    return [
      'image' => $user_picture_obj,
      'name' => $user_name,
      'role' => $role,
      'id' => $user_id
    ];
  }

}