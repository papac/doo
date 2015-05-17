<?php

/**
* PHP, une classe PHP simple. Dans le but simplifié l'utilisation de PDO
* @author Dakia Franck <dakiafranckinfo@gmail.com>
* @package Doo;
* @version 0.1.0
*/
namespace Doo;

/**
* Doo date maker
* Create date 05/05/2015
*/
/**
 * Class DooDateMaker
 *
 * @package Doo
 */
/**
 * Class DooDateMaker
 *
 * @package Doo
 */
class DooDateMaker
{

    /**
     * @var
     */
    private $timestanp;
    /**
     * @var string
     */
    private $local;

    /**
     * @var
     */
    private $seconds;
    /**
     * @var
     */
    private $minutes;
    /**
     * @var
     */
    private $hours;

    /**
     * @var
     */
    private $dayOfYear;
    /**
     * @var
     */
    private $dayOfWeek;
    /**
     * @var
     */
    private $dayOfMonth;
    /**
     * @var
     */
    private $monthOfYear;
    /**
     * @var
     */
    private $year;

    /**
     * @param null $timestanp
     * @param null $local
     */
    function __construct($timestanp = null, $local = null)
    {

        if($timestanp !== null)
        {

            if(is_string($timestanp))
            {

                $this->local = $timestanp;
                $timestanp = $this->local !== "ci_CI" ? time() : (time() - 7200);

            }
            else
            {

                if($local !== null)
                {
                    $this->local = $local;
                    $timestanp = $local === "ci_CI" ? $timestanp - 7200: $timestanp;
                }

            }

        }
        else
        {
            $this->local = "fr_FR";
            $timestanp = time() - 7200;
        }

        $initDate = \getdate($timestanp);

        $this->seconds = $initDate['seconds'];
        $this->minutes = $initDate['minutes'];
        $this->hours = $initDate['hours'];

        $this->dayOfWeek = $initDate['wday'];
        $this->dayOfYear = $initDate['yday'];
        $this->dayOfMonth = $initDate["mday"];
        $this->year = $initDate['year'];
        $this->monthOfYear = $initDate['mon'];

        $this->timestanp = $initDate[0];

    }

    /**
    * @param string, redefinir la local
    */
    public function setLocal($local)
    {

        if(!is_string($local))
        {
            $this->local = $local;
        }

    }

    /**
    * getDayName, fonction permetant de recuperer le nom litteral du jour.
    *
    * @param function, fonction de rappel
    * @return string
    */
    public function getDayName($cb = null)
    {
        $day = [

            "fr_FR" => [
                "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"
            ],

            "es_ES" => [
                "", "", "", "", "", "", ""
            ]

        ];

        if($this->local === 'fr_FR' || $this->local === 'es_ES' || $this->local === 'ci_CI')
        {

            if($this->local === 'ci_CI')
            {

                $this->local = "fr_FR";

            }

            $dayName = $day[$this->local][$this->dayOfWeek];

        }
        elseif($this->local === 'en_EN')
        {

            $dayName = \getdate($this->timestanp)["weekday"];

        }

        if($cb !== null)
        {

            call_user_func($cb, $dayName);

        }

        return $dayName;
    }

    /**
    * getDayOfMonth, fonction permetant d'obtenir le numero du jour du mois
    * @param function, fonction de rappel facultatif
    * =================== e.g ================================
    * $date = new DooDateMaker();
    * echo $date->getDayOfMonth([function]);
    * // or
    * $date->getDayOfMonth(function($dayOfMonth)
    * {
    *   echo $dateOfMonth;
    * });
    *
    */
    public function getDayOfMonth($cb = null)
    {

        if($cb !== null)
        {

            call_user_func($cb, $this->dayOfWeek);

        }

        return $this->dayOfWeek;

    }

    /**
     * @param null $cb
     * @return mixed
     */
    public function getYear($cb = null)
    {

        if($cb !== null)
        {

            call_user_func($cb, $this->year);

        }

        return $this->year;

    }

    /**
     * @param null $cb
     * @return mixed
     */
    public function getMonthOfYear($cb = null)
    {

        if($cb !== null)
        {

            call_user_func($cb ,$this->monthOfYear);

        }

        return $this->monthOfYear;

    }
    /**
    * getMonthName, fonction permettant de recuperer le nom d'un mois
    * @param function, une fonction de rappel
    * @return string, le nom du mois
    */
    public function getMonthName($cb = null)
    {

        $month = [

            "fr_FR" => [
                "Janvier", "Fevrier", "Mars",
                "Avril", "Mai", "Juin",
                "Juillet", "Aout", "Sptembre",
                "Octobre", "Novembre", "Decembre"
            ],

            "es_ES" => [
                "Lunes", "Martes", "Miercoles",
                "MonthAtEs", "MonthAtEs", "MonthAtEs",
                "MonthAtEs", "MonthAtEs", "MonthAtEs",
                "MonthAtEs", "MonthAtEs", "MonthAtEs"
                ]

        ];

        if($this->local === 'fr_FR' || $this->local === 'es_ES' || $this->local === 'ci_CI')
        {

            if($this->local === 'ci_CI')
            {

                $this->local = 'fr_FR';

            }

            $monthName = $month[$this->local][$this->monthOfYear - 1];

        }
        elseif($this->local === 'en_EN')
        {

            $monthName = getdate($this->timestanp)["month"];

        }

        return $monthName;

    }

    /**
    * format, fonction permettant de format des dates en fonction des besion
    * @param string, formatage e.g: Y-m-d H:m:s
    * @param fonction, fonction de rappel pouvant recuperer le resultat
    * @return string, representant le date format
    */
    public function format($format, $cb = null)
    {

        $date = \date($format);

        if($cb !== null)
        {

            call_user_func($cb, $date);

        }

        return $date;

    }


    /**
    * addDay, fonction permetant d'ajouter des jours
    * @param int, un timestanp
    * @return DooDateMaker, avec l'ajout des jours
    */
    public function addDay($dayNumber)
    {

        return new DooDateMaker($this->timestanp + ($dayNumber * 24 * 3600), $this->local);

    }

    /**
    * addMonth, fonction permetant d'ajouter des mois
    *
    * @param int $monthNumber, un timestamp
    * @param null $cb, un callable
    * @return DooDateMaker, avec l'ajout des jours
    */
    public function addMonth($monthNumber, $cb = null)
    {
        if($monthNumber < 0)
        {

            if($cb !== null)
            {

                call_user_func($cb, new \Exception("Error, verify offset => not accepted negative offset"));
                return null;

            }
            else
            {

                trigger_error("Error, verify offset => not accepted negative offset", E_USER_WARNING);

            }


        }

        $month = [
            31, 28, 31,
            30, 31, 30,
            31, 31, 30,
            31, 30, 31
        ];

        $somOfDay = 0;

        $i = $this->monthOfYear;

        while($monthNumber--)
        {

            if($i > 11){
                $i = 0;
            }

            $somOfDay += $month[$i];
            $i++;

        }

        $r = $somOfDay + ($month[$this->monthOfYear - 1] - $this->dayOfMonth);

        return new DooDateMaker(time() + ($r * 24 * 3600), $this->local);

    }

    /**
    * seconds, fonction permetant de recuperer les seconds
    *
    * @return int, nombre de seconds
    */
    public function seconds()
    {

        return $this->seconds;

    }


    /**
    * hours, fonction permetant de recuperer les heures
    *
    * @return int, nombre de heures
    */
    public function hours()
    {

        return $this->hours;

    }

    /**
    * minutes, fonction permetant de recuperer les minutes
    *
    * @return int, nombre de minute
    */
    public function minutes()
    {

        return $this->minutes;

    }

}
