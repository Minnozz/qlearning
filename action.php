<?php
	class Action {
		private $label;
		private $outcomes = array();
		private $outcomeWeights = array();

		function __construct($label) {
			$this->label = $label;
		}

		public function getLabel() {
			return $this->label;
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
