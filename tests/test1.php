<?php
	require_once(__DIR__ .'/../includes.php');

	define('WIDTH', 7);
	define('HEIGHT', 4);
	define('ITERATIONS', 1000);

	$obstacles = array(
		label(2, 2),
		label(4, 1),
		label(3, 3),
	);

	$q = new QLearning();
	$q->setLearningRate(0.3);
	$q->setDiscountFactor(0.95);
	$q->setExplorationFunction(function(Action $action) {
		return $action->getQValue() + max(0, 50 - $action->getVisits());
	});

	// Create states
	for($y = 1; $y <= HEIGHT; $y++) {
		for($x = 1; $x <= WIDTH; $x++) {
			$label = label($x, $y);

			if(!in_array($label, $obstacles)) {
				$state = new State($label);
				$q->addState($state);
			}
		}
	}

	$q->setInitialState($q->getState(label(1, 3)));

	$q->getState(label(6, 1))->setAbsorbing(true);
	$q->getState(label(6, 1))->setReward(99);

	$q->getState(label(6, 3))->setAbsorbing(true);
	$q->getState(label(6, 3))->setReward(-99);

	// Set possible actions for all states
	for($y = 1; $y <= HEIGHT; $y++) {
		for($x = 1; $x <= WIDTH; $x++) {
			$label = label($x, $y);
			if(in_array($label, $obstacles)) {
				continue;
			}
			$state = $q->getState($label);

			if($state->isAbsorbing()) {
				$action = new Action('Finish', $state->getReward());
				$action->addOutcome($state);
				$state->addAction($action);
			} else {
				$north = label($x, ($y > 1) ? $y - 1 : $y);
				$north = in_array($north, $obstacles) ? $state : $q->getState($north);

				$east = label(($x < WIDTH) ? $x + 1 : $x, $y);
				$east = in_array($east, $obstacles) ? $state : $q->getState($east);

				$south = label($x, ($y < HEIGHT) ? $y + 1 : $y);
				$south = in_array($south, $obstacles) ? $state : $q->getState($south);

				$west = label(($x > 1) ? $x - 1 : $x, $y);
				$west = in_array($west, $obstacles) ? $state : $q->getState($west);

				$action = new Action('N');
				$action->addOutcome($west, 1);
				$action->addOutcome($north, 8);
				$action->addOutcome($east, 1);
				$state->addAction($action);

				$action = new Action('E');
				$action->addOutcome($north, 1);
				$action->addOutcome($east, 8);
				$action->addOutcome($south, 1);
				$state->addAction($action);

				$action = new Action('S');
				$action->addOutcome($east, 1);
				$action->addOutcome($south, 8);
				$action->addOutcome($west, 1);
				$state->addAction($action);

				$action = new Action('W');
				$action->addOutcome($south, 1);
				$action->addOutcome($west, 8);
				$action->addOutcome($north, 1);
				$state->addAction($action);
			}
		}
	}

	// Main loop
	$i = 0;
	while($i++ < 1000) {
		// Iterate the learning algorithm
		print("Iteration ". $i ."\n");
		$q->iterate();

		// Draw the state space with Q values
		for($y = 1; $y <= HEIGHT; $y++) {
			$lines = array();

			for($x = 1; $x <= WIDTH; $x++) {
				$label = label($x, $y);

				if(in_array($label, $obstacles)) {
					$lines[0][] = sprintf('+-----------+');
					$lines[1][] = sprintf('| xxxxxxxxx |');
					$lines[2][] = sprintf('| xxxxxxxxx |');
					$lines[3][] = sprintf('| xxxxxxxxx |');
					$lines[4][] = sprintf('+-----------+');
				} else {
					$state = $q->getState($label);

					if($state->isAbsorbing()) {
						$lines[0][] = sprintf('+-----------+');
						$lines[1][] = sprintf('|           |');
						$lines[2][] = sprintf('|    % 3d    |', $state->getAction('Finish')->getQValue());
						$lines[3][] = sprintf('|           |');
						$lines[4][] = sprintf('+-----------+');
					} else {
						$lines[0][] = sprintf('+-----------+');
						$lines[1][] = sprintf('|    % 3d    |', $state->getAction('N')->getQValue());
						$lines[2][] = sprintf('| % 3d   % 3d |', $state->getAction('W')->getQValue(), $state->getAction('E')->getQValue());
						$lines[3][] = sprintf('|    % 3d    |', $state->getAction('S')->getQValue());
						$lines[4][] = sprintf('+-----------+');
					}
				}
			}

			foreach($lines as $parts) {
				print(implode(' ', $parts) ."\n");
			}
			print("\n");
		}
		print("\n");

		usleep(10000);
	}

	// Draw the policy
	print("Policy:\n");
	for($y = 1; $y <= HEIGHT; $y++) {
		$lines = array();

		for($x = 1; $x <= WIDTH; $x++) {
			$label = label($x, $y);

			if(in_array($label, $obstacles)) {
				$lines[0][] = sprintf('+-----------+');
				$lines[1][] = sprintf('| xxxxxxxxx |');
				$lines[2][] = sprintf('| xxxxxxxxx |');
				$lines[3][] = sprintf('| xxxxxxxxx |');
				$lines[4][] = sprintf('+-----------+');
			} else {
				$state = $q->getState($label);

				if($state->isAbsorbing()) {
					$lines[0][] = sprintf('+-----------+');
					$lines[1][] = sprintf('|           |');
					$lines[2][] = sprintf('|   Finish  |');
					$lines[3][] = sprintf('|           |');
					$lines[4][] = sprintf('+-----------+');
				} else {
					$lines[0][] = sprintf('+-----------+');
					$lines[1][] = sprintf('|           |');
					$lines[2][] = sprintf('|     %s     |', $state->determineNextAction()->getLabel());
					$lines[3][] = sprintf('|           |');
					$lines[4][] = sprintf('+-----------+');
				}
			}
		}

		foreach($lines as $parts) {
			print(implode(' ', $parts) ."\n");
		}
	}

	function label($x, $y) {
		return '('. $x .','. $y .')';
	}
?>
