<?php
	class State {
		private $label;
		private $absorbing = false;
		private $reward;
		private $actions = array();
		private $qValues = array();

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

		public function addAction(Action $action, $initialQValue = 0) {
			if(isset($this->actions[$action->getLabel()])) {
				throw new QLearningException('Action with label '. $action->getLabel() .' already exists for state '. $this->getLabel());
			}
			$this->actions[$action->getLabel()] = $action;
			$this->setQValueForAction($action, $initialQValue);
		}

		public function getAction($label) {
			if(!isset($this->actions[$label])) {
				throw new QLearningException('Action with label '. $label .' does not exist for state '. $this->getLabel());
			}
			return $this->actions[$label];
		}

		public function setQValueForAction(Action $action, $qValue) {
			// TODO: Store Q values in Action objects
			$this->qValues[$action->getLabel()] = $qValue;
		}

		public function getQValueForAction(Action $action) {
			// TODO: Store Q values in Action objects
			return $this->qValues[$action->getLabel()];
		}

		public function determineBestAction() {
			// TODO: Exploration (don't always choose the highest Q value)

			$highestQValue = max($this->qValues);
			$bestActions = array_keys($this->qValues, $highestQValue);

			if(count($bestActions) > 0) {
				$bestAction = $bestActions[array_rand($bestActions)];
			} else {
				$bestAction = $bestActions[0];
			}

			return $this->actions[$bestAction];
		}
	}
?>
