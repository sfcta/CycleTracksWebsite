<?php

require_once('Database.php');
require_once('User.php');

class UserFactory
{
	static $class = 'User';

	public static function insert( $device )
	{
		$db = DatabaseConnectionFactory::getConnection();

		$query = "INSERT INTO user ( device ) VALUES ( '" .
				$db->escape_string( $device ) . "' )";

		if ( ( $db->query( $query ) === true ) &&
			 ( $id = $db->insert_id ) )
		{
			Util::log( __METHOD__ . "() created new user {$id} for device {$device}" );
			return self::getUser( $id );
		}
		else
			Util::log( __METHOD__ . "() ERROR failed to create new user for device {$device}" );

		return false;
	}

	public static function getUser( $id )
	{
		$db = DatabaseConnectionFactory::getConnection();
		$user = null;

		if ( ( $result = $db->query( "SELECT * FROM user WHERE id='" . $db->escape_string( $id ) . "'" ) ) &&
				( $result->num_rows ) )
		{
			$user = $result->fetch_object( self::$class );
			$result->close();
		}

		return $user;
	}

	public static function getUserByDevice( $device )
	{
		$db = DatabaseConnectionFactory::getConnection();
		$user = null;

		if ( ( $result = $db->query( "SELECT * FROM user WHERE device='" . $db->escape_string( $device ) . "'" ) ) &&
				( $result->num_rows ) )
		{
			$user = $result->fetch_object( self::$class );
			$result->close();
		}

		return $user;
	}

	/**
	* @desc update user record identified by $old with diffs in $new
	* @param User $old object instantiated from current DB record for user
	* @param User $new object instantiated from client data
	*/
	public static function update( User $old, User $new )
	{
		$db = DatabaseConnectionFactory::getConnection();

		$update = '';

		//$fields = (array) $old;
		foreach ( $new->getPersonalInfo() as $key => $value )
		{
			// only update values if non-null
			// NOTE: 0 is an allowed value for cycling_freq
			if ( !empty( $value ) || ( $key == 'cycling_freq' && is_numeric( $value ) ) )
			{
				Util::log( "updating {$key}\t=> '{$value}'" );
				if ( !empty( $update ) )
					$update .= ', ';

				$update .= "{$key}='" . $db->escape_string( $value ) . "'";
			}
		}

		// sanity check - ensure we have at least one field to update
		// and a valid user.id to work with
		if ( $update && isset( $old->id ) && $old->id )
		{
			// build update query
			$query = "UPDATE user SET {$update} WHERE id='" . $db->escape_string( $old->id ) . "' LIMIT 1";

			if ( $db->query( $query ) ) 
			{
				Util::log( __METHOD__ . "() updated user {$old->id}:" );
				Util::log( $query );
				return self::getUser( $old->id );
			}
			else
				Util::log( __METHOD__ . "() ERROR failed to update user {$old->id}" );
		}
		else
			Util::log( __METHOD__ . " nothing to do" );

		return false;
	}
}
