<?php

  namespace Doo;

	/**
	* Doo date maker
	* Create date
	*/
	class DooDateMaker
	{

		private $timestanp;
    private $local;

		private $seconds;
		private $minutes;
		private $hours;

		private $dayOfYear;
		private $dayOfWeek;
		private $dayOfMonth;
		private $monthOfYear;
		private $year;

		function __construct($timestanp = null, $local = null)
		{

			if($timestanp !== null)
			{

				if(is_string($timestanp))
				{

					$this->local = $timestanp;
					$initDate = \getdate();

				}
				else
				{

					$initDate = \getdate($timestanp);

				}

			}
			else
			{

				$initDate = \getdate();

			}

			$this->timestanp = $initDate[0];

			if($this->local === 'fr_FR')
			{

				$this->seconds = $initDate['seconds'];
				$this->minutes = $initDate['minutes'];
				$this->hours = $initDate['hours'];

				$this->dayOfWeek = $initDate['wday'];
				$this->dayOfYear = $initDate['yday'];
				$this->dayOfMonth = $initDate["mday"];
				$this->year = $initDate['year'];
				$this->monthOfYear = $initDate['mon'];

			}

			elseif($this->local === 'es_ES')
			{

				$this->seconds = $initDate['seconds'];
				$this->minutes = $initDate['minutes'];
				$this->hours = $initDate['hours'];

				$this->dayOfWeek = $initDate['wday'];
				$this->dayOfYear = $initDate['yday'];
				$this->dayOfMonth = $initDate["mday"];
				$this->year = $initDate['year'];
				$this->timestanp = $initDate[0];
				$this->monthOfYear = $initDate['mon'];

			}

			elseif($this->local === "en_EN")
			{

				$this->seconds = $initDate['seconds'];
				$this->minutes = $initDate['minutes'];
				$this->hours = $initDate['hours'];

				$this->dayOfWeek = $initDate['wday'];
				$this->dayOfYear = $initDate['yday'];
				$this->dayOfMonth = $initDate["mday"];
				$this->year = $initDate['year'];
				$this->monthOfYear = $initDate['mon'];

			}

			elseif($this->local === "ci_CI")
			{

				$this->dayOfWeek = $initDate['wday'];
				$this->dayOfYear = $initDate['yday'];

				$tmp = date_create('Africa/Abidjan');
				$tmp = (array) $tmp;
				$tmp = $tmp["date"];


				$part = explode(' ', $tmp);
				$partTime = explode(':', $part[1]);
				$partDate = explode('-', $part[0]);

				$initDate = [
					"seconds" => (int) $partTime[2],
					"minutes" => (int) $partTime[1],
					"hours" => (int) $partTime[0],
					"mday" => (int) $partDate[2],
					"mon" => (int) $partDate[1],
					"year" => (int) $partDate[0]
				];

				$this->seconds = $initDate['seconds'];
				$this->minutes = $initDate['minutes'];
				$this->hours = $initDate['hours'];

				$this->dayOfMonth = $initDate["mday"];
				$this->year = $initDate['year'];
				$this->monthOfYear = $initDate['mon'];

			}

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
		* getDayName, fonction permetant de recuperer le nom litteral du jour
		* @return string
		*/
		public function getDayName()
		{
			$day = [

				"fr_FR" => [
					"Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"
				],

				"es_ES" => [
					"", "", "",
					"", "", "",
					"", "", "",
					"", "", ""
				]

			];

			if($this->local === 'fr_FR' || $this->local === 'es_ES' || $this->local === 'ci_CI')
			{

				if($this->local === 'ci_CI')
				{

						$this->local = "fr_FR";

				}

				$dayName = $day[$this->local][$this->dayOfWeek - 1];
			}
			elseif($this->local === 'en_EN')
			{
				$dayName = getdate($this->timestanp)["wday"];
			}

			return $dayName;
		}

		public function getDayOfMonth($cb = null)
		{

			if($cb !== null)
			{
				$cb($this->dayOfWeek);
			}

			return $this->dayOfWeek;
		}

		public function getYear($cb = null)
		{

			if($cb !== null)
			{
				$cb($this->year);
			}

			return $this->year;
		}

		public function getMonthOfYear($cb = null)
		{

			if($cb !== null)
			{
				$cb($this->monthOfYear);
			}

			return $this->monthOfYear;

		}

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
					"MonthAtEs", "MonthAtEs", "MonthAtEs",
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

				$monthName = getdate($this->timestanp)["weekday"];

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
				$cb($date);
			}

			return $date;

		}


		/**
		*	addDay, fonction permetant d'ajouter des jours
		* @param int, un timestanp
		* @return DooDateMaker, avec l'ajout des jours
		*/
		public function addDay($dayNumber)
		{

			return new DooDateMaker(time() + ($dayNumber * 24 * 3600), $this->local);

		}

		/**
		*	addMonth, fonction permetant d'ajouter des mois
		* @param int, un timestanp
		* @return DooDateMaker, avec l'ajout des jours
		*/
		public function addMonth($monthNumbre)
		{

			$month = [
				"Janvier" => 31,
				"Fervrier" => 28,
				"Avril" => 31
			];

			return new DooDateMaker(time() + ($monthNumbre * 31 * 24 * 3600), $this->local);

		}

		/**
		* seconds, fonction permetant de recuperer les seconds
		* @return int, nombre de seconds
		*/
		public function seconds()
		{

			return $this->seconds;

		}


		/**
		* hours, fonction permetant de recuperer les heures
		* @return int, nombre de heures
		*/
		public function hours()
		{

			return $this->hours;

		}

		/**
		* minutes, fonction permetant de recuperer les minutes
		* @return int, nombre de minute
		*/
		public function minutes()
		{

			return $this->minutes;

		}

	}

 ?>
