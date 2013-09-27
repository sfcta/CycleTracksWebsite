<?php

class User
{
	public $id;
	public $created;
	public $device;
	public $email;
	public $age;
	public $gender;
	public $homeZIP;
	public $schoolZIP;
	public $workZIP;
	public $cycling_freq;

	public function __construct( $object=null )
	{
		if ( is_object( $object ) )
		{
			// NOTE: trim string values to remove whitespace
			if ( isset( $object->email ) )
				$this->email        = trim( $object->email );

			if ( isset( $object->age ) )
				$this->age          = trim( $object->age );

			if ( isset( $object->gender ) )
				$this->gender       = trim( $object->gender );

			if ( isset( $object->homeZIP ) )
				$this->homeZIP      = trim( $object->homeZIP );

			if ( isset( $object->schoolZIP ) )
				$this->schoolZIP    = trim( $object->schoolZIP );

			if ( isset( $object->workZIP ) )
				$this->workZIP      = trim( $object->workZIP );
			
			/*
			if ( isset( $object->cycling_freq ) )
				$this->cycling_freq = $object->cycling_freq;
			*/
			if ( isset( $object->cyclingFreq ) )
				$this->cycling_freq = $object->cyclingFreq;
		}
	}

	/**
	* @desc return user-editable personal info fields
	* @return array of user-editable personal info fields as key / value pairs
	*/
	public function getPersonalInfo()
	{
		$info = array(
			'email'        => $this->email,
			'age'          => $this->age,
			'gender'       => $this->gender,
			'homeZIP'      => $this->homeZIP,
			'schoolZIP'    => $this->schoolZIP,
			'workZIP'      => $this->workZIP,
			'cycling_freq' => $this->cycling_freq,
		);

		return $info;
	}
}
