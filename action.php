<?php
	class Action {
		private $label;
		private $outcomes = array();
		private $outcomeWeights = array();
		private $qValue;
		private $visits = 0;

		function __construct($label, $qValue = 0) {
			$this->label = $label;
			$this->qValue = $qValue;
		}

		public function getLabel() {
			return $this->label;
		}

		public function setQValue($qValue) {
			$this->qValue = $qValue;
		}

		public function getQValue() {
			return $this->qValue;
		}

		public function addVisit() {
			$this->visits++;
		}

		public function getVisits() {
			return $this->visits;
		}

		public function addOutcome(State $state, $weight = 1) {
			if(!is_int($weight) || $weight < 0) {
				throw new QLearningException('Invalid weight for outcome: '. $weight);
			}

			$this->outcomes[] = $state;
			$this->outcomeWeights[] = $weight;
		}

		public function determineOutcome() {
			$rand = mt_rand(1, array_sum($this->outcomeWeights));
			$n = 0;
			foreach($this->outcomes as $i => $outcome) {
				if($this->outcomeWeights[$i] > 0) {
					$n += $this->outcomeWeights[$i];
					if($n >= $rand) {
						return $outcome;
					}
				}
			}
			throw new QLearningException('No valid outcome found');
		}
	}
?>
