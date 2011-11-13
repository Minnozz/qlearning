<?php
	class State {
		private $label;
		private $absorbing = false;
		private $reward;
		private $actions = array();

		function __construct($label, $reward = 0) {
			$this->label = $label;
			$this->reward = $reward;
		}

		public function getLabel() {
			return $this->label;
		}

		public function setAbsorbing($absorbing) {
			$this->absorbing = $absorbing;
		}

		public function isAbsorbing() {
			return $this->absorbing;
		}

		public function setReward($reward) {
			$this->reward = $reward;
		}

		public function getReward() {
			return $this->reward;
		}

		public function addAction(Action $action) {
			if(isset($this->actions[$action->getLabel()])) {
				throw new QLearningException('Action with label '. $action->getLabel() .' already exists for state '. $this->getLabel());
			}
			$this->actions[$action->getLabel()] = $action;
		}

		public function getAction($label) {
			if(!isset($this->actions[$label])) {
				throw new QLearningException('Action with label '. $label .' does not exist for state '. $this->getLabel());
			}
			return $this->actions[$label];
		}

		public function determineNextAction(Closure $explorationFunction = NULL) {
			$highestQValue = NULL;
			$bestActions = array();
			foreach($this->actions as $action) {
				if($explorationFunction === NULL) {
					$effectiveQValue = $action->getQValue();
				} else {
					$effectiveQValue = $explorationFunction($action);
				}
				if($highestQValue === NULL || $effectiveQValue >= $highestQValue) {
					if($effectiveQValue == $highestQValue) {
						$bestActions[] = $action;
					} else {
						$highestQValue = $effectiveQValue;
						$bestActions = array($action);
					}
				}
			}

			if(count($bestActions) > 0) {
				return $bestActions[array_rand($bestActions)];
			} else {
				return $bestActions[0];
			}
		}
	}
?>
