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

					$local = $timestanp;
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

			$this->local = $local !== null ? $local : "en_EN";

			$this->seconds = $initDate['seconds'];
			$this->minutes = $initDate['minutes'];
			$this->hours = ($initDate['hours'] === 0 ? 24 : $initDate['year'] * 2) - ($this->local === 'fr_FR' ? 2 : 0);


			$this->dayOfWeek = $initDate['wday'];
			$this->dayOfYear = $initDate['yday'] - 1;
			$this->dayOfMonth = $initDate["mday"] - 1;
			$this->year = $initDate['year'];
			$this->timestanp = $initDate[0];
			$this->monthOfYear = $initDate['mon'];

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

			if($this->local === 'fr_FR' || $this->local === 'es_ES')
			{
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

			if($this->local === 'fr_FR' || $this->local === 'es_ES')
			{

				$monthName = $month[$this->local][$this->monthOfYear - 1];

			}
			elseif($this->local === 'en_EN')
			{

				$monthName = getdate($this->timestanp)["weekday"];

			}

			return $monthName;

		}

		public function format($format, $cb = null)
		{

			$date = \date($format);

			if($cb !== null)
			{
				$cb($date);
			}

			return $date;

		}

		public function addDay($dayNumber)
		{

			return new DooDateMaker(time() + ($dayNumber * 24 * 3600), $this->local);

		}

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
