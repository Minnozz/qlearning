<?php
	class QLearning {
		private $states = array();
		private $initialState;
		private $currentState;
		private $learningRate;
		private $discountFactor;

		public function __construct($learningRate = 1, $discountFactor = 1) {
			$this->setLearningRate($learningRate);
			$this->setDiscountFactor($discountFactor);
		}

		public function addState(State $state) {
			if(isset($this->states[$state->getLabel()])) {
				throw new QLearningException('State with label '. $state->getLabel() .' already exists');
			}
			$this->states[$state->getLabel()] = $state;
		}

		public function getState($label) {
			if(!isset($this->states[$label])) {
				throw new QLearningException('State with label '. $label .' does not exist');
			}
			return $this->states[$label];
		}

		public function setInitialState(State $state) {
			$this->initialState = $state;
		}

		public function setLearningRate($learningRate) {
			$this->learningRate = $learningRate;
		}

		public function getLearningRate() {
			return $this->learningRate;
		}

		public function setDiscountFactor($discountFactor) {
			$this->discountFactor = $discountFactor;
		}

		public function getDiscountFactor() {
			return $this->discountFactor;
		}

		public function iterate() {
			// Start in initial state
			$currentState = $this->initialState;

			$nextAction = NULL;
			while(!$currentState->isAbsorbing()) {
				// Determine which action to perform
				if($nextAction === NULL) {
					// First action only
					$action = $currentState->determineBestAction();
				} else {
					// Action already determined in previous step
					$action = $nextAction;
				}
				$this->debug('Performing action '. $action->getLabel() .' in state '. $currentState->getLabel());

				// Perform action
				$nextState = $action->determineOutcome();

				// Determine next action (necessary for updating Q value)
				$nextAction = $nextState->determineBestAction();

				// Calculate new Q value for executed action
				$qValue = $currentState->getQValueForAction($action);
				$nextActionQValue = $nextState->getQValueForAction($nextAction);
				$this->debug('Next action will be '. $nextAction->getLabel() .' in state '. $nextState->getLabel() .' with Q value '. $nextActionQValue);
				$newQValue = $qValue + $this->learningRate * ($currentState->getReward() + $this->discountFactor * $nextActionQValue - $qValue);

				$this->debug('Updating Q value for aaction '. $action->getLabel() .' in state '. $currentState->getLabel() .' from '. $qValue .' to '. $newQValue);

				// Update Q value for the executed action
				$currentState->setQValueForAction($action, $newQValue);

				// Update current state
				$currentState = $nextState;
			}
		}

		private function debug($message) {
			print($message ."\n");
		}
	}

	class QLearningException extends Exception {
	}
?>
