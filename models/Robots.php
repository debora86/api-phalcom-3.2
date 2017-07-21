<?php

//namespace Store\Toys;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Uniqueness;

class Robots extends Model {

	public $id;
	public $name;
	public $type;
	public $year;
	public $soft_delete;

	public function initialize() {
		$this->setSchema("example");
	}

	public function getSource() {

		return 'robots';
	}

//consulta de los robots
	public function getName() {

		return $this->name;
	}

	//public static function find($parameters = null) {
	//return parent::find($parameters);
	//}

	public function validation() {
		$validator = new Validation();

		$validator->add(
			"type",

			// Type must be: droid, mechanical or virtual

			new InclusionIn(
				[
					'domain' => [
						'droid',
						'mechanical',
						'virtual',
					],
				]
			)

		);

		$validator->add(
			'name',
			// Robot name must be unique

			new Uniqueness(
				[

					'message' => 'The robot name must be unique',
				]
			)

		);
		// Year cannot be less than zero
		if ($this->year < 0) {
			$this->appendMessage(
				new Message('The year cannot be less than zero')
			);
		}

		// Check if any messages have been produced
		if ($this->validationHasFailed() === true) {
			return false;
		}
	}
}